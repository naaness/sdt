<?php
use PackageForm as FormPackage;
use DeleteForm as FormDelete;
use \Phalcon\Paginator\Adapter\Model as Paginacion;

class PackagesController extends ControllerBase
{

    public function indexAction($team_id)
    {
        $paginator = new Paginacion(
            array(
                "data" => Packages::find(array(
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
        $this->addBreadcrumb('Checklist','packages/index/'.$team_id);

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
            'title_view'    =>  'Checklists',
            'v_session'     =>  $has_user,
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'team'          =>  $team,
            'team_id'       =>  $team_id,
            'user_id'       =>  $user_id,
            'user_package'  =>  $this->_get_user_package($user_id),
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
            $form = new FormPackage(null, $team_id);
            if($form->isValid($request->getPost())!= false)
            {
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:M::S');

                $package = new Packages();
                $package->team_id = $team_id;
                $package->name = $request->get('name');
                $package->code = strtoupper($request->get('code'));
                $package->description = $request->get('description');
                $package->user_id = $user_id;
                $package->created = $today;
                $package->modified = $today;
                $package->status = 1;
                if (!$package->validationCode()) {
                    $this->createListError($package);
                }else{
                    if ($package->save()) {
                        $this->flash->success('El checklist ha sido creado');
                        return $this->dispatcher->forward(array("action" => "index"));
                    }else{
                        $this->createListError($package);
                    }
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
        $this->addBreadcrumb('Checklist','packages/index/'.$team_id);
        $this->addBreadcrumb('Crear Checklist','Packages/add/'.$team_id);

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Crear Equipo de Trabajo',
            'v_session' => $has_user,
            'form'      => new FormPackage(null,$team_id),
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'team_id'   => $team_id
        ));
    }

    public function editAction($team_id=0,$package_id=0){
        $project_id=0;
        $request = $this->request;
        $user_id = $this->session->get("userId");
        $user = UsersTeams::findFirst('user_id = '.$user_id.' AND team_id = '.$team_id);
        if(!$user){
            $this->flash->error('No puedes realizar esta accion');
            return $this->dispatcher->forward(array("action" => "index"));
        }
        // verificar si el usuario es el deño del checklist
        $package = Packages::findFirst($package_id);
        if ($package->user_id != $user_id ){
            $this->flash->error('No puedes editar este checklist');
            return $this->dispatcher->forward(array("action" => "index"));
        }
        if($request->isPost())
        {
            $form = new FormPackage(null, $team_id);
            if($form->isValid($request->getPost())!= false)
            {
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:M::S');

                $package->team_id = $team_id;
                $package->name = $request->get('name');
                $package->description = $request->get('description');
                $package->user_id = $request->get('user_id');
                $package->modified = $today;
                $valid = true;
                if ($package->code!=strtoupper($request->get('code'))) {
                    $package->code = strtoupper($request->get('code'));
                    if (!$package->validationCode()) {
                        $this->createListError($package);
                        $valid = false;
                    }
                }
                if($valid){
                    if ($package->save()) {
                        $this->flash->success('El checklist ha sido modificado');
                        return $this->dispatcher->forward(array("action" => "index"));
                    }
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
        $this->addBreadcrumb('Checklist','packages/index/'.$team_id);
        $this->addBreadcrumb('Editar Checklist','Packages/add/'.$team_id);

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $package = Packages::findFirst($package_id);
        $this->view->setVars(array(
            'title_view'=>  'Crear Equipo de Trabajo',
            'v_session' =>  $has_user,
            'form'      =>  new FormPackage($package,$team_id),
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'team_id'   =>  $team_id,
            'package_id'=>  $package_id
        ));
    }

    /**
     * get user and package method
     *
     * @throws _get_user_package
     * @param string $team_id
     * @return array
     */
    public function _get_user_package ($user_id = null) {
        $userPackage = UsersPackages::find('user_id = '.$user_id);
        $codes = array();
        foreach ($userPackage as $item){
            $codes[$item->package_id]=$item->status;
        }
        return $codes;
    }

    /**
     * createUsePackage method
     *
     * @return void
     */
    public function createUsePackageAction ($team_id=0, $package_id=0) {
        $user_id = $this->session->get("userId");
        // Cambiar el estado de user en relacion al paquete a 1
        $userPackage = new UsersPackages();
        $userPackage->user_id = $user_id;
        $userPackage->package_id = $package_id;
        $userPackage->status = 1;

        if ($userPackage->save()) {
            $fecha_hoy = new \DateTime('America/Mexico_City');
            $today = $fecha_hoy->format('Y-m-d H:m:s');
            $today0 = $fecha_hoy->format('Y-m-d'). ' 00:00:00';
            $tasks = Tasks::find('package_id = '.$package_id. ' AND user_id = 0');
            // Copiar las tareas y unidades de tiempo para el usuario que quiere usar
            foreach ($tasks as $task) {
                // Crear Tarea clon
                $newtask = new Tasks();
                $newtask->package_id = $package_id;
                $newtask->project_id = 0;
                $newtask->name = $task->name;
                $newtask->description = $task->description;
                $newtask->blocked = 0;
                $newtask->created = $today;
                $newtask->modified = $today;
                $newtask->status = 1;
                $newtask->user_id = $user_id;
                $newtask->percent = '00.00';
                $newtask->priority_id = $task->priority_id;
                $newtask->task_id_parent = $task->id;
                $newtask->edit = 0;

                if ($newtask->save()) {
                    $last_task_id = $newtask->id;

                    // Crear relacion entre el usuario y la tarea
                    $usertask = new UsersTasks();
                    $usertask->user_id = $user_id;
                    $usertask->task_id = $last_task_id;
                    $usertask->status = 1;
                    $usertask->save();

                    // Crear relacion entre usuario y checklist

                    // Buscar las unidades de tiempo que tienen que ver con la tarea de la que se realiza la copia
                    $algo = $task->getUnidTimes('start_day > "'.$today0.'"');
                    foreach ( $algo as $nidTime) {
                        // Crear una copia de cada unidad de tiempo
                        $newunid = new UnidTimes();
                        $newunid->task_id = $last_task_id;
                        $newunid->created = substr($today,0, 10);
                        $newunid->modified = substr($today,0, 10);
                        $newunid->start_day = substr($nidTime->start_day,0, 10);
                        $newunid->unit_time_id_parent = $nidTime->id;
                        $newunid->priority_id = $nidTime->priority_id;
                        $newunid->save();

                        $trm = $nidTime->tasksRepeats;
                        if($trm){
                            $tr_h = new TasksRepeats();
                            $tr_h->task_id              =   $newunid->task_id;
                            $tr_h->unid_time_id         =   $newunid->id;
                            $tr_h->options              =   $trm->options;
                            $tr_h->each_period          =   $trm->each_period;
                            $tr_h->day_L                =   $trm->day_L;
                            $tr_h->day_M                =   $trm->day_M;
                            $tr_h->day_X                =   $trm->day_X;
                            $tr_h->day_J                =   $trm->day_J;
                            $tr_h->day_V                =   $trm->day_V;
                            $tr_h->day_S                =   $trm->day_S;
                            $tr_h->day_D                =   $trm->day_D;
                            $tr_h->month_week           =   $trm->month_week;
                            $tr_h->start_day            =   $trm->start_day;
                            $tr_h->N_R_T                =   $trm->N_R_T;
                            $tr_h->repeat_interval      =   $trm->repeat_interval;
                            $tr_h->end_day              =   $trm->end_day;
                            $tr_h->day_position         =   $trm->day_position;
                            $tr_h->save();
                        }
                        // Crear las alertas
//                        $this->sdt->createAlert($newunid,$user_id,"newActivity",$task);
//                        // Para cada usuario se debe crear un alerta
//                        $alert = new Alerts();
//                        $alert->user_id         =   $user_id;
//                        $alert->change_user_id  =   $user_id;
//                        $alert->unid_time_id    =   $newunid->id;
//                        $alert->change_id       =   $last_task_id;
//                        $alert->type            =   "newActivity";
//                        $alert->date            =   substr($nidTime->start_day,0,10);
//                        $alert->was_seen        =   0;
//                        $alert->send_email      =   0;
//                        $alert->save();
                    }
                }
            }
        }
        return $this->response->redirect('packages/index/'.$team_id);
        // return $this->dispatcher->forward(array("action" => "index"));
    }

    /**
     * UsePackage method
     *
     * @return void
     */
    public function usePackageAction ($team_id=0, $package_id=0) {
        $this->_changeStatusUserPackage($team_id,$package_id,1);
        return $this->response->redirect('packages/index/'.$team_id);
    }

    /**
     * noUsePackag method
     *
     * @return void
     */
    public function noUsePackageAction($team_id=0, $package_id=0) {
        $this->_changeStatusUserPackage($team_id,$package_id,0);
        return $this->response->redirect('packages/index/'.$team_id);
    }

    /**
     * _changeStatusUserPackage method
     *
     * @return void
     */
    public function _changeStatusUserPackage($team_id=null, $package_id=null, $status=null){
        $user_id = $this->session->get("userId");

        // Cambiar el estado de user en relacion al paquete a 0
        // Obtener el la relacion usuario paquete
        $usersPackage = UsersPackages::find('package_id = '.$package_id.' AND user_id = '.$user_id);
        foreach ($usersPackage as $task) {
            $task->status = $status;
            $task->save();
        }
        $tasks = Tasks::find('package_id = '.$package_id.' AND user_id = '.$user_id);
        echo "Tareas ". count($tasks). "<br>";
        foreach ($tasks as $task) {
            foreach ( $task->getUsersTasks() as $usertask) {
                $usertask->status = $status;
                $usertask->save();
            }
        }
    }

    public function deleteAction($team_id=0, $package_id=0){
        $user_id = $this->session->get("userId");
        $package = Packages::findFirst($package_id);
        if($package->user_id==$user_id && $package->team_id==$team_id){
            $request = $this->request;
            if($request->isPost()){
                $form = new FormDelete();
                if($form->isValid($request->getPost())!= false){
                    $this->deletePackage($package_id);

                    $this->flash->success('El checklist ha sido eliminado');
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
            $this->addBreadcrumb('Checklists','packages/index/'.$team_id);
            $this->addBreadcrumb('Eliminar Checklist','packages/edit/'.$team_id);

            $this->view->setVars(array(
                'title_view'    =>  'Eliminar Checklist',
                'v_session'     =>  $has_user,
                'form'          =>  new FormDelete($package),
                'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
                'team_id'       =>  $team_id,
                'package_id'    =>  $package_id
            ));
        }else{
            return $this->response->redirect('index/index');
        }
    }
}

