<?php
use TeamForm as FormTeam;
use DeleteForm as FormDelete;
use \Phalcon\Paginator\Adapter\Model as Paginacion;

class TeamsController extends ControllerBase
{

    public function indexAction() {
        $paginator = new Paginacion(
            array(
                //obtenemos los productos
                "data" => Teams::find(array(
                            "order" => "name"
                        )
                    ),
                //limite por página
                "limit"=> 10,
                //variable get page convertida en un integer
                "page" => $this->request->getQuery('page', 'int')
            )
        );

        //pasamos el objeto a la vista con el nombre de $page
        $this->view->page = $paginator->getPaginate();

        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');
        $this->addBreadcrumb('Equipos','teams');

        $user_id = $this->session->get("userId");
        $user = Users::findFirst($user_id);

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Equipos',
            'v_session' => $has_user,
            'breadcrumb'=>$this->createHtmlBreadcrumb(),
            'role'      =>  $user->role,
            'user_id'   =>  $user->id
        ));
    }

    public function addAction(){
        $request = $this->request;
        $user_id = $this->session->get("userId");
        if($request->isPost())
        {
            $form = new FormTeam();
            if($form->isValid($request->getPost())!= false)
            {
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:m:s');

                $team = new Teams();
                $team->leader_id = $user_id;
                $team->name = $request->get('name');
                $team->description = $request->get('description');
                $team->created = $today;
                $team->modified = $today;
                $team->status = 1;
                if (!$team->save()) {
                    foreach ($form->getMessages() as $message)
                    {
                        $this->flash->error($message);
                    }
                }else{
                    // Almacenar la relacion usuarios y equipos
                    $last_id = $team->id;

                    foreach ($request->get('users_ids') as $user) {
                        $userteam = new UsersTeams();
                        $userteam->user_id = $user;
                        $userteam->team_id = $last_id;
                        $userteam->save();

                        // Para cada usuario se debe crear una notificacion
                        $this->sdt->createNotification($user,$user_id,"newTeam",$last_id,$today);
                    }
                    $this->flash->success('El equipo de trabajo ha sido creado');

                }
                return $this->dispatcher->forward(array("action" => "index"));

            }
            else
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message);
                }
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');
        $this->addBreadcrumb('Equipos','teams');
        $this->addBreadcrumb('Crear Equipo','teams/add');

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Crear Equipo de Trabajo',
            'v_session' => $has_user,
            'form'      => new FormTeam(),
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));

        $this->assets
            ->addCss('css/multi_select/bootstrap-multiselect.css');

        $this->assets
            ->addJs('js/multi_select/bootstrap-multiselect.js')
            ->addJs('js/multi_select/init.js');
    }

    public function editAction($id){
        $request = $this->request;
        $user_id = $this->session->get("userId");
        if($request->isPost())
        {
            $form = new FormTeam();
            if($form->isValid($request->getPost())!= false)
            {
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:m:s');

                $team = Teams::findFirst($request->get('id'));
                $team->leader_id = $user_id;
                $team->name = $request->get('name');
                $team->description = $request->get('description');
                $team->created = $today;
                $team->modified = $today;
                $team->status = 1;
                if (!$team->save()) {
                    foreach ($form->getMessages() as $message)
                    {
                        $this->flash->error($message);
                    }
                }else{
                    // Obtener relaciones anteriores
                    $userTeams = UsersTeams::findByTeamId($request->get('id'));
                    $usersA = array();
                    foreach ($userTeams as $userTeam) {
                        $usersA[$userTeam->user_id]=1;
                    }

                    // Almacenar la relacion usuarios y equipos
                    foreach ($request->get('users_ids') as $user) {
                        if(!isset($usersA[$user])){
                            $usersA[$user]=2;
                            $userteam = new UsersTeams();
                            $userteam->user_id = $user;
                            $userteam->team_id = $request->get('id');
                            $userteam->save();
                            // Para cada usuario se debe crear una notificacion
                            $this->sdt->createNotification($user,$user_id,"newTeam",$request->get('id'),$today);
                        }else{
                            $this->sdt->createNotification($user,$user_id,"editTeam",$request->get('id'),$today);
                        }
                        $usersA[$user]+=1;
                    }
                    // 1: fueron eliminados de sacados del equipo
                    // 2: Permanecen en el equipo
                    // 3: son usuarios nuevos
                    $arrkeys = array_keys($usersA);
                    foreach ($arrkeys as $arrkey) {
                        if($usersA[$arrkey]==1){
                            // eliminar relacion
                            $userTeams = UsersTeams::findFirst('user_id = '.$arrkey.' AND team_id = '.$request->get('id'));
                            $userTeams->delete();
                            $this->sdt->createNotification($arrkey,$user_id,"deleteTeam",$request->get('id'),$today);
                        }
                    }
                    $this->flash->success('El equipo de trabajo ha sido editado');

                }
                return $this->dispatcher->forward(array("action" => "index"));

            }
            else
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message);
                }
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');
        $this->addBreadcrumb('Equipos','teams');
        $this->addBreadcrumb('Editar Equipo','teams/edit');

        $team = Teams::findFirst($id);

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>  'Editar Equipo de Trabajo',
            'v_session' =>  $has_user,
            'form'      =>  new FormTeam($team),
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'id'        =>  $id
        ));

        // Seleccionar los usuarios que estan en el equipo

        $usersTeam = UsersTeams::findByTeamId($id);
        $arr = array();
        foreach ($usersTeam as $item){
            array_push($arr, $item->user_id);
        }
        $this->tag->setDefaults(array("users_ids[]" => $arr));

        $this->assets
            ->addCss('css/multi_select/bootstrap-multiselect.css');

        $this->assets
            ->addJs('js/multi_select/bootstrap-multiselect.js')
            ->addJs('js/multi_select/init.js');
    }

    public function viewAction($id){

        $phql = 'SELECT
			Users.id, Users.status, IFNULL( CONCAT(Profiles.name," ", Profiles.last_name), Users.username) as username
			FROM Users
			INNER JOIN UsersTeams ON Users.id = UsersTeams.user_id
			LEFT JOIN Profiles ON Profiles.user_id = Users.id
			WHERE
			UsersTeams.team_id = '.$id.'
			ORDER BY username ASC ';
        $results = $this->modelsManager->executeQuery($phql);


        $paginator = new Paginacion(
            array(
                //obtenemos los productos
                "data" =>  $results,
                //limite por página
                "limit"=> 10,
                //variable get page convertida en un integer
                "page" => $this->request->getQuery('page', 'int')
            )
        );

        //pasamos el objeto a la vista con el nombre de $page
        $this->view->page = $paginator->getPaginate();

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');
        $this->addBreadcrumb('Equipos','teams');
        $this->addBreadcrumb('Ver Equipo','teams/edit');

        $team = Teams::findFirst($id);

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>  'Ver Equipo de Trabajo',
            'v_session' =>  $has_user,
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'id'        =>  $id,
            'name'      =>  $team->name
        ));
    }

    public function deleteAction ($team_id=0){
        $user_id = $this->session->get("userId");
        $team = Teams::findFirst($team_id);
        if($team->leader_id==$user_id){
            $request = $this->request;
            if($request->isPost()){
                // eliminar todos los proyectos
                $projects = Projects::find('team_id = '.$team_id);
                foreach ($projects as $project) {
                    $this->deleteProject($project->id);
                }
                $packages = Packages::find('team_id = '.$team_id);
                foreach ($packages as $package) {
                    $this->deletePackage($package->id);
                }
                $usersteams = UsersTeams::find('team_id = ' .$team_id);
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:m:s');
                foreach ($usersteams as $userteam) {
                    $userteam->status=0;
                    $userteam->save();

                    $this->sdt->createNotification($userteam->user_id,$user_id,"deleteTeam",$userteam->id,$today);
                }
            }
            // User el Breadcrumbs de bootstrap
            $this->addBreadcrumb('[ HTD','htd');
            $this->addBreadcrumb('Checklist','checklist');
            $this->addBreadcrumb('RM ]','rmregistries');
            $this->addBreadcrumb('Equipos','teams');
            $this->addBreadcrumb('Eliminar Equipo','teams/view/'.$team_id);

            $has_user = $this->session->has("userId");
            if ($has_user){
                $this->createAlerts();
                $this->createNotifications();
                $this->createCustomization();
            }
            $this->view->setVars(array(
                'title_view'    =>  'Eliminar Equipo',
                'v_session'     =>  $has_user,
                'form'          =>  new FormDelete($team),
                'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
                'team_id'       =>  $team_id
            ));
        }else{
            return $this->response->redirect('index/index');
        }
    }
}

