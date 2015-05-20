<?php

class HtdController extends ControllerBase
{

    public function indexAction()
    {
        $this->assets
            ->addCss('css/fullcalendar.css')
            ->addCss('css/googleColor.css')
            ->addCss('css/jquery.drop_down-plugin.css')
            ->addCss('css/jQueryUI.min.css')
            ->addCss('css/infoTask.css');

        $this->assets
            ->addJs('js/jQueryUI.js')
            ->addJs('js/calendarioUI.js')
            ->addJs('js/moment.min.js')
            ->addJs('js/rrule/rrule.js')
            ->addJs('js/rrule/nlp.js')
            ->addJs('js/sdt_general/dateFormatString.js')
            ->addJs('js/loadAnimation.js')
            ->addJs('js/htd/htd_var_global.js')
            ->addJs('js/sdt_general/repeatTask.js')
            ->addJs('js/jquery.drop_down-plugin.js')
            ->addJs('js/fullcalendar.js')
            ->addJs('js/sdt_general/sdt_new_task.js')
            ->addJs('js/htd/sdt_htd.js')
            ->addJs('js/infoTask.js');

        $user_id = $this->session->get("userId");

        // crear cinta de navegacion
        $this->addBreadcrumb('[ RM','rmregistries');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('HTD ]','htd');
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('d/m/Y');
        $path = $this->gcalendar->getAuthUrl();
        $user = Users::findFirst($user_id);
        if ($path!=""){
            if ($user->sync_calendar==1){
                // redirigir por la url
                $this->response->redirect($path, NULL);
            }
        }else{
            if ($user->sync_calendar==2){
                $user->sync_calendar = 1;
                $user->save();
            }elseif ($user->sync_calendar==1){
                $user->sync_calendar = 0;
                $user->save();
            }
        }
        $this->view->setVars(array(
            'title_view'    =>  'Htd',
            'v_session'     =>  $has_user,
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'user_id'       =>  $user_id,
            'path'          =>  $path,
            'today'         =>  $today
        ));
    }

    public function getTasksAction (){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            $day = $request->get('day');
            $this->response->setJsonContent($this->sdt->getTasksHtd($day));
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }

    public function aupdatePriorityAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            $unidTime = UnidTimes::findFirst($request->get('unit_id'));
            $unidTime->priority_id = $request->get('priority_id');
            $unidTime->save();
        }
    }

    public function UpdateFollowUpAction (){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            $unidTime_id    = $request->get('unit_id');
            $pos            = strpos($unidTime_id, '_');
            $user_id        = $this->session->get("userId");
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
            $unidTime->follow_up = $request->get('follow');
            $unidTime->save();
            $doPercent=true;
            $ut_next=null;
            if($request->get('nextday')!='null'){
                $doPercent=false;
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

            // Actualizar la alerta
            $alerts = Alerts::find('unid_time_id = '.$unidTime->id);
            foreach ($alerts as $alert) {
                if ($request->get('follow')==1){
                    $alert->was_seen        =   0;
                }else{
                    $alert->was_seen        =   1;
                }
                $alert->save();
            }
            if($doPercent){
                // Recalculo el porcentaje de la tarea
                $percent = $this->sdtPercentTask($unidTime->task_id);
                $task = Tasks::findFirst($unidTime->task_id);
                $task->percent = $percent;
                $task->save();
            }
//            $this->response->setJsonContent($percent);
            $this->response->setJsonContent($unidTime->id);
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
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

    public function delegateAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            $user_id = $this->session->get("userId");
            $user_id_actual = $request->get('person');
            $unid_id = $request->get('unit_id');
            if ($user_id_actual!=$user_id) { // No crear mas relacion si es el mismo usuario
                // Sabes si actualmente el responsable es el usuario que s epide serlo
                $unidTime = UnidTimes::findFirst($unid_id);

                $task = $unidTime->getTasks();
                if ($task->user_id!=$user_id_actual) {
                    // Creo la relacion tarea usuario
                    $userTasks = new UsersTasks();
                    $userTasks->user_id = $user_id_actual;
                    $userTasks->task_id = $task->id;
                    $userTasks->status  = 1;
                    $userTasks->save();

                    // Actualizo la tarea con su nuevo responsable
                    $task->user_id = $user_id_actual;
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

                    $this->response->setJsonContent('Ok');
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
        }
    }

    public function oauth2callbackAction(){
        $this->gcalendar->gl_callback();
    }

    public function getEventsAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isGet() == true) {
            $start = $request->get('start');
            $end = $request->get('end');
            $this->response->setJsonContent($this->gcalendar->getEventsByDay($start));
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }

    public function get_daysAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true){
            $this->response->setJsonContent($this->sdt->getDaysTasksMonth($request->get('date')));
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

}

