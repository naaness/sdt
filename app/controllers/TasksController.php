<?php
use \Phalcon\Paginator\Adapter\Model as Paginacion;
use TaskForm as FormTask;
use DeleteForm as FormDelete;
class TasksController extends ControllerBase
{

    public function indexAction($team_id=0, $project_id=0, $package_id=0, $model=0) {

//        $user_id = $this->session->has("userId");
//        if($user_id){
            $user_id = $this->session->get("userId");
            $can_editable = false;
            if ($team_id && $project_id && $package_id) { // Mostrar todas las tareas del usuario
                $paginator = new Paginacion(
                    array(
                        "data" => Tasks::find(array(
                                    "user_id = '".$user_id."'  AND status > 0",
                                    "status > 0",
                                    "order" => "name"
                                )
                            ),
                        "limit"=> 25,
                        //variable get page convertida en un integer
                        "page" => $this->request->getQuery('page', 'int')
                    )
                );
            }elseif ($team_id && $project_id) { // Mostrar las tareas del proyecto
                $paginator = new Paginacion(
                    array(
                        "data" => Tasks::find(array(
                                    "project_id = '".$project_id."'  AND status > 0" ,
                                    "order" => "name"
                                )
                            ),
                        "limit"=> 25,
                        //variable get page convertida en un integer
                        "page" => $this->request->getQuery('page', 'int')
                    )
                );
                $project = Projects::findFirst($project_id);
                if ($project->user_id==$user_id){
                    $can_editable=true;
                }
            }elseif ($team_id && $package_id && $model) { // Mostrar las tareas modelos del paquete
                $paginator = new Paginacion(
                    array(
                        "data" => Tasks::find(array(
                                    "package_id = '".$package_id."' AND user_id = 0  AND status > 0",
                                    "order" => "name"
                                )
                            ),
                        "limit"=> 25,
                        //variable get page convertida en un integer
                        "page" => $this->request->getQuery('page', 'int')
                    )
                );
                $package = Packages::findFirst($package_id);
                if ($package->user_id==$user_id){
                    $can_editable=true;
                }
            }elseif ($team_id && $package_id) { // Mostrar las tareas del paquete elegido por el usuario
                $paginator = new Paginacion(
                    array(
                        "data" => Tasks::find(array(
                                    "package_id = '".$package_id."' AND user_id = 0 AND status > 0",
                                    "order" => "name"
                                )
                            ),
                        "limit"=> 25,
                        "page" => $this->request->getQuery('page', 'int')
                    )
                );
                $package = Packages::findFirst($package_id);
                if ($package->user_id==$user_id){
                    $can_editable=true;
                }
            }else{

                $phql = 'SELECT
                Tasks.* , UsersTasks.*
                FROM Tasks
                INNER JOIN UsersTasks ON UsersTasks.task_id = Tasks.id
                WHERE
                Tasks.project_id = 0
                AND Tasks.package_id = 0
                AND Tasks.status > 0
                AND UsersTasks.status = 1
                AND UsersTasks.user_id = '.$user_id.'
                ORDER BY Tasks.name ASC ';
                $results = $this->modelsManager->executeQuery($phql);

                $paginator = new Paginacion(
                    array(
                        "data" =>  $results,
                        //limite por página
                        "limit"=> 25,
                        //variable get page convertida en un integer
                        "page" => $this->request->getQuery('page', 'int')
                    )
                );
                $this->view->pick("tasks/indexme");
            }

            //pasamos el objeto a la vista con el nombre de $page
            $this->view->page = $paginator->getPaginate();

            $this->addBreadcrumb('[ HTD','htd');
            $this->addBreadcrumb('Checklist','checklist');
            $this->addBreadcrumb('RM ]','rmregistries');
            $can_add = true;
            if ($project_id){
                // Saber si es el lider del proyecto
                $element = Projects::findFirst($project_id);
                if ($element->user_id!=$user_id){
                    $can_add = false;
                }
                $this->addBreadcrumb('Equipos','teams');
                $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
                $this->addBreadcrumb('Proyectos','projects/index/'.$team_id);
            }elseif ($package_id){
                $element = Packages::findFirst($package_id);
                $this->addBreadcrumb('Equipos','teams');
                $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
                $this->addBreadcrumb('Checklists','packages/index/'.$team_id);
            }
            $this->addBreadcrumb('Tareas','teams/index/'.$team_id.'/'.$project_id.'/'.$package_id.'/'.$model);


            $has_user = $this->session->has("userId");
            if ($has_user){
                $this->createAlerts();
                $this->createNotifications();
                $this->createCustomization();
                $this->checklistSetting();
            }
            $this->view->setVars(array(
                'title_view'    =>  'Tareas',
                'v_session'     =>  $has_user,
                'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
                'team_id'       =>  $team_id,
                'project_id'    =>  $project_id,
                'package_id'    =>  $package_id,
                'model'         =>  $model,
                'user_id'       =>  $user_id,
                'element'       =>  $element,
                'can_add'       =>  $can_add,
                'title'         =>  'Tareas'
            ));

            // Archivos js y css para el checklist
            $this->assets
                ->addCss('css/jQueryUI.min.css')
                ->addCss('css/checklist.css')
                ->addCss('css/unit-time.css')
                ->addCss('css/infoTask.css')
                ->addCss('css/chatTask.css');

            $this->assets
                ->addJs('js/jQueryUI.js')
                ->addJs('js/loadAnimation.js')              // Controlar animacion de loading
                ->addJs('js/moment.min.js')                 // Manipulacion de dechas
                ->addJs('js/rrule/rrule.js')                // ayuda a rrule
                ->addJs('js/rrule/nlp.js')                  // Manipulacion de recurrencias SI sirvee!!!!!!
                ->addJs('js/sdt_general/dateFormatString.js')         // Ayuda a moment para trabajar con los datos del ch
                ->addJs('js/checklist/ch_var_global.js')              // Principales variables de ch
                ->addJs('js/sdt_general/repeatTask.js')                 // Funcionalidad para crear recurrencias
                ->addJs('js/checklist/sdt_checklist_live.js')         // Core del checklist
                ->addJs('js/checklist/sdt_checklist_filtrar.js')      // funcionalidad de filtrar ch
                ->addJs('js/infoTask.js')                   // informacion de la tarea
                ->addJs('js/chatTask.js')                   // Peuqeño foro por tarea
                ->addJs('js/checklist/sdt_checklist_init.js');


    }

