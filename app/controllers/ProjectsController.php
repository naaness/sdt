<?php
use ProjectForm as FormProject;
use DeleteForm as FormDelete;
use \Phalcon\Paginator\Adapter\Model as Paginacion;
class ProjectsController extends ControllerBase
{

    public function indexAction($team_id=0)
    {
//        $paginator = new Paginacion(
//            array(
//                "data" => Projects::findByTeamId(array(
//                            "team_id = '".$team_id."'",
//                            "order" => "name"
//                        )
//                    ),
//                "limit"=> 10,
//                //variable get page convertida en un integer
//                "page" => $this->request->getQuery('page', 'int')
//            )
//        );

        $paginator = new Paginacion(
            array(
                "data" => Projects::find(array(
                            "team_id = '".$team_id."' AND status = 1",
                            "order" => "name"
                        )
                    ),
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
        $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
        $this->addBreadcrumb('Proyectos','projects/index/'.$team_id);

        $user_id = $this->session->get("userId");

        $team = Teams::findFirst($team_id);
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }

        $canCreate = false;
        $user = UsersTeams::findFirst('user_id = '.$user_id.' AND team_id = '.$team_id);
        if($user){
            $canCreate=true;
        }

        $this->view->setVars(array(
            'title_view'    =>  'Proyectos',
            'v_session'     =>  $has_user,
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'team'          =>  $team,
            'team_id'       =>  $team_id,
            'user_id'       =>  $user_id,
            'canCreate'     =>  $canCreate
        ));

    }

    public function addAction($team_id=0){
        $request = $this->request;
        $user_id = $this->session->get("userId");
        $user = UsersTeams::findFirst('user_id = '.$user_id.' AND team_id = '.$team_id);
        if(!$user){
            $this->flash->error('No puedes realizar esta accion');
            return $this->dispatcher->forward(array("action" => "index"));
        }
        if($request->isPost())
        {
            $form = new FormProject(null, $team_id);
            if($form->isValid($request->getPost())!= false)
            {
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:m:s');

                $project = new Projects();
                $project->team_id = $team_id;
                $project->user_id = $user_id;
                $project->name = $request->get('name');
                $project->code = strtoupper($request->get('code'));
                $project->description = $request->get('description');
                $project->created = $today;
                $project->modified = $today;
                $project->status = 1;
                if (!$project->validation()) {
                    $this->createListError($project);
                }else{
                    if ($project->save()) {
                        // Almacenar la relacion usuarios y equipos
                        $last_id = $project->id;

                        foreach ($request->get('users_ids') as $user) {
                            $userproject = new UsersProjects();
                            $userproject->user_id = $user;
                            $userproject->project_id = $last_id;
                            $userproject->save();

                            // Para cada usuario se debe crear una notificacion
                            $this->sdt->createNotification($user,$user_id,"newProject",$last_id,$today);
                        }
                        $this->flash->success('El proyecto ha sido creado');
                    }
                }
                return $this->dispatcher->forward(array("action" => "index"));
            }
            else
            {
                $this->createListError($form);
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');
        $this->addBreadcrumb('Equipos','teams');
        $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
        $this->addBreadcrumb('Proyectos','projects/index/'.$team_id);
        $this->addBreadcrumb('Crear Proyecto','projects/add/'.$team_id);
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Crear Equipo de Trabajo',
            'v_session' => $has_user,
            'form'      => new FormProject(null,$team_id),
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'team_id'   => $team_id
        ));

        $this->assets
            ->addCss('css/multi_select/bootstrap-multiselect.css');

        $this->assets
            ->addJs('js/multi_select/bootstrap-multiselect.js')
            ->addJs('js/multi_select/init.js');
    }

    public function editAction($team_id=0,$project_id=0){
        $request = $this->request;
        $user_id = $this->session->get("userId");
        // Solo podra acceder el usuario dueño del proyecto
        $project = Projects::findFirst($project_id);
        if ($project->user_id!=$user_id){
            return $this->response->redirect('Projects/index/'.$team_id);
        }
        if($request->isPost())
        {
            $form = new FormProject(null, $team_id);
            if($form->isValid($request->getPost())!= false)
            {
                $codeValid = true;
                if ($project->code != strtoupper($request->get('code'))){
                    $project->code = strtoupper($request->get('code'));
                    if (!$project->validationCode()) {
                        $codeValid=false;
                        $this->createListError($project);
                    }
                }
                if($codeValid){
                    $fecha_hoy = new \DateTime('America/Mexico_City');
                    $today = $fecha_hoy->format('Y-m-d H:m:s');

                    $project->team_id = $team_id;
                    $project->user_id = $request->get('user');
                    $project->name = $request->get('name');

                    $project->description = $request->get('description');
                    $project->modified = $today;

                    if ($project->save()) {
                        // Obtener relaciones anteriores
                        $userProjects = UsersProjects::findByProjectId($request->get('id'));
                        $usersA = array();
                        foreach ($userProjects as $userProject) {
                            $usersA[$userProject->user_id]=1;
                        }

                        // Almacenar la relacion usuarios y equipos
                        foreach ($request->get('users_ids') as $user) {
                            if(!isset($usersA[$user])){
                                $usersA[$user]=2;
                                $userproject = new UsersProjects();
                                $userproject->user_id = $user;
                                $userproject->project_id = $request->get('id');
                                $userproject->save();
                                $this->sdt->createNotification($user,$user_id,"newProject",$request->get('id'),$today);
                            }else{
                                $this->sdt->createNotification($user,$user_id,"editProject",$request->get('id'),$today);
                            }
                            $usersA[$user]+=1;
                        }

                        $arrkeys = array_keys($usersA);
                        foreach ($arrkeys as $arrkey) {
                            if($usersA[$arrkey]==1){
                                // eliminar relacion
                                $userProjects = UsersProjects::findFirst('user_id = '.$arrkey.' AND project_id = '.$request->get('id'));
                                $userProjects->delete();
                                $this->sdt->createNotification($arrkey,$user_id,"deleteProject",$request->get('id'),$today);
                            }
                        }

                        $this->flash->success('El proyecto ha sido editado');
                    }
                    return $this->dispatcher->forward(array("action" => "index"));
                }
            }
            else
            {
                $this->createListError($form);
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');
        $this->addBreadcrumb('Equipos','teams');
        $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
        $this->addBreadcrumb('Proyectos','projects/index/'.$team_id);
        $this->addBreadcrumb('Editar Proyecto','projects/edit/'.$team_id);

        $project = Projects::findFirst($project_id);

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'    =>  'Editar Proyecto',
            'v_session'     =>  $has_user,
            'form'          =>  new FormProject($project,$team_id),
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'team_id'       =>  $team_id,
            'project_id'    =>  $project_id
        ));

        // Seleccionar los usuarios que estan en el proyecto
        $project = Projects::findFirst($project_id);
        $usersProject = UsersProjects::findByProjectId($project_id);
        $arr = array();
        foreach ($usersProject as $item){
            array_push($arr, $item->user_id);
        }
        $this->tag->setDefaults(array(
            "users_ids[]"   =>  $arr,
            "user"          =>  $project->user_id
        ));

        $this->assets
            ->addCss('css/multi_select/bootstrap-multiselect.css');

        $this->assets
            ->addJs('js/multi_select/bootstrap-multiselect.js')
            ->addJs('js/multi_select/init.js');

    }

    public function deleteAction($team_id=0, $project_id=0){
        $user_id = $this->session->get("userId");
        $project = Projects::findFirst($project_id);
        if($project->user_id==$user_id && $project->team_id==$team_id){
            $request = $this->request;
            if($request->isPost()){
                $form = new FormDelete();
                if($form->isValid($request->getPost())!= false){
                    $this->deleteProject($project_id);
                    $this->flash->success('El proyecto ha sido eliminado');
                    return $this->dispatcher->forward(array("action" => "index"));
                }
            }
            $has_user = $this->session->has("userId");
            if ($has_user){
                $this->createAlerts();
                $this->createNotifications();
                $this->createCustomization();
            }

            // User el Breadcrumbs de bootstrap
            $this->addBreadcrumb('[ HTD','htd');
            $this->addBreadcrumb('Checklist','checklist');
            $this->addBreadcrumb('RM ]','rmregistries');
            $this->addBreadcrumb('Equipos','teams');
            $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
            $this->addBreadcrumb('Proyectos','projects/index/'.$team_id);
            $this->addBreadcrumb('Eliminar Proyecto','projects/edit/'.$team_id);

            $this->view->setVars(array(
                'title_view'    =>  'Eliminar Proyecto',
                'v_session'     =>  $has_user,
                'form'          =>  new FormDelete($project),
                'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
                'team_id'       =>  $team_id,
                'project_id'    =>  $project_id
            ));
        }else{
            return $this->response->redirect('index/index');
        }
    }

    public function getProjectsAction(){
        $this->autoRender=false;
        if($this->request->isAjax() == true){
            $user_id = $this->session->get("userId");
            $phql = 'SELECT
			Projects.id, Projects.name
			FROM Projects
			WHERE Projects.user_id = '.$user_id.'
            ORDER BY Projects.name ASC';
            $projects = $this->modelsManager->executeQuery($phql);
            $this->response->setJsonContent($projects->toArray());
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

}

