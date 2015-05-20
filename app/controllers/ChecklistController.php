<?php

class ChecklistController extends ControllerBase{

    public function indexAction(){
//        $this->assets
//            ->addCss('bower_components/bootstrap/dist/css/bootstrap.min.css')
//            ->addCss('css/jQueryUI.min.css')
//            ->addCss('css/checklist.css')
//            ->addCss('css/unit-time.css')
//            ->addCss('css/infoTask.css')
//            ->addCss('css/chatTask.css');
//
//        $this->assets
//            ->addJs('bower_components/jquery/dist/jquery.min.js')
//            ->addJs('bower_components/angular/angular.min.js')
//            ->addJs('bower_components/angular-route/angular-route.min.js')
//            ->addJs('bower_components/bootstrap/dist/js/bootstrap.min.js')
//            ->addJs('js/jQueryUI.js')
//            ->addJs('js/loadAnimation.js')              // Controlar animacion de loading
//            ->addJs('js/moment.min.js')                 // Manipulacion de dechas
//            ->addJs('js/rrule/rrule.js')                // ayuda a rrule
//            ->addJs('js/rrule/nlp.js')                  // Manipulacion de recurrencias SI sirvee!!!!!!
//            ->addJs('js/dateFormatString.js')           // Ayuda a moment para trabajar con los datos del ch
//            ->addJs('js/checklist/ch_var_global.js')              // Principales variables de ch
//            ->addJs('js/checklist/repeatTask.js')                 // Funcionalidad para crear recurrencias
//            ->addJs('js/checklist/sdt_checklist_live.js')         // Core del checklist
//            ->addJs('js/checklist/sdt_checklist_filtrar.js')      // funcionalidad de filtrar ch
//            ->addJs('js/infoTask.js')                   // informacion de la tarea
//            ->addJs('js/chatTask.js')                   // Peuqeño foro por tarea
//            ->addJs('js/checklist/app.js');                       // Angular

        // Archivos js y css para el checklist
        $this->assets
            ->addCss('css/jQueryUI.min.css')
            ->addCss('css/checklist.css')
            ->addCss('css/unit-time.css')
            ->addCss('css/infoTask.css')
            ->addCss('css/chatTask.css')
            ->addCss('css/tour/bootstrap-tour.min.css');

        $this->assets
            ->addJs('js/jQueryUI.js')
            ->addJs('js/loadAnimation.js')              // Controlar animacion de loading
            ->addJs('js/moment.min.js')                 // Manipulacion de dechas
            ->addJs('js/rrule/rrule.js')                // ayuda a rrule
            ->addJs('js/rrule/nlp.js')                  // Manipulacion de recurrencias SI sirvee!!!!!!
            ->addJs('js/sdt_general/dateFormatString.js')           // Ayuda a moment para trabajar con los datos del ch
            ->addJs('js/jCryption/jquery.jcryption.3.1.0.js')
            ->addJs('js/checklist/ch_var_global.js')              // Principales variables de ch
            ->addJs('js/sdt_general/repeatTask.js')                 // Funcionalidad para crear recurrencias
            ->addJs('js/checklist/sdt_checklist_live.js')         // Core del checklist
            ->addJs('js/checklist/sdt_checklist_filtrar.js')      // funcionalidad de filtrar ch
            ->addJs('js/infoTask.js')                   // informacion de la tarea
            ->addJs('js/chatTask.js')                   // Peuqeño foro por tarea
            ->addJs('js/sdt_general/sdt_new_task.js')
            ->addJs('js/checklist/sdt_checklist_init.js')
            ->addJs('js/tour/bootstrap-tour.min.js')
            ->addJs('js/tour/init_tour.js');


        // crear cinta de navegacion
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('RM','rmregistries');
        $this->addBreadcrumb('Checklist ]','checklist');

        $hoy0 = new \DateTime('America/Mexico_City');
        $hoy = $hoy0->format('d/m/Y');


        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
            $this->checklistSetting();
        }
        $this->view->setVars(array(
            'v_session' =>  $has_user,
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'hoy'       =>  $hoy,
            'title'         =>  'Checklist General',
            'title_view'    =>  'Checklist',
        ));
    }

    public function sdtChecklistJsonAction(){
        $request = $this->request;
        if($request->isGet()== true Or $request->isAjax()== true){

            $user_id = $this->session->get("userId");
            $checklist = UsersChecklists::findFirst('user_id = '.$user_id);
            $date=$this->sdt->formatDateSdt($checklist->date);
            if ($request->has('date')) {
                $date=$request->get('date');
                $checklist->date = $this->sdt->toDateStandard($date);
            }
            $range=$checklist->range;
            if ($request->has('range')) {
                $range=$request->get('range');
                $checklist->range=$range;
            }
            $checklist->save();
            $type='normal';
            if ($request->has('type')) {
                $type=$request->get('type');
            }
            $team_id=0;
            if ($request->has('team_id')) {
                $team_id=$request->get('team_id');
            }
            $project_id=0;
            if ($request->has('project_id')) {
                $project_id=$request->get('project_id');
            }
            $package_id=0;
            if ($request->has('package_id')) {
                $package_id=$request->get('package_id');
            }
            $model=0;
            if ($request->has('model')) {
                $model=$request->get('model');
            }
            $task_id=0;
            if ($request->has('task_id')) {
                $task_id=$request->get('task_id');
            }

            $results = $this->sdt->getUnitTimesOfTasks($date, $range, $task_id, $team_id, $project_id, $package_id, $model, $type);

            $this->response->setJsonContent($results);
            $this->response->setStatusCode(200,'OK');
            $this->response->send();
            exit;
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    public function homeAction(){
        $this->assets
            ->addCss('css/jQueryUI.min.css')
            ->addCss('css/checklist.css')
            ->addCss('css/unit-time.css')
            ->addCss('css/infoTask.css')
            ->addCss('css/chatTask.css');

        $this->assets
            ->addJs('js/jQueryUI.js')
            ->addJs('js/calendarioUI.js')
            ->addJs('js/jquery.tablednd.js')
            ->addJs('js/sdt_checklist.js')
            ->addJs('js/sdt_checklist_filtrar.js')
            ->addJs('js/infoTask.js')
            ->addJs('js/chatTask.js')
            ->addJs('js/moment.min.js')
            ->addJs('js/repeatTask.js');

        // crear cinta de navegacion
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('RM','rmregistries');
        $this->addBreadcrumb('Checklist ]','checklist');

        $hoy0 = new \DateTime('America/Mexico_City');
        $hoy = $hoy0->format('d/m/Y');


        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>  'CheckList',
            'v_session' =>  $has_user,
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'hoy'       =>  $hoy
        ));
    }

    /**
     * sdtChecklistView
     *
     * @throws _get_package
     * @param integer $package_id
     * @return array
     */
    public function sdtChecklistViewAction () {

        $request = $this->request;
        if($request->isAjax() == true)
        {
            $user_id = $this->session->get("userId");
            $date=0;
            if ($request->has('date')) {
                $date=$request->get('date');
            }
            $range='1';
            if ($request->has('range')) {
                $range=$request->get('range');
            }
            $type='normal';
            if ($request->has('type')) {
                $type=$request->get('type');
            }
            $team_id=0;
            if ($request->has('team_id')) {
                $team_id=$request->get('team_id');
            }
            $project_id=0;
            if ($request->has('project_id')) {
                $project_id=$request->get('project_id');
            }
            $package_id=0;
            if ($request->has('package_id')) {
                $package_id=$request->get('package_id');
            }
            $model=0;
            if ($request->has('model')) {
                $model=$request->get('model');
            }
            $task_id=0;
            if ($request->has('task_id')) {
                $task_id=$request->get('task_id');
            }

            $results = $this->sdt->getUnitTimesOfTasks($date, $range, $task_id, $team_id, $project_id, $package_id, $model, $type);

            $hoy0 = new \DateTime('America/Mexico_City');
            $hoy = $hoy0->format('d/m/Y');
            $start = $results['header']['start'];
            $ntoday = $this->sdt->diffDate( $hoy, $start);

            $fecha = explode('/', $start);
            $_mktime = mktime(0, 0, 0, $fecha[1], $fecha[0], $fecha[2]);

            // Arreglo para dar nombre, clase y color a la prioridad
            $this->view->setVars(array(
                'ntoday'    =>  $ntoday,
                'icons'     =>  $this->sdt->icons(),
                'results'   =>  $results,
                'range'     =>  $range,
                'fecha'     =>  $fecha,
                '_mktime'   =>  $_mktime,
                'nweek'     =>  date('w', $_mktime),
                'today'     =>  $hoy,
                'width'     =>  $results['header']['diff_days']*30+56+250+56,
                'user_id'   =>  $user_id
            ));

        }else{
            $this->view->disable();
        }
    }

    /**
     * sdtChangeFolloUp
     *
     * @throws _get_package
     * @param integer $package_id
     * @return array
     */
    public function sdtChangeFolloUpAction() {
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            if ($request->has('id') && $request->has('follow')) {
                // verificar si es un elemento dummy
                $unidTime_id = $request->get('id');
                $pos = strpos($unidTime_id, '_');
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d');
                $user_id = $this->session->get("userId");
                $new_ut = null;
                if ($pos !== false){
                    $pos = explode('_', $unidTime_id);
                    $task = Tasks::findFirst($pos[0]);
                    // crear unidad de tiempo
                    $newunid = new UnidTimes();
                    $newunid->task_id = $task->id;
                    $newunid->start_day = $this->sdt->toDateStandard($request->get('coldate'));
                    $newunid->priority_id = $task->priority_id;
                    $newunid->save();
                    $this->sdt->createAlert($newunid,$user_id,"newActivity",$task);
                    $unidTime_id = $newunid->id;
                }
                $unidTime = UnidTimes::findFirst($unidTime_id);
                if ($unidTime) {
                    $unidTime->follow_up = $request->get('follow');
                    if ($unidTime->save()) {
                        $ut_next = null;
                        // Actualizar el alerta cuando no se realiza ningun cambio en la unidad de tiempo.
                        $alert = $unidTime->alerts;
                        if($alert){
                            if ($request->get('follow')==1){
                                $alert->was_seen        =   0;
                            }else{
                                $alert->was_seen        =   1;
                            }
                            $alert->save();
                        }
                        if ($request->get('follow')!=1){
                            // verificar si para next_day hay una unidad de tiempo
                            if($request->get('nextday')!='null'){
                                // preguntar si ya tiene una unidad siguiente , asi no se creara
                                if(!$unidTime->next_time_r){
                                    // saber si ya existe la relacion de ese dia
                                    $un = UnidTimes::findFirst('task_id = '.$unidTime->tasks->id.' AND start_day = "'.$request->get('nextday').'"');
                                    if($un==null){
                                        // Crear la unidad de tiempo
                                        $newunid = new UnidTimes();
                                        $newunid->task_id = $unidTime->tasks->id;
                                        $newunid->start_day = $request->get('nextday');
                                        $newunid->priority_id = $unidTime->tasks->priority_id;
                                        $newunid->save();
                                        $this->sdt->createAlert($newunid,$user_id,"newActivity",$unidTime->tasks);
                                        $ut_next = array(
                                            'id' => $newunid->id,
                                            'task_id'=>$newunid->task_id,
                                            'start_day'=>$newunid->start_day
                                        );
                                        $unidTime->next_day_r   =   $request->get('nextday');
                                        $unidTime->next_time_r  =   $newunid->id;
                                    }else{
                                        $unidTime->next_time_r  =   $un->id;
                                    }
                                    $unidTime->save();
                                }
                            }
                        }

                        $day = $this->sdt->formatDateSdt($unidTime->start_day);
                        $fecha_hoy = new \DateTime('America/Mexico_City');
                        $today = $fecha_hoy->format('d/m/Y');
                        $color_class='past';
                        if ($this->sdt->diffDate($day,$today)>=0) {
                            $color_class='future';
                        }
                        $segui = $this->sdt->icons();

                        $percent='';
                        if($request->get('perc')==1){
                            $percent = $this->sdtPercentTask($unidTime->task_id);
                        }
                        $dato = array(
                            'icon'=>$segui['icon'][$request->get('follow')],
                            'color'=>$segui['class'][$color_class][$request->get('follow')],
                            'task_id'=>$unidTime->task_id,
                            'task_percent'=>$percent,
                            'unid_s'=>$unidTime->id,
                            'ut_next'=>$ut_next,
                            'old_id'=>$request->get('id')
                        );

                        $task = Tasks::findFirst($unidTime->task_id);
                        $task->percent = $percent;
                        if ($task->save()) {
                            $this->response->setJsonContent($dato);
                            $this->response->setStatusCode(200,'Ok');
                            $this->response->send();
                        }else{
                            $this->response->setStatusCode(404,'Not Found');
                        }
                    }
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
     * sdtChangeDayStart
     *
     * @throws _get_package
     * @param integer $package_id
     * @return double
     */
    public function sdtPercentTask ($task_id) {
        // Faltaria calcular el procentaje de avance de un proyecto
        $unidTimes = UnidTimes::findByTaskId($task_id);
        $count = 0;
        $valid = 0;
        foreach ($unidTimes as $unit) {
            if ($unit->follow_up<4) {
                $count+=1;
                if ($unit->follow_up==2) {
                    $valid+=1;
                }
            }
        }
        $percent='00.00';
        if ($count>0) {
            $percent = number_format((float)($valid/$count)*100, 2, '.', '');
        }
        return $percent;
    }

    /**
     * sdtUnitTimeTransfer
     *
     * @throws _get_package
     * @param integer $package_id
     * @return array
     */
    public function sdtUnitTimeTransferAction (){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true)
        {
            if ($request->has('id') && $request->has('newdate') && $request->has('task')) {
                $unidTime = UnidTimes::findFirst($request->get('id'));
                if (isset($unidTime)) {
                    // Obtener el dia de la unidad de tiempo
                    $task = explode('_', $request->get('task'));
                    $fecha = explode("/", $request->get('newdate'));
                    $day = $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' 05:05:05';
                    $fecha_hoy = new \DateTime('America/Mexico_City');
                    $today = $fecha_hoy->format('Y-m-d H:m:s');

                    // Crear la unidad de tiempo
                    $newunid = new UnidTimes();
                    $newunid->task_id = $task[0];
                    $newunid->start_day = substr($day,0,10);
                    $newunid->back_day = substr($unidTime->start_day, 0,10);
                    $newunid->task_id_back = $unidTime->id;
                    $newunid->priority_id = $unidTime->priority_id;

                    $user_id = $this->session->get("userId");
                    if ($newunid->save()) {
                        $this->sdt->createAlert($newunid,$user_id,"newActivity",$newunid->tasks);

                        $id_unit_time = $newunid->id;

                        $color_class='past';
                        if ($this->sdt->diffDate($request->get('newdate'),$fecha_hoy->format('d/m/Y'))>=0) {
                            $color_class='future';
                        }
                        $segui = $this->sdt->icons();
                        $dato = array(
                            'id'=>$request->get('id'),
                            'icon'=>$segui['icon'][4],
                            'color'=>$segui['class']['future'][4],
                            'id_new'=>$id_unit_time,
                            'icon_new'=>$segui['icon'][1],
                            'color_new'=>$segui['class'][$color_class][1],
                            'task'=>$request->get('task')
                        );

                        $unidTime->modified = $today;
                        $unidTime->follow_up = 4;
                        $unidTime->task_id_next = $id_unit_time;
                        $unidTime->next_day = $day;

                        if ($unidTime->save()) {

                            // Modificar el alerta
                            $alerts = Alerts::find('unid_time_id = '.$unidTime->id);
                            foreach ($alerts as $alert) {
                                if ($unidTime->follow_up==1){
                                    $alert->was_seen        =   0;
                                }else{
                                    $alert->was_seen        =   1;
                                }
                                $alert->save();
                            }

                            $this->response->setJsonContent($dato);
                            $this->response->setStatusCode(200,'Ok');
                            $this->response->send();
                        }
                    }else{
                        $this->response->setJsonContent('None');
                        $this->response->setStatusCode(200,'Ok');
                        $this->response->send();
                    }
                }else{
                    $this->response->setJsonContent('None');
                    $this->response->setStatusCode(200,'Ok');
                    $this->response->send();
                }
            }else{
                $this->response->setJsonContent('None');
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }
        }else{
            $this->response->setJsonContent('None');
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }

    /**
     * sdtAddUnitTime
     *
     * @throws _get_package
     * @param integer $package_id
     * @return array
     */
    public function sdtAddUnitTimeAction () {
        //$modelTask = ClassRegistry::init('Task');
        $user_id = $this->session->get("userId");

        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true)
        {
            if ($request->has('task_id') && $request->has('newdate')  && $request->has('dummy_id') ) {

                // Se obtiene los datos de la tarea donde se esta creando la unidad de tiempo
                $task = Tasks::findFirst($request->get('task_id'));
                $fecha = $request->get('newdate');
                $fecha = explode("/", $fecha);
                $day = $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' 03:03:03';

                $day2 = $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' 00:00:01';

                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d');

                $newunid = new UnidTimes();
                $newunid->task_id = $task->id;
                $newunid->start_day = substr($day, 0,10);
                $newunid->priority_id = $task->priority_id;

                if ($newunid->save()) {
//                    $this->sdt->createAlert($newunid,$user_id,"newActivity",$task);
                    $this->sdt->createNotification($newunid->tasks->user_id,$user_id,"changeUnidTime",$newunid->tasks->id,$today);
                    $id_unit_time = $newunid->id;

                    // Crear una alerta en donde indique que dia realizar la tarea
                    // esta se relaciona con la unidad de tiempo a trabajar
                    // y la tarea, que deje constancia de su relacion en caso de cambiar.
//                    if($task->package_id==0){
//                        $alert = new Alerts();
//                        $alert->user_id         =   $task->user_id;
//                        $alert->change_user_id  =   $user_id;
//                        $alert->unid_time_id    =   $id_unit_time;
//                        $alert->change_id       =   $task->id;
//                        $alert->type            =   "newActivity";
//                        $alert->date            =   substr($day2,0,10);
//                        $alert->save();
//                    }


                    // Cambiar el status de la tarea si es un proyecto
                    // Porque si una tarea de proyecto sufre una modificacion por
                    // su director, el responsble debera aceptar los nuevos cambios
                    $task_status = $this->_statusTask($request->get('task_id'));

                    $segui = $this->sdt->icons();
                    $today = $fecha_hoy->format('d/m/Y');
                    $color_class='past';
                    if ($this->sdt->diffDate($request->get('newdate'),$today)>=0) {
                        $color_class='future';
                    }
                    $percent='';
                    if($request->get('perc')==1){
                        $percent = $this->sdtPercentTask($request->get('task_id'));
                    }
                    $dato=array(
                        'id'=>$id_unit_time,
                        'color'=>$segui['class'][$color_class][1],
                        'task_id'=>$request->get('task_id'),
                        'task_percent'=>$percent,
                        'task_change_status'=>$task_status['change_status']
                    );
                    // aqui se actualiza la tarea
                    $task->percent = $percent;
                    $task->status = $task_status['status'];
                    if ($task->save()) {
                        $this->addUnitTimesChildren($request->get('task_id'),$day,$id_unit_time);
                        $this->response->setJsonContent($dato);
                        $this->response->setStatusCode(200,'Ok');
                        $this->response->send();
                    }else{
                        $this->response->setJsonContent('None1');
                        $this->response->setStatusCode(200,'Ok');
                        $this->response->send();
                    }
                }else{
                    $this->response->setJsonContent('None2');
                    $this->response->setStatusCode(200,'Ok');
                    $this->response->send();
                }
            }else{
                $this->response->setJsonContent('None3');
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }
        }else{
            $this->response->setJsonContent('None4');
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }

    public function _statusTask($task_id){
        // Si la tarea fue modificada por un usuaio diferente al responsable, entonces asume el estado en espera

        $user_id = $this->session->get("userId");
        $task = Tasks::findFirst($task_id);

        $change_status=false;
        $status = $task->status;
        if ($task->user_id!=$user_id) {
            $status = 2;
            if ($task->status!=2) {
                $change_status=true;
            }
        }
        $datos = array(
            'status'=>$status,
            'change_status'=>$change_status
        );
        return $datos;
    }

    /**
     * addUnitTimesChildren
     *
     * @throws addUnitTimesChildren
     * @param integer $task_id, $day
     * @return void
     */
    public function addUnitTimesChildren ($task_id, $day, $id_unit_time) {
        $user_id = $this->session->get("userId");
        // listar las tareas hijas
        $tasks = Tasks::findByTaskIdParent($task_id);
        // Crear las unidades de tiempo correspondientes
        foreach ($tasks as $task) {

            $newunid = new UnidTimes();
            $newunid->task_id = $task->id;
            $newunid->start_day = substr($day, 0,10);
            $newunid->unit_time_id_parent = $id_unit_time;
            $newunid->priority_id = $task->priority_id;
            $newunid->save();


            // Actualizar el porcentaje de cada tarea
            $percent = $this->sdtPercentTask($task->id);
            $task->percent = $percent;
            $task->save();

            // Para cada usuario se debe crear un alerta
//            $alert = new Alerts();
//            $alert->user_id         =   $task->user_id;
//            $alert->change_user_id  =   $user_id;
//            $alert->unid_time_id    =   $id_unit_time;
//            $alert->change_id       =   $task->id;
//            $alert->type            =   "newActivity";
//            $alert->date            =   substr($day,0,10);
//            $alert->save();
            $this->sdt->createAlert($newunid,$user_id,"newActivity",$task);

        }
    }

    /**
     * sdtDeleteUnitTime
     *
     * @throws _get_package
     * @param integer $package_id
     * @return array
     */
    public function sdtDeleteUnitTimeAction () {
        $user_id = $this->session->get("userId");

        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true)
        {
            if ($request->has('id') && $request->has('task_id')) {
                $pos = strpos($request->get('id'),'_');
                if($pos===false){
                    $unidTime = UnidTimes::findFirst($request->get('id'));
                    if (isset($unidTime->id)) {
                        // Actualizar el mensaje y la fecha
                        // Para cada usuario se debe crear un alerta
                        $alerts = $unidTime->alerts;
                        if($alerts){
                            $alerts->delete();
                        }
                        $fecha_hoy = new \DateTime('America/Mexico_City');
                        $today = $fecha_hoy->format('Y-m-d H:m:s');
//                        foreach ($alerts as $alert) {
//                            $alert->delete();
//                        }

                        $conditions = "user_id = ?1 AND type = ?2 AND change_id = ?3";
                        $parameters = array(1 => $unidTime->tasks->user_id, 2 => "deleteUnidTime", 3=>$unidTime->tasks->id);
                        $notifications = Notifications::find(array(
                            $conditions,
                            "bind" => $parameters
                        ));
                        $crear_notificacion = false;
                        if ($notifications->count()==0){
                            $crear_notificacion=true;
                        }else{
                            $notification = $notifications->getLast();
                            if ($notification->was_seen==1){
                                $crear_notificacion=true;
                            }
                        }
                        if($crear_notificacion){
                            // Si ya fue visto se crea una nueva notificacion
                            // que informe que se ha realizado un cambio de unidad de tiempo
                            // en la tarea que tiene a cargo
                            $this->sdt->createNotification($unidTime->tasks->user_id,$user_id,"changeUnidTime",$unidTime->tasks->id,$today);
                        }
                        // eliminar la tarea repeticion
                        $trm = $unidTime->tasksRepeats;
                        $task_repeat = null;
                        if($trm){
                            $trm->delete();
                            $task_repeat = $unidTime->id;
                        }

                        if ($unidTime->delete()) {
                            // cambiar el status
                            $task_status = $this->_statusTask($request->get('task_id'));

                            $percent='';
                            if($request->get('perc')==1){
                                $percent = $this->sdtPercentTask($request->get('task_id'));
                            }

                            $dato=array(
                                'id'=>$request->get('id'),
                                'task_id'=>$request->get('task_id'),
                                'task_percent'=>$percent,
                                'task_change_status'=>$task_status['change_status'],
                                'task_repeat'=>$task_repeat
                            );
                            $task = Tasks::findFirst($request->get('task_id'));
                            $task->task_percent = $percent;
                            $task->status = $task_status['status'];
                            if ($task->save()) {
                                $this->deleteUnitTimesChildren($request->get('id'),$request->get('task_id'));
                                $this->response->setJsonContent($dato);
                                $this->response->setStatusCode(200,'Ok');
                                $this->response->send();
                            }else{
                                $this->response->setJsonContent('None');
                                $this->response->setStatusCode(200,'Ok');
                                $this->response->send();
                            }
                        }else{
                            $this->response->setJsonContent('None');
                            $this->response->setStatusCode(200,'Ok');
                            $this->response->send();
                        }
                    }else{
                        $this->response->setJsonContent('None');
                        $this->response->setStatusCode(200,'Ok');
                        $this->response->send();
                    }
                }else{
                    $this->response->setJsonContent('None');
                    $this->response->setStatusCode(200,'Ok');
                    $this->response->send();
                }
            }else{
                $this->response->setJsonContent('None');
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }
        }else{
            $this->response->setJsonContent('None');
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }

    /**
     * sdtDeleteUnitTime
     *
     * @throws _get_package
     * @param integer $package_id
     * @return array
     */
    public function deleteUnitTimesChildren ($id,$task_id) {
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d H:m:s');
        $user_id = $this->session->get("userId");
        // las unidades de tiempo que tienen hijos entonces tambien actualizar
        // su dia
        $unids = UnidTimes::find(array('unit_time_id_parent = '.$id));
        // A esas unidades de tiempo eliminarlas
        $deleted=false;
        foreach ($unids as $unid) {

            $conditions = "user_id = ?1 AND type = ?2 AND change_id = ?3";
            $parameters = array(1 => $unid->tasks->user_id, 2 => "deleteUnidTime", 3=>$unid->tasks->id);
            $notifications = Notifications::find(array(
                $conditions,
                "bind" => $parameters
            ));
            $crear_notificacion = false;
            if ($notifications->count()==0){
                $crear_notificacion=true;
            }else{
                $notification = $notifications->getLast();
                if ($notification->was_seen==1){
                    $crear_notificacion=true;
                }
            }
            if($crear_notificacion){
                // Si ya fue visto se crea una nueva notificacion
                // que informe que se ha realizado un cambio de unidad de tiempo
                // en la tarea que tiene a cargo
                $this->sdt->createNotification($unid->tasks->user_id,$user_id,"deleteUnidTime",$unid->tasks->id,$today);
            }

            // Actualizar el mensaje y la fecha
            // Para cada usuario se debe crear un alerta
//            $alerts = Alerts::find('unid_time_id = '.$unid->id);
//            foreach ($alerts as $alert) {
//                $alert->delete();
//            }

            $trm = $unid->tasksRepeats;
            if($trm){
                $trm->delete();
            }

            $unid->delete();
            $deleted=true;
        }
        if ($deleted) {
            // listar las tareas hijas
            $tasks = Tasks::find(array('task_id_parent = '.$task_id));
            // Actualizar el porcentaje para cada tarea
            foreach ($tasks as $task) {
                $percent = $this->sdtPercentTask($task->id);
                $task->percent = $percent;
                $task->save();
            }
        }
    }

    /**
     * sdtChangeDayStart
     *
     * @throws _get_package
     * @param integer $package_id
     * @return array
     */
    public function sdtChangeDayStartAction () {
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            if ($request->has('id') && $request->has('newdate') && $request->has('task_id')  && $request->has('task_id_old')) {
                $unidTime = UnidTimes::findFirst($request->get('id'));
                if (count($unidTime)) {
                    $fecha = explode('/', $request->get('newdate'));
                    $day = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
                    $day0 = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
                    $unidTime->task_id = $request->get('task_id');
                    $unidTime->start_day = $day;

                    if ($unidTime->save()) {

                        $taskRepeats = $unidTime->tasksRepeats;
                        if($taskRepeats){
                            $taskRepeats->start_day = $unidTime->start_day;
                            $taskRepeats->save();
                        }
                        $user_id = $this->session->get("userId");
                        // Actualizar el mensaje y la fecha
                        // Para cada usuario se debe crear un alerta
                        $alerts = UnidTimes::find('unit_time_id_parent = '.$unidTime->id);
                        $fecha_hoy = new \DateTime('America/Mexico_City');
                        $today = $fecha_hoy->format('Y-m-d H:m:s');
                        foreach ($alerts as $alert) {
                            $alert->date        =   $day0;
                            $alert->was_seen    =   0;
                            $alert->save();


                            // Para no repetir las notificacines de cambio en la tarea
                            // Se compara con la ultima notificacion
                            // y si esta aun no ha sido vista se crea una nueva
                            $conditions = "user_id = ?1 AND type = ?2 AND change_id = ?3";
                            $parameters = array(1 => $alert->tasks->user_id, 2 => "changeUnidTime", 3=>$alert->tasks->id);
                            $notifications = Notifications::find(array(
                                $conditions,
                                "bind" => $parameters
                            ));
                            $crear_notificacion = false;
                            if ($notifications->count()==0){
                                $crear_notificacion=true;
                            }else{
                                $notification = $notifications->getLast();
                                if ($notification->was_seen==1){
                                    $crear_notificacion=true;
                                }
                            }
                            if($crear_notificacion){
                                // Notificar siempre y cuando el dueño de la tearea es diferente al editor
                                // Si ya fue visto se crea una nueva notificacion
                                // que informe que se ha realizado un cambio de unidad de tiempo
                                // en la tarea que tiene a cargo
                                $this->sdt->createNotification($unidTime->tasks->user_id,$user_id,"changeUnidTime",$unidTime->tasks->id,$today);
                            }
                        }

                        $segui = $this->sdt->icons();
                        $day2 = $request->get('newdate');
                        $fecha_hoy = new \DateTime('America/Mexico_City');
                        $today = $fecha_hoy->format('d/m/Y');
                        $color_class='past';
                        if ($this->sdt->diffDate($day2,$today)>=0) {
                            $color_class='future';
                        }
                        //Cambiarel status
                        $status_task = $this->_statusTask($request->get('task_id'));
                        $status_task_old = $this->_statusTask($request->get('task_id_old'));

                        $percent = $this->sdtPercentTask($request->get('task_id'));
                        $percent_old = $this->sdtPercentTask($request->get('task_id_old'));
                        $dato = array(
                            'id'=>$request->get('id'),
                            'color'=>$segui['class'][$color_class][$unidTime->follow_up],
                            'task_id'=>$request->get('task_id'),
                            'task_id_old'=>$request->get('task_id_old'),
                            'task_percent'=>$percent,
                            'task_percent_old'=>$percent_old,
                            'task_change_status'=>$status_task['change_status'],
                            'task_change_status_old'=>$status_task_old['change_status']
                        );
                        $task = Tasks::findFirst($request->get('task_id'));
                        $task->percent = $percent;
                        $task->status = $status_task['status'];
                        if ($task->save()) {
                            if ($request->get('task_id')!=$request->get('task_id_old')) {
                                $task2 = Tasks::findFirst($request->get('task_id_old'));
                                $task2->percent = $percent_old;
                                $task2->status = $status_task_old['status'];

                                if ($task2->save()) {
                                    // Aqui debo generar una notificacion donde se indique que
                                    // La nueva tarea adquirio la unidad de tiempo
                                    $conditions = "user_id = ?1 AND type = ?2 AND change_id = ?3";
                                    $parameters = array(1 => $task2->user_id, 2 => "changeUnidTime", 3=>$task2->id);
                                    $notifications = Notifications::find(array(
                                        $conditions,
                                        "bind" => $parameters
                                    ));
                                    $crear_notificacion = false;
                                    if ($notifications->count()==0){
                                        $crear_notificacion=true;
                                    }else{
                                        $notification = $notifications->getLast();
                                        if ($notification->was_seen==1){
                                            $crear_notificacion=true;
                                        }
                                    }
                                    if($crear_notificacion){
                                        // Notificar siempre y cuando el dueño de la tearea es diferente al editor
                                        // Si ya fue visto se crea una nueva notificacion
                                        // que informe que se ha realizado un cambio de unidad de tiempo
                                        // en la tarea que tiene a cargo
                                        $this->sdt->createNotification($task2->user_id,$user_id,"changeUnidTime",$task2->id,$today);
                                    }

                                    $this->updateUnitTimesChildren($request->get('id'),$day, $request->get('task_id'), $request->get('task_id_old'));

                                    $this->response->setJsonContent($dato);
                                    $this->response->setStatusCode(200,'Ok');
                                    $this->response->send();
                                }else{
                                    $this->response->setJsonContent('None');
                                    $this->response->setStatusCode(200,'Ok');
                                    $this->response->send();
                                }
                            }else{
                                $this->updateUnitTimesChildren($request->get('id'),$day, $request->get('task_id'), $request->get('task_id_old'));

                                $this->response->setJsonContent($dato);
                                $this->response->setStatusCode(200,'Ok');
                                $this->response->send();
                            }
                        }else{
                            $this->response->setJsonContent('None');
                            $this->response->setStatusCode(200,'Ok');
                            $this->response->send();
                        }
                    }else{
                        $this->response->setJsonContent('None');
                        $this->response->setStatusCode(200,'Ok');
                        $this->response->send();
                    }
                }else{
                    $this->response->setJsonContent('None');
                    $this->response->setStatusCode(200,'Ok');
                    $this->response->send();
                }
            }else{
                $this->response->setJsonContent('None');
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }
        }else{
            $this->response->setJsonContent('None');
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }

    /**
     * updateUnitTimesChildren
     *
     * @throws updateUnitTimesChildren
     * @param integer $id
     * @return void
     */
    public function updateUnitTimesChildren($id, $day, $task_id, $task_id_old){
        // las unidades de tiempo que tienen hijos entonces tambien actualizar
        // su dia
        $unidTimes = UnidTimes::find('unit_time_id_parent = '.$id);
        // listar las tareas hijas, el usuario es el puente
        $tasks = Tasks::find('task_id_parent = '.$task_id);
        $task = array();
        foreach ($tasks as $tak) {
            $task[$tak->user_id]=$tak->id;
        }

        $user_id = $this->session->get("userId");
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d H:m:s');

        // A esas unidades de tiempo actualizarlas con el nuevo dia
        foreach ($unidTimes as $unid) {
            $unid->task_id = $task[$unid->tasks->user_id];
            $unid->start_day = $day;
            $unid->save();

            $taskRepeats = $unid->tasksRepeats;
            if($taskRepeats){
                $taskRepeats->start_day = $day;
                $taskRepeats->save();
            }

            // Para no repetir las notificacines de cambio en la tarea
            // Se compara con la ultima notificacion
            // y si esta aun no ha sido vista se crea una nueva
            $conditions = "user_id = ?1 AND type = ?2 AND change_id = ?3";
            $parameters = array(1 => $unid->tasks->user_id, 2 => "changeUnidTime", 3=>$unid->tasks->id);
            $notifications = Notifications::find(array(
                $conditions,
                "bind" => $parameters
            ));
            $crear_notificacion = false;
            if ($notifications->count()==0){
                $crear_notificacion=true;
            }else{
                $notification = $notifications->getLast();
                if ($notification->was_seen==1){
                    $crear_notificacion=true;
                }
            }
            if($crear_notificacion){
                // Notificar siempre y cuando el dueño de la tearea es diferente al editor
                // Si ya fue visto se crea una nueva notificacion
                // que informe que se ha realizado un cambio de unidad de tiempo
                // en la tarea que tiene a cargo
                $this->sdt->createNotification($unid->tasks->user_id,$user_id,"changeUnidTime",$unid->tasks->id,$today);
            }


            ///////////////////////////////////////////////////////////////////
            if ($task_id!=$task_id_old) {
                // Actualizar el porcentaje de cada tarea
                $percent = $this->sdtPercentTask($unid->tasks->id);
                $unid->tasks->percent = $percent;
                if ($unid->tasks->user_id!=$user_id){
                    $unid->tasks->status=2;
                }
                $unid->tasks->save();

                $percent = $this->sdtPercentTask($task[$unid->tasks->user_id]);
                $task2 = Tasks::findFirst($task[$unid->tasks->user_id]);
                $task2->percent = $percent;
                if ($task2>user_id!=$user_id){
                    $task2->status=2;
                }
                $task2->save();

                // Crear la alerta de
                // Si se cambia de tarea la unidad de tiempo entonces
                // se debe informar en esa otra tarea que ha sido cambiada
                $alert = new Alerts();
                $alert->user_id         =   $task2->user_id;
                $alert->change_user_id  =   $user_id;
                $alert->unid_time_id    =   $unid->id;
                $alert->change_id       =   $task2->id;
                $alert->type            =   "newActivity";
                $alert->date            =   substr($day,0,10);
                $alert->save();

                // Si ya fue visto se crea una nueva notificacion
                // que informe que se ha realizado un cambio de unidad de tiempo
                // en la tarea que tiene a cargo
                $this->sdt->createNotification($task2->user_id,$user_id,"changeUnidTime",$task2->id,$today);
            }
        }
    }

    public function delegatesToMeAction(){
        $this->assets
            ->addCss('css/jQueryUI.min.css')
            ->addCss('css/checklist.css')
            ->addCss('css/unit-time.css')
            ->addCss('css/infoTask.css')
            ->addCss('css/chatTask.css');

        $this->assets
            ->addJs('js/jQueryUI.js')
            ->addJs('js/calendarioUI.js')
            ->addJs('js/jquery.tablednd.js')
            ->addJs('js/sdt_checklist.js')
            ->addJs('js/sdt_checklist_filtrar.js')
            ->addJs('js/infoTask.js')
            ->addJs('js/chatTask.js')
            ->addJs('js/moment.min.js');

        // crear cinta de navegacion
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('RM','rmregistries');
        $this->addBreadcrumb('Checklist ]','checklist');
        $this->addBreadcrumb('Tareas delegadas a mi','checklist');

        $hoy0 = new \DateTime('America/Mexico_City');
        $hoy = $hoy0->format('d/m/Y');


        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>  'CheckList',
            'v_session' =>  $has_user,
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'hoy'       =>  $hoy,
            'type_delegate'      =>  'tome'
        ));
    }

    public function myDelegatesAction(){
        $this->assets
            ->addCss('css/jQueryUI.min.css')
            ->addCss('css/checklist.css')
            ->addCss('css/unit-time.css')
            ->addCss('css/infoTask.css')
            ->addCss('css/chatTask.css');

        $this->assets
            ->addJs('js/jQueryUI.js')
            ->addJs('js/calendarioUI.js')
            ->addJs('js/jquery.tablednd.js')
            ->addJs('js/sdt_checklist.js')
            ->addJs('js/sdt_checklist_filtrar.js')
            ->addJs('js/infoTask.js')
            ->addJs('js/chatTask.js')
            ->addJs('js/moment.min.js');

        // crear cinta de navegacion
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('RM','rmregistries');
        $this->addBreadcrumb('Checklist ]','checklist');
        $this->addBreadcrumb('Mis tareas delegadas','checklist');

        $hoy0 = new \DateTime('America/Mexico_City');
        $hoy = $hoy0->format('d/m/Y');


        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>  'CheckList',
            'v_session' =>  $has_user,
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'hoy'       =>  $hoy,
            'type_delegate'      =>  'fromme'
        ));
    }
    public function myProjectsAction(){
        $this->assets
            ->addCss('css/jQueryUI.min.css')
            ->addCss('css/checklist.css')
            ->addCss('css/unit-time.css')
            ->addCss('css/infoTask.css')
            ->addCss('css/chatTask.css');

        $this->assets
            ->addJs('js/jQueryUI.js')
            ->addJs('js/calendarioUI.js')
            ->addJs('js/jquery.tablednd.js')
            ->addJs('js/sdt_checklist.js')
            ->addJs('js/sdt_checklist_filtrar.js')
            ->addJs('js/infoTask.js')
            ->addJs('js/chatTask.js')
            ->addJs('js/moment.min.js');

        // crear cinta de navegacion
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('RM','rmregistries');
        $this->addBreadcrumb('Checklist ]','checklist');
        $this->addBreadcrumb('Mis proyectos','checklist');

        $hoy0 = new \DateTime('America/Mexico_City');
        $hoy = $hoy0->format('d/m/Y');


        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>  'CheckList',
            'v_session' =>  $has_user,
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'hoy'       =>  $hoy,
            'type_delegate'      =>  'mypro'
        ));
    }

    public function projectsAction(){
        $this->assets
            ->addCss('css/jQueryUI.min.css')
            ->addCss('css/checklist.css')
            ->addCss('css/unit-time.css')
            ->addCss('css/infoTask.css')
            ->addCss('css/chatTask.css');

        $this->assets
            ->addJs('js/jQueryUI.js')
            ->addJs('js/calendarioUI.js')
            ->addJs('js/jquery.tablednd.js')
            ->addJs('js/sdt_checklist.js')
            ->addJs('js/sdt_checklist_filtrar.js')
            ->addJs('js/infoTask.js')
            ->addJs('js/chatTask.js')
            ->addJs('js/moment.min.js');

        // crear cinta de navegacion
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('RM','rmregistries');
        $this->addBreadcrumb('Checklist ]','checklist');
        $this->addBreadcrumb('Proyectos','checklist');

        $hoy0 = new \DateTime('America/Mexico_City');
        $hoy = $hoy0->format('d/m/Y');


        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>  'CheckList',
            'v_session' =>  $has_user,
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'hoy'       =>  $hoy,
            'type_delegate'      =>  'proje'
        ));
    }

    public function addRepeatAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            // verificar si hay que actualizar o crear
            $opt = json_decode($request->get('opt'));

            $datfech1 = $this->sdt->toDateStandard($opt->start_day);
            $datfech2 = $this->sdt->toDateStandard($opt->end_day);
            $datfech3 = $opt->next_day;
            $unid_time_id = $opt->unid_time_id;
            $old_unid_t = null;
            $user_id = $this->session->get("userId");

            $pos = strpos($unid_time_id,'_');
            if ($pos !== false){
                $arr = explode("_",$unid_time_id);
                $old_unid_t = $unid_time_id;

                $tr_old = TasksRepeats::findFirst('task_id = '.$arr[0].' AND unid_time_id = '.$arr[1]);
                // reducir un dia
                $tr_old->end_day = $this->sdt->toDateStandard($this->sdt->_operateDate($opt->start_day,-1));
                $tr_old->save();
                // crear una unidad de tiempo relacionado
                $task = Tasks::findFirst($opt->task_id);
                $newunid = new UnidTimes();
                $newunid->task_id       =   $opt->task_id;
                $newunid->start_day     =   $opt->start_day;
                $newunid->priority_id   =   $task->priority_id;
                if($newunid->save()==false){
                    $this->createListError($newunid);
                    echo('task_id = '.$arr[0].' AND unid_time_id = '.$arr[1]);
                    exit;
                }
                // Crear la alerta!!!!
                $this->sdt->createAlert($newunid,$user_id,"newActivity",$newunid->tasks);

                $unid_time_id = $newunid->id;

                // crear para cada tarea hija una unidad de tiempo relacionada
                $tasks = Tasks::find('task_id_parent = '.$opt->task_id);
                foreach ($tasks as $tak) {
                    $newunid_child = new UnidTimes();
                    $newunid_child->task_id = $tak->id;
                    $newunid_child->start_day = $opt->start_day;
                    $newunid_child->priority_id = $tak->priority_id;
                    $newunid_child->unit_time_id_parent = $newunid->id;
                    $newunid_child->save();

                    $this->sdt->createAlert($newunid_child,$user_id,"newActivity",$tak);
                }
            }

            $tr = TasksRepeats::findFirst('task_id = '.$opt->task_id.' AND unid_time_id = '.$unid_time_id);
            $NoHasCreated=true;

            if($datfech3){
                $datfech3 = $this->sdt->toDateStandard($opt->next_day);
            }
            if(!$tr){
                $tr = new TasksRepeats();
                $tr->task_id        =   $opt->task_id;
                $tr->unid_time_id   =   $unid_time_id;

                // Crear para cada unidad de tiempo repetitiva por cada unidad de tiempo hija
                $unids = UnidTimes::find(array('unit_time_id_parent = '.$unid_time_id));
                foreach ($unids as $unid) {
                    $tr_h = new TasksRepeats();
                    $tr_h->task_id              =   $unid->task_id;
                    $tr_h->unid_time_id         =   $unid->id;
                    $tr_h->options              =   $opt->options;
                    $tr_h->each_period          =   $opt->each_period;
                    $tr_h->day_L                =   $opt->day_L;
                    $tr_h->day_M                =   $opt->day_M;
                    $tr_h->day_X                =   $opt->day_X;
                    $tr_h->day_J                =   $opt->day_J;
                    $tr_h->day_V                =   $opt->day_V;
                    $tr_h->day_S                =   $opt->day_S;
                    $tr_h->day_D                =   $opt->day_D;
                    $tr_h->month_week           =   $opt->month_week;
                    $tr_h->start_day            =   $datfech1;
                    $tr_h->N_R_T                =   $opt->N_R_T;
                    $tr_h->repeat_interval      =   $opt->repeat_interval;
                    $tr_h->end_day              =   $datfech2;
                    $tr_h->day_position         =   $opt->day_position;
                    $tr_h->save();

                    $unid->next_day_r           =   $datfech3;
                    $unid->save();
                }
                $NoHasCreated=false;
            }
            $tr->options            =   $opt->options;
            $tr->each_period        =   $opt->each_period;
            $tr->day_L              =   $opt->day_L;
            $tr->day_M              =   $opt->day_M;
            $tr->day_X              =   $opt->day_X;
            $tr->day_J              =   $opt->day_J;
            $tr->day_V              =   $opt->day_V;
            $tr->day_S              =   $opt->day_S;
            $tr->day_D              =   $opt->day_D;
            $tr->month_week         =   $opt->month_week;
            $tr->start_day          =   $datfech1;
            $tr->N_R_T              =   $opt->N_R_T;
            $tr->repeat_interval    =   $opt->repeat_interval;
            $tr->end_day            =   $datfech2;
            $tr->day_position       =   $opt->day_position;

            if($opt->built_type==2){
                // buscar unidades de tiempo y periodicas mayor a ese tiempo y eliminarlas.
                $unidTimes = UnidTimes::find('task_id = '.$opt->task_id.' AND start_day > '. $tr->start_day .' AND id != '.$unid_time_id);
                foreach ($unidTimes as $unidTime) {
                    $unidTime->delete();
                }
                $tasksRepeats = TasksRepeats::find('task_id = '.$opt->task_id.' AND start_day >'.$tr->start_day .'AND unid_time_id != '.$unid_time_id);
                foreach ($tasksRepeats as $taskRepeat) {
                    $taskRepeat->delete();
                }
            }else{
                $ut1 = UnidTimes::findFirst($tr->unid_time_id);
                $estar = true;
                $noDelete = true;
                while ($estar){
                    $ut0 = $ut1;
                    if($ut1->next_time_r){ // tarea repetitiva tiene un dia siguiente?
                        $ut2 = UnidTimes::findFirst($ut1->next_time_r); // obtener ese dia siguiente dia
                        $ut0->next_time_r=null; // decir que ya no tiene mas siguiente
                        $ut1 = $ut2;
                    }else{
                        $estar=false; // terminar
                    }
                    if($noDelete){
                        $noDelete=false;
                        $ut0->save(); // almancenar el cambio
                    }else{
                        $ut0->save(); // almancenar el cambio
                    }
                }
            }

            if($NoHasCreated){
                // Actualizar las tareas repetitivas hijas
                $unids = UnidTimes::find(array('unit_time_id_parent = '.$unid_time_id));
                foreach ($unids as $unid) {
                    $unidRepeat =  $unid->tasksRepeats;//   TasksRepeats::findFirst('task_id = '.$unid->task_id.' AND unid_time_id = '.$unid->id);
                    if($unidRepeat){
                        $unidRepeat->options            =   $opt->options;
                        $unidRepeat->each_period        =   $opt->each_period;
                        $unidRepeat->day_L              =   $opt->day_L;
                        $unidRepeat->day_M              =   $opt->day_M;
                        $unidRepeat->day_X              =   $opt->day_X;
                        $unidRepeat->day_J              =   $opt->day_J;
                        $unidRepeat->day_V              =   $opt->day_V;
                        $unidRepeat->day_S              =   $opt->day_S;
                        $unidRepeat->day_D              =   $opt->day_D;
                        $unidRepeat->month_week         =   $opt->month_week;
                        $unidRepeat->start_day          =   $datfech1;
                        $unidRepeat->N_R_T              =   $opt->N_R_T;
                        $unidRepeat->repeat_interval    =   $opt->repeat_interval;
                        $unidRepeat->end_day            =   $datfech2;
                        $unidRepeat->day_position       =   $opt->day_position;
                        $unidRepeat->next_day           =   $datfech3;
                    }
                }

                if($opt->built_type==2){
                    $tasks = Tasks::find('task_id_parent = '.$tr->task_id);
                    foreach ($tasks as $tak) {
                        // buscar unidades de tiempo y periodicas mayor a ese tiempo y eliminarlas.
                        $unidTimes = $tak->getUnidTimes('start_day > '. $datfech1);
                        foreach ($unidTimes as $unidTime) {
                            if($unidTime->unit_time_id_parent!=$unid_time_id){
//                                $alert = $unidTime->alerts();
//                                $alert->was_seen=1;
//                                $alert->save();
                                $unidTime->delete();
                            }
                        }
                        $tasksRepeats = $tak->getTasksRepeats('start_day >'.$datfech1);
                        foreach ($tasksRepeats as $taskRepeat) {
                            if($taskRepeat->unidTimes->unit_time_id_parent!=$unid_time_id){
                                $taskRepeat->delete();
                            }
                        }
                    }
                }
            }

//            $this->response->setJsonContent($opt);
//            $this->response->setStatusCode(200,'Ok');
//            $this->response->send();

            if($tr->save()){
                $tr->built_type = $opt->built_type;
                $tr->old_unid_t = $old_unid_t;
                // Actualizo la unidad de tiempo a sin seguimiento
                $uni = UnidTimes::findFirst($unid_time_id);
                $uni->follow_up = 1;
                $uni->next_day_r           =   $datfech3;
                $uni->next_time_r          =   null;
                $uni->save();
                $alert = $uni->alerts;
                if($alert){
                    $alert->was_seen =0;
                    $alert->save();
                }


                $this->response->setJsonContent($tr);
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }else{
                // $this->createListError($tr);
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    public function getRepeatConfAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            $ut_id = $request->get('id');
            $tr="None";
            if(strpos($ut_id,'_')===false){
                $tr = TasksRepeats::findFirst('unid_time_id = '.$ut_id);
                if(!$tr){
                    $tr="None";
                }else{
                    $tr = $this->sdtRuler->createRecurrentEvent($tr,new DateTime($tr->start_day),0,new DateTime('2015-05-11'));
                }
            }

            $this->response->setJsonContent($tr);
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }
}