    public function addAction($team_id=0, $project_id=0, $package_id=0, $model=0){
        $request = $this->request;
        $user_id = $this->session->get("userId");
        $options = array(
            'team_id'       =>  $team_id,
            'project_id'    =>  $project_id,
            'package_id'    =>  $package_id,
        );
        if($request->isPost())
        {
            $form = new FormTask(null, $options);
            if($form->isValid($request->getPost())!= false)
            {
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:m:s');

                $task = new Tasks();
                $task->package_id = $package_id;
                $task->project_id = $project_id;
                $task->name = $request->get('name');
                $task->description = $request->get('description');
                $task->status = 1;
                $task->priority_id = $request->get('priority_id');
                $task->user_id = $request->get('user_id');
                $task->task_id_parent = 0;
                if ($package_id>0) {
                    $task->user_id = 0;
                }elseif ($project_id>0) {
                    // Si la tarea es para el mismo director del proyecto entonces el status es 1
                    if ($user_id!=$request->get('user_id')) {
                        $task->status = 2;
                    }
                }
                $task->edit = 0;
                if ($task->save()) {
                    $last_id = $task->id;
                    if ($project_id) {
                        if ($user_id != $request->get('user_id')){
                            $usertask = new UsersTasks();
                            $usertask->user_id = $user_id;
                            $usertask->task_id = $last_id;
                            $usertask->status = 1;
                            $usertask->save();
                        }
                        // Se debe crear dos relaciones de tarea usuario
                        // usuario responsable con tarea creada
                        $usertask = new UsersTasks();
                        $usertask->user_id = $request->get('user_id');
                        $usertask->task_id = $last_id;
                        $usertask->status = 1;
                        $usertask->save();


                        // Para cada usuario se debe crear una notificacion
                        $this->sdt->createNotification($task->user_id,$user_id,"delegate",$task->id,$today);

                        $delegate = new Delegates();
                        $delegate->first_user = $user_id;
                        $delegate->second_user = $request->get('user_id');
                        $delegate->task_id = $last_id;
                        $delegate->save();

                    }elseif ($package_id){
                        // Crear Una tarea por cada usuario vinculado
                        // Obtener cada usuario relacionado con el paquete
                        $results = UsersPackages::findByPackageId($package_id);
                        // Crear una tarea para cada usuario
                        foreach ($results as $result) {
                            // Crear Tarea apartir de la original del paquete

                            $task = new Tasks();
                            $task->project_id = 0;
                            $task->package_id = $package_id;
                            $task->name = $request->get('name');
                            $task->description = $request->get('description');
                            $task->blocked = 0;
                            $task->created = $today;
                            $task->modified = $today;
                            $task->status = 1;
                            $task->user_id = $result->user_id;
                            $task->percent = '00.00';
                            $task->priority_id = $request->get('priority_id');
                            $task->task_id_parent = $last_id;

                            if ($task->save()) {
                                // obtener la ultima tarea creada
                                $task_id_package = $task->id;

                                $usertask = new UsersTasks();
                                $usertask->user_id = $result->user_id;
                                $usertask->task_id = $task_id_package;
                                $usertask->status = 1;
                                $usertask->save();

                                // Crear una notificacion que informe sobre que a este checklist se agrego una nueva tarea
                                $this->sdt->createNotification($result->user_id,$user_id,"newChecklist",$package_id,$today);
                            }
                        }
                    }
                    $this->flash->success('La tarea ha sido creada');
                    return $this->dispatcher->forward(array("action" => "index"));
                }else{
                    $this->createListError($task);
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
        $can_add = true;
        if ($project_id>0){
            // Saber si es el lider del proyecto
            $element = Projects::findFirst($project_id);
            $this->addBreadcrumb('Equipos','teams');
            $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
            $this->addBreadcrumb('Proyectos','projects/index/'.$team_id);
        }elseif ($package_id>0){
            $element = Packages::findFirst($package_id);
            $this->addBreadcrumb('Equipos','teams');
            $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
            $this->addBreadcrumb('Checklists','packages/index/'.$team_id);
        }
        $this->addBreadcrumb('Tareas','tasks/index/'.$team_id.'/'.$project_id.'/'.$package_id.'/'.$model);
        $this->addBreadcrumb('Crear Tarea','tasks/add/'.$team_id.'/'.$project_id.'/'.$package_id.'/'.$model);

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'    =>  'Crear Tarea',
            'v_session'     =>  $has_user,
            'form'          =>  new FormTask(null, $options),
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'team_id'       =>  $team_id,
            'project_id'    =>  $project_id,
            'package_id'    =>  $package_id,
            'model'         =>  $model,
            'user_id'       =>  $user_id,
            'element'       =>  $element->name
        ));

        if ($team_id==0 && $project_id==0 && $package_id==0 && $model==0){
            $this->tag->setDefaults(array(
                "user_id"   =>  $user_id,
            ));
        }
    }

    public function editAction($team_id=0, $project_id=0, $package_id=0, $model=0, $task_id=0){
        $request = $this->request;
        $user_id = $this->session->get("userId");
        $options = array(
            'team_id'       =>  $team_id,
            'project_id'    =>  $project_id,
            'package_id'    =>  $package_id,
        );
        if($request->isPost())
        {
            $form = new FormTask(null, $options);
            if($form->isValid($request->getPost())!= false)
            {
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:m:s');

                $task = Tasks::findFirst($request->get('id'));
                $task->name = $request->get('name');
                $task->description = $request->get('description');
                $task->priority_id = $request->get('priority_id');
                $task->modified = $today;
                if ($package_id>0) {
                    $task->user_id = 0;
                }elseif ($project_id>0) {
                    if($request->has('user_id')){
                        if($task->user_id != $request->get('user_id')){
                            // Eliminar la relaciones de el anterior usuario con la tarea
                            $conditions = "user_id = ?1 AND task_id = ?2";
                            $parameters = array(1 => $task->user_id, 2 => $task->id);
                            $userstasks = UsersTasks::find(array(
                                $conditions,
                                "bind" => $parameters
                            ));
                            foreach ($userstasks as $userstask) {
                                $userstask->delete();
                            }

                            $task->user_id = $request->get('user_id');
                            $userTask = new UsersTasks();
                            $userTask->user_id = $task->user_id;
                            $userTask->task_id = $task->id;
                            $userTask->status = 1;
                            if ($userTask->save()==false) {
                                $this->createListError($userTask);
                            }

                            // Para cada usuario se debe crear una notificacion
                            $this->sdt->createNotification($task->user_id,$user_id,"delegate",$task->id,$today);

                            $delegate = new Delegates();
                            $delegate->first_user = $user_id;
                            $delegate->second_user = $request->get('user_id');
                            $delegate->task_id = $task->id;
                            $delegate->save();

                            // pasar todas las alertas al nuevo dueño
                            $alerts = Alerts::find('change_id = '.$task->id);
                            foreach ($alerts as $alert) {
                                $alert->user_id = $request->get('user_id');
                                $alert->change_user_id = $user_id;
                                $alert->save();
                            }
                        }
                    }
                    // Si la tarea es para el mismo director del proyecto entonces el status es 1
                    if ($user_id!=$request->get('user_id')) {
                        if ($task->status!=2){
                            // Para crear una notificacion de modificacion de la tarea
                            $this->sdt->createNotification($request->get('user_id'),$user_id,"changeUnidTime",$request->get('id'),$today);
                        }
                        $task->status = 2;

                    }
                }
                if ($task->save()==false) {
                    $this->createListError($task);
                }else{
                    if ($package_id){
                        $taskschildren = Tasks::findByTaskIdParent($task->id);
                        // Crear una tarea para cada usuario
                        foreach ($taskschildren as $taskc) {
                            // Actualizar
                            $taskc->name = $request->get('name');
                            $taskc->description = $request->get('description');
                            $taskc->save();

                            // Para cada tarea hija crear una noticiacion de dicho cambio
                            $this->sdt->createNotification($taskc->user_id,$user_id,"changeUnidTime",$taskc->id,$today);
                        }
                    }

                    $this->flash->success('La tarea ha sido actualizada');
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
        $can_add = true;
        if ($project_id>0){
            // Saber si es el lider del proyecto
            $element = Projects::findFirst($project_id);
            $this->addBreadcrumb('Equipos','teams');
            $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
            $this->addBreadcrumb('Proyectos','projects/index/'.$team_id);
        }elseif ($package_id>0){
            $element = Packages::findFirst($package_id);
            $this->addBreadcrumb('Equipos','teams');
            $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
            $this->addBreadcrumb('Checklists','packages/index/'.$team_id);
        }
        $this->addBreadcrumb('Tareas','tasks/index/'.$team_id.'/'.$project_id.'/'.$package_id.'/'.$model);
        $this->addBreadcrumb('Editar Tarea','tasks/edit/'.$team_id.'/'.$project_id.'/'.$package_id.'/'.$model);

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'    =>  'Editar Tarea',
            'v_session'     =>  $has_user,
            'form'          =>  new FormTask(Tasks::findFirst($task_id), $options),
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'team_id'       =>  $team_id,
            'project_id'    =>  $project_id,
            'package_id'    =>  $package_id,
            'model'         =>  $model,
            'task_id'       =>  $task_id,
            'user_id'       =>  $user_id,
            'element'       =>  $element
        ));

        if ($team_id==0 && $project_id==0 && $package_id==0 && $model==0){
            $this->tag->setDefaults(array(
                "user_id"   =>  $user_id,
            ));
        }
    }

    public function deleteAction($team_id=0, $project_id=0, $package_id=0, $model=0, $task_id=0){
        $request = $this->request;
        $user_id = $this->session->get("userId");
        if($request->isPost()){
            $form = new FormDelete();
            if($form->isValid($request->getPost())!= false){
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:m:s');
                $task = Tasks::findFirst($request->get('id'));
                $task->status = 0;
                $task->modified = $today;
                if ($task->save()==true){
                    // no duplicar notificacion para los usuarios
                    $users = array();
                    $userstasks = UsersTasks::find('task_id = '.$task->id);
                    foreach ($userstasks as $userstask) {
                        $userstask->status=0;
                        $userstask->save();
                        if (!isset($users[$userstask->user_id])){
                            $users[$userstask->user_id]= $userstask->user_id;
                            $this->sdt->createNotification($userstask->user_id,$user_id,"deleteTask",$task->id,$today);
                        }
                    }
                    // si la tarea tiene hijos entonces
                    $taskschildren = Tasks::findByTaskIdParent($task->id);
                    // Crear una tarea para cada usuario
                    foreach ($taskschildren as $taskc) {
                        // Para cada tarea hija crear una noticiacion de dicho cambio
                        $this->sdt->createNotification($taskc->user_id,$user_id,"deleteTask",$taskc->id,$today);

                        $users = array();
                        $userstasks = UsersTasks::find('task_id = '.$taskc->id);
                        foreach ($userstasks as $userstask) {
                            $userstask->status=0;
                            $userstask->save();
                            if (!isset($users[$userstask->user_id])){
                                $users[$userstask->user_id]= $userstask->user_id;
                                $this->sdt->createNotification($userstask->user_id,$user_id,"deleteTask",$task->id,$today);
                            }
                        }
                    }
                    $this->flash->success('La tarea ha sido eliminada');
                    return $this->dispatcher->forward(array("action" => "index"));
                }else{
                    $this->createListError($task);
                }
            }else{
                $this->createListError($form);
            }
        }
        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');
        if ($project_id){
            // Saber si es el lider del proyecto
            $this->addBreadcrumb('Equipos','teams');
            $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
            $this->addBreadcrumb('Proyectos','projects/index/'.$team_id);
        }elseif ($package_id){
            $this->addBreadcrumb('Equipos','teams');
            $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
            $this->addBreadcrumb('Checklists','packages/index/'.$team_id);
        }
        $this->addBreadcrumb('Tareas','tasks/index/'.$team_id.'/'.$project_id.'/'.$package_id.'/'.$model);
        $this->addBreadcrumb('Eliminar Tarea','tasks/delete/'.$team_id.'/'.$project_id.'/'.$package_id.'/'.$model);

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'    =>  'Eliminar Tarea',
            'v_session'     =>  $has_user,
            'form'          =>  new FormDelete(Tasks::findFirst($task_id)),
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'team_id'       =>  $team_id,
            'project_id'    =>  $project_id,
            'package_id'    =>  $package_id,
            'model'         =>  $model,
            'task_id'       =>  $task_id,
            'user_id'       =>  $user_id
        ));

    }
    public function getTaskInfoAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true)
        {
            if ( $request->has('id'))  {
                $phql = 'SELECT
                Tasks.*, Projects.*, Packages.*, Priorities.*, Users.username, Profiles.name, Profiles.last_name, RmRegistries.numbering, RmRegistries.registry, RmRegistries.day, RmRegistries.id
                FROM Tasks
                LEFT JOIN Projects ON Projects.id = Tasks.project_id
                LEFT JOIN Packages ON Packages.id = Tasks.package_id
                LEFT JOIN Priorities ON Priorities.id = Tasks.priority_id
                LEFT JOIN Users ON Users.id = Tasks.user_id
                LEFT JOIN Profiles ON Users.id = Profiles.user_id
                LEFT JOIN RmRegistriesTasks On RmRegistriesTasks.task_id = Tasks.id
                LEFT JOIN RmRegistries On RmRegistries.id = RmRegistriesTasks.rm_registry_id
                WHERE
                Tasks.id = '.$request->get('id').'';
                $results = $this->modelsManager->executeQuery($phql);

                $arr = $results->toArray();
                if($arr[0]["numbering"]){
                    $rmregistry = RmRegistries::findFirst($arr[0]["id"]);
                    $arr[0]["username2"] = $rmregistry->users->username;
                    $arr[0]["name2"] = $rmregistry->users->profiles->name;
                    $arr[0]["last_name2"] = $rmregistry->users->profiles->last_name;
                    if($rmregistry->rm_label_id>0){
                        $arr[0]["lb_color"] = $rmregistry->rmLabels->color;
                        $arr[0]["lb_b_color"] = $rmregistry->rmLabels->b_color;
                        $arr[0]["lb_font"] = $rmregistry->rmLabels->rmFonts->name;
                        $arr[0]["lb_size"] = $rmregistry->rmLabels->rmSizes->size;
                    }
                }

                $this->response->setJsonContent($arr);
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    public function giveAnswerAction (){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true)
        {
            if ( $request->has('taread') && $request->has('respues') && $request->has('comentario') ) {
                $user_id = $this->session->get("userId");
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:m:s');
                $task = Tasks::findFirst($request->get('taread'));

                // Primero buscar no el ultimo usuario relacionado
                // sino el penultimo, porque ese delego la tarea aeste ultimo
                $users_tasks = UsersTasks::find('task_id = '.$task->id);

                if ($request->get('respues')==3) {
                    $fecha_hoy = new \DateTime('America/Mexico_City');
                    $today = $fecha_hoy->format('Y-m-d H:m:s');
                    $comment = new Comments();
                    $comment->task_id = $task->id;
                    $comment->user_id = $task->user_id;
                    $comment->date = $today;
                    $comment->comment = $request->get('comentario');
                    $comment->save();

                    $type = "rejected";
                }elseif ($request->get('respues')==1) {
                    $type = "accepted";

                    // todas las alertas creadas para
                    // cada unidad de tiempo de esta tarea pasarla a la persona que
                    // acepto la tarea
                    $alerts = Alerts::find('change_id = '.$task->id);
                    foreach ($alerts as $alert) {
                        $alert->type    =   "changeActivity";
                        $alert->was_seen        =   1;
                        $alert->save();

//                        $alertnew = new Alerts();
//                        $alertnew->user_id         =   $user_id;
//                        $alertnew->change_user_id  =   $alert->user_id;
//                        $alertnew->unid_time_id    =   $alert->unid_time_id;
//                        $alertnew->change_id       =   $task->id;
//                        $alertnew->type            =   "newActivity";
//                        $alertnew->date            =   substr($alert->date,0,10);
//                        $alertnew->was_seen        =   0;
//                        $alertnew->send_email      =   0;
//                        $alertnew->save();
                        $this->sdt->createAlert($alert->unidTimes,$user_id,"newActivity",tasks);
                    }
                }

                // Entonces la consulta debe tener minimo 2 registros
                if ($users_tasks->count()>1){
                    // Toma el penultimo registro
                    $user_res = $users_tasks[$users_tasks->count()-2];
                    // Se notifica que el usuario acepto la tarea
                    $this->sdt->createNotification($user_res->user_id,$user_id,$type,$task->id,$today);
                }elseif ($users_tasks->count()==1){
                    // verificar si la tarea proviene de un proyecto
                    if($task->project_id>0){
                        // Se notifica que el usuario acepto la tarea al lider del proyecto
                        $this->sdt->createNotification($task->projects->user_id,$user_id,$type,$task->id,$today);
                    }
                }

                $task->status = $request->get('respues');
                $task->save();

            }
        }
        $this->response->setJsonContent('Ok');
        $this->response->setStatusCode(200,'Ok');
        $this->response->send();
    }

    public function getTaskAction (){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            if ( $request->has('id') ) {
                $tasks = Tasks::findFirst($request->get('id'));
                $this->response->setJsonContent($tasks->toArray());
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    public function updateTaskAction (){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            if ( $request->has('id') && $request->has('name') && $request->has('description') && $request->has('priority') ) {

                // Actualizar Tarea
                $task = Tasks::findFirst($request->get('id'));
                $task->name = $request->get('name');
                $task->description = $request->get('description');
                $task->priority_id= $request->get('priority');
                if ($task->save()) {
                    $this->response->setJsonContent('Ok');
                    $this->response->setStatusCode(200,'Ok');
                    $this->response->send();
                }else{
                    $this->response->setStatusCode(404,'Not Found');
                }
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }


    /**
     * get package
     *
     * @throws _get_package
     * @param integer $package_id
     * @return array
     */
    public function newTaskAction () {
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            if ( $request->has('name') && $request->has('description') && $request->has('priority') && $request->has('delegate') && $request->has('project')) {
                $user_id = $this->session->get("userId");
                // Crear Tarea
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:m:s');
                $task = new Tasks();
                $task->project_id = $request->get('project');
                $task->created = $today;
                $task->modified = $today;
                $task->name = $request->get('name');
                $task->description = $request->get('description');
                $task->blocked = 0;
                $task->status = 1;
                $task->percent = '00.00';
                $task->priority_id  = $request->get('priority');
                $task->user_id = $request->get('delegate');
                $task->task_id_parent = 0;
                $task->edit = 0;

                if ($task->save()) {
                    $last_task_id = $task->id;
                    // Crear la relacion usuario tarea
                    $userTask           =   new UsersTasks();
                    $userTask->user_id  =   $user_id;
                    $userTask->task_id  =   $last_task_id;
                    $userTask->status   =   1;
                    $userTask->save();

                    if($user_id!=$request->get('delegate')){
                        $userTask           =   new UsersTasks();
                        $userTask->user_id  =   $request->get('delegate');
                        $userTask->task_id  =   $last_task_id;
                        $userTask->status   =   1;
                        $userTask->save();
                    }
                    if ($request->has('date')) {
                        // Crear una unidad de tiempo para esa tarea
                        $date = explode('/', $request->get('date'));

                        $newunid = new UnidTimes();
                        $newunid->task_id = $last_task_id;
                        $newunid->start_day = $date[2].'-'.$date[1].'-'.$date[0];
                        $newunid->priority_id = $request->get('priority');
                        $newunid->save();

                        if($user_id!=$request->get('delegate')){
                            // Actualizo la tarea con su nuevo responsable
                            $task->status = 2;
                            $task->save();

                            $fecha_hoy = new \DateTime('America/Mexico_City');
                            $today = $fecha_hoy->format('Y-m-d H:m:s');

                            // Para cada usuario se debe crear una notificacion
                            $this->sdt->createNotification($task->user_id,$user_id,"delegate",$task->id,$today);

                            $delegate = new Delegates();
                            $delegate->first_user = $user_id;
                            $delegate->second_user = $task->user_id;
                            $delegate->task_id = $task->id;
                            $delegate->save();
                        }


//                        // Para cada usuario se debe crear un alerta
//                        $alert = new Alerts();
//                        $alert->user_id         =   $user_id;
//                        $alert->change_user_id  =   $user_id;
//                        $alert->unid_time_id    =   $newunid->id;
//                        $alert->change_id       =   $last_task_id;
//                        $alert->type            =   "newActivity";
//                        $alert->date            =   $date[2].'-'.$date[1].'-'.$date[0];
//                        $alert->was_seen        =   0;
//                        $alert->send_email      =   0;
//                        $alert->save();
                    }
                    $this->response->setJsonContent('Ok');
                    $this->response->setStatusCode(200,'Ok');
                    $this->response->send();
                }else{
                    $this->createListError($task);
                    $this->response->setStatusCode(404,'Not Found');
                }
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }
    }

    public function traslateRMtoHTDAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            if ( $request->has('name') && $request->has('description') && $request->has('id_rm') && $request->has('date') && $request->has('priority') && $request->has('delegate') && $request->has('project') ) {
                $user_id = $this->session->get("userId");
                // Crear Tarea

                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d H:m:s');
                $task = new Tasks();
                $task->name = $request->get('name');
                $task->project_id = $request->get('project');
                $task->description = $request->get('description');
                $task->priority_id = $request->get('priority');
                $task->user_id = $request->get('delegate');
                if ($task->save()) {
                    $last_task_id = $task->id;
                    // Crear la relacion usuario tarea
                    $usertask = new UsersTasks();
                    $usertask->user_id = $user_id;
                    $usertask->task_id = $last_task_id;
                    $usertask->status = 1;
                    if ($usertask->save()==false){
                        $this->createListError($usertask);
                        $this->response->setStatusCode(404,'Not Found');
                    }

                    if($request->get('delegate')!=$user_id){
                        $usertask = new UsersTasks();
                        $usertask->user_id = $request->get('delegate');
                        $usertask->task_id = $last_task_id;
                        $usertask->status = 1;
                        if ($usertask->save()==false){
                            $this->createListError($usertask);
                            $this->response->setStatusCode(404,'Not Found');
                        }

                        $delegate = new Delegates();
                        $delegate->first_user = $user_id;
                        $delegate->second_user = $task->user_id;
                        $delegate->task_id = $task->id;
                        if ($delegate->save()==false){
                            $this->createListError($delegate);
                            $this->response->setStatusCode(404,'Not Found');
                        }

                        $task->status = 2;
                        if ($task->save()==false){
                            $this->createListError($task);
                            $this->response->setStatusCode(404,'Not Found');
                        }

                        $this->sdt->createNotification($task->user_id,$user_id,"delegate",$task->id,$today);
                    }
                    // Crear relacion rm_registro y tarea
                    $RmRegistriesTask = new RmRegistriesTasks();
                    $RmRegistriesTask->rm_registry_id = $request->get('id_rm');
                    $RmRegistriesTask->task_id = $last_task_id;
                    $RmRegistriesTask->day = $today;
                    $RmRegistriesTask->created = $today;
                    $RmRegistriesTask->modified = $today;
                    if ($RmRegistriesTask->save()==true){
                        if ($request->has('date')) {
                            // Crear una unidad de tiempo para esa tarea
                            $date = explode('/', $request->get('date'));

                            $newunid = new UnidTimes();
                            $newunid->task_id = $last_task_id;
                            $newunid->start_day = $date[2].'-'.$date[1].'-'.$date[0];
                            $newunid->priority_id = $request->get('priority');
                            if ($newunid->save()==false){
                                $this->createListError($newunid);
                                $this->response->setStatusCode(404,'Not Found');
                            }

                            // Para cada usuario se debe crear un alerta
//                            $this->sdt->createAlert($newunid,$user_id,"newActivity",$task);

                        }
                        $this->response->setJsonContent('Ok');
                        $this->response->setStatusCode(200,'Ok');
                        $this->response->send();
                    }else{
                        $this->createListError($RmRegistriesTask);
                        $this->response->setStatusCode(404,'Not Found');
                    }
                }else{
                    $this->createListError($task);
                    $this->response->setStatusCode(404,'Not Found');
                }
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    public function viewAction($team_id=0, $project_id=0, $package_id=0, $model=0, $task_id=0){
        $user_id = $this->session->get("userId");
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');
        $can_add = true;
        if ($project_id){
            // Saber si es el lider del proyecto
            $project = Projects::findFirst($project_id);
            if ($project->user_id!=$user_id){
                $can_add=false;
            }
            $this->addBreadcrumb('Equipos','teams');
            $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
            $this->addBreadcrumb('Proyectos','projects/index/'.$team_id);
        }elseif ($package_id){
            $this->addBreadcrumb('Equipos','teams');
            $this->addBreadcrumb('Ver Equipo','teams/view/'.$team_id);
            $this->addBreadcrumb('Checklists','packages/index/'.$team_id);
        }
        $this->addBreadcrumb('Tareas','tasks/index/'.$team_id.'/'.$project_id.'/'.$package_id.'/'.$model);
        $this->addBreadcrumb('Ver Tarea','');

        // verificar si el usuario que ve la vista es el penultimo
        // entonces debera aparecer el link de reactivar
        $reactivar = false;
        $users_tasks = UsersTasks::find('task_id = '.$task_id);
        if ($users_tasks->count()>1){
            $user_res = $user_res = $users_tasks[$users_tasks->count()-2];
            if($user_res->user_id==$user_id){
                $reactivar=true;
            }
        }

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'    =>  'Ver Tarea',
            'v_session'     =>  $has_user,
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'team_id'       =>  $team_id,
            'project_id'    =>  $project_id,
            'package_id'    =>  $package_id,
            'model'         =>  $model,
            'user_id'       =>  $user_id,
            'can_add'       =>  $can_add,
            'task'          =>  Tasks::findFirst($task_id),
            'reactive'      =>  $reactivar
        ));
    }

    public function reactiveTaskAction($task_id=0){
        if ($task_id>0){
            $user_id = $this->session->has("userId");
            if ($user_id){
                $user_id = $this->session->get("userId");
                // verificar si el usuario que ve la vista es el penultimo
                // entonces debera aparecer el link de reactivar
                $reactivar = false;
                $users_tasks = UsersTasks::find('task_id = '.$task_id);
                if ($users_tasks->count()>1){
                    $user_res = $user_res = $users_tasks[$users_tasks->count()-2];
                    if($user_res->user_id==$user_id){
                        $reactivar=true;
                    }
                }
                if ($reactivar){
                    $fecha_hoy = new \DateTime('America/Mexico_City');
                    $today = $fecha_hoy->format('Y-m-d H:m:s');
                    // cambiar el estado de la tarea a en espera
                    $task = Tasks::findFirst($task_id);
                    $task->status = 2;
                    $task->save();
                    // Crear una notificacion para el responsable de la tarea que esta ha sido reactivada
                    $this->sdt->createNotification($task->user_id,$user_id,"reactived",$task->id,$today);

                    $team_id =0;
                    if ($task->project_id>0){
                        $team_id = $task->projects->team_id;
                    }elseif ($task->package_id>0){
                        $team_id = $task->packages->team_id;
                    }
                    $this->flash->success('La tarea ha sido reactivada');
                    return $this->dispatcher->forward(
                        array(
                            "action" => 'view',
                            "params" => array($team_id, $task->project_id, $task->package_id, 0,$task->id)
                        )
                    );
                }else{
                    return $this->response->redirect('index/index');
                }
            }else{
                return $this->response->redirect('index/index');
            }
        }else{
            return $this->response->redirect('index/index');
        }
    }

    public function deleteTaskAction($task_id=0){
        if($task_id>0){
            $user_id = $this->session->has("userId");
            if ($user_id){
                $user_id = $this->session->get("userId");
                $users_tasks = UsersTasks::find('task_id = '.$task_id);
                foreach ($users_tasks as $user_task) {
                    $user_task->status =0;
                    $user_task->save();

                    $fecha_hoy = new \DateTime('America/Mexico_City');
                    $today = $fecha_hoy->format('Y-m-d H:m:s');

                    // Crear una notificacion para el responsable de la tarea que esta ha sido reactivada
                    $this->sdt->createNotification($user_task->user_id,$user_id,"deleteTask",$user_task->id,$today);
                }
                $task = Tasks::findFirst($task_id);
                $task->status = 0;
                $task->save();
                $this->flash->success('La tarea ha sido reactivada');
                return $this->dispatcher->forward(array("controller"=>"checklist","action" => "index"));
            }else{
                return $this->response->redirect('htd/index');
            }
        }else{
            return $this->response->redirect('htd/index');
        }
    }

    public function cronJobSendEmailAction($code,$view=1){
        if ($code=="Zxvasdfhh6598o43444ABB65468465lskUdfliewWfsl"){
            $fecha_hoy = new \DateTime('America/Mexico_City');
            $today = $fecha_hoy->format('Y-m-d');
            $day = $fecha_hoy->format('d/m/Y');
            if($view==0 || $view==2){
                $users = Users::findById($this->session->get("userId"));
            }else{
                $users = Users::find();
            }
            foreach ($users as $user) {
                if($user->status){
                    $username =$user->username;
                    if (!empty($user->profiles)) {
                        $username=$user->profiles->name . ' '.$user->profiles->last_name;
                    }
                    // Obtener las alertas sin resolver hasta el dia de hoy

                    $phql = 'SELECT
                    UnidTimes.*
                    FROM UnidTimes
                    INNER JOIN Tasks ON (Tasks.status = 1 AND Tasks.id = UnidTimes.task_id)
                    WHERE
                    Tasks.user_id = '.$user->id.'
                    AND UnidTimes.start_day <= "'.$today.'"
                    AND UnidTimes.follow_up = 1
                    ORDER BY UnidTimes.start_day DESC, UnidTimes.follow_up';
                    $alerts = $this->modelsManager->executeQuery($phql);

                    $html_alert="";
                    $conta = 0;
                    foreach ($alerts as $alert) {
                        $priority = $alert->priorities->name;
                        $type = "(Personal)";
                        if ($alert->tasks->project_id>0){
                            $type = "(Del proyecto ".$alert->tasks->projects->name.")";
                        }elseif ($alert->tasks->package_id>0){
                            $type = "(Del proyecto ".$alert->tasks->packages->name.")";
                        }
                        $msg = '<strong>'.$alert->tasks->name.'</strong> '.$type;
                        $html_alert.='<tr style="font-family:Arial;font-size:14px;font-weight:normal;font-weight:normal;color:#666666">';
                        $html_alert.='<td height="40px" style="height:50px;border-right:2px solid #ffffff;padding-left:15px;border-bottom:2px solid #ffffff;background-color:#ededed">';
                        $html_alert.=$msg;
                        $html_alert.='</td>';
                        $html_alert.='<td height="40px" style="height:50px;border-right:2px solid #ffffff;padding-left:15px;border-bottom:2px solid #ffffff;background-color:#ededed;text-align:center;">';
                        $html_alert.= $this->sdt->diffName($this->sdt->diffDate($alert->start_day,$day));
                        $html_alert.='</td>';
                        $html_alert.='<td height="40px" style="width:70px;height:40px;border-right:2px solid #ffffff;border-bottom:2px solid #ffffff;background-color:#ededed;text-align:center;">';
                        $html_alert.= $priority;
                        $html_alert.='</td>';
                        $html_alert.='</tr>';
                    }
                    //$user->email => $username
                    if ($view==1){
                        $conta+=1;
                        $html_notification = $this->htmlNotifications($user->id,false);
                        if($html_notification!="" || $html_alert!=""){
//                        echo("---<br>");
//                        echo('SDT - Notificaciones ' . $this->sdt->dateString($day));
//                        echo("<br>---<br>");
//                        exit;
                            $this->getDI()->getMail()->send(
                                'SDT - Alertas - ' . $user->username,
                                array(
                                    $user->email =>  $username
                                ),
                                'SDT - Notificaciones ' . $this->sdt->dateString($day),
                                'alerts',
                                array(
                                    'html_alert' => $html_alert,
                                    'html_notification' => $html_notification
                                )
                            );
                        }
                    }
                }
            }
            if ($view==0){
                $html_notification = $this->htmlNotifications($user->id,false);
                if($html_notification!="" || $html_alert!=""){
                    $this->view->setVars(array(
                        'html_alert'        =>  $html_alert,
                        'html_notification' =>  $html_notification,
                        'username'          =>  $username . '  ' . $this->sdt->dateString($day)
                    ));
                }
            }
            if ($view==2){
                $this->getDI()->getMail()->send(
                    'SDT - Alertas - ' . 'Gaminoso User',
                    array(
                        "gaminoso@yahoo.es" =>  "Gaminoso"
                    ),
                    'SDT - Tareas y Notificaciones ' . $day . ' '.$conta,
                    'alerts',
                    array(
                        'html_alert' => $html_alert,
                        'html_notification' => $html_notification
                    )
                );
            }
        }
        if ($view==1 || $view==2){
            return $this->response->redirect('index/index');
        }
    }

    public function getTaskChatAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true){
            $jc = $this->jcrypt;
            $jc->go();
            if ( $request->has('id')){
                $datos      = $this->sdt->getChatTask($request->get('id'));
                $datos      =   json_encode($datos);
                $encrypted  =   $jc->encrypt($datos);
                $this->response->setJsonContent(array('encrypted'=>$encrypted));
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    public function getTaskChatScrollAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true){
            $jc = $this->jcrypt;
            $jc->go();
            if ( $request->has('id') && $request->has('range') ){
                $this->response->setJsonContent($this->sdt->getChatTaskScroll($request->get('id'),$request->get('range')));
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    public function setTaskChatAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true){
            $jc = $this->jcrypt;
            $jc->go();
            if ( $request->has('id') and $request->has('msg')){
                $meg = new TasksMessages();
                $meg->task_id = $request->get('id');
                $meg->user_id = $this->session->get("userId");
                $meg->message = $request->get("msg");
                if($meg->save()==false){
                    $this->response->setStatusCode(404,'Not Found');
                }else{
                    $this->sdt->createNotificationMessageTask($request->get('id'));
                    $this->response->setJsonContent($this->sdt->getBasicChat());
                    $this->response->setStatusCode(200,'Ok');
                    $this->response->send();
                }
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    public function getNewsMessagesTaskAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true){
            $this->response->setJsonContent($this->sdt->getNewsMessagesTask());
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }
}

