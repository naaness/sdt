<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function createAlerts($add = true){
        if($add){
            $this->assets
                ->addJs('js/sdt_five_steps.js')
                ->addJs('js/alert.js');
        }
//        //Pregruntar si el usuario esta logeado
        if ($this->session->has("userId")){
            // Obtener el id del usuario
            $user_id = $this->session->get("userId");
//            // Obtener todas las alertas que pertenescan al usuario
//            $allalerts = Alerts::findByUserId($user_id);
//            if ($allalerts->count()>0){
//                // Obtener todas las alertas que el usuario no ha visto, contarlas
//                $fecha_hoy = new \DateTime('America/Mexico_City');
//                $today = $fecha_hoy->format('Y-m-d'). ' 23:59:59';
//
//
//                // Enviar las dos variables a la vista
//                $this->view->setVars(array(
//                    'itemsalert'        =>  $this->htmlAlerts($user_id,$today),
//                    'numitemsalert'     =>  $this->countAlerts($user_id,$today)
//                ));
//            }
            $user = Users::findFirst($user_id);
            $this->view->setVars(array(
                'type_role'        =>  $user->role,
            ));
        }
    }

    public function countAlerts($user_id, $today){
//        $phql = 'SELECT
//                Alerts.*
//                FROM Alerts
//                INNER JOIN Tasks ON (Tasks.status = 1 AND Tasks.id = Alerts.change_id)
//                WHERE
//                Alerts.user_id = '.$user_id.'
//                AND Alerts.date <= "'.$today.'"
//                AND Alerts.was_seen = 0 ';
        $phql = 'SELECT
                UnidTimes.*
                FROM UnidTimes
                INNER JOIN Tasks ON (Tasks.status = 1 AND Tasks.id = UnidTimes.task_id)
                INNER JOIN UsersTasks ON (UsersTasks.task_id = Tasks.id)
                WHERE
                Tasks.user_id = '.$user_id.'
                AND UnidTimes.start_day <= "'.$today.'"
                AND UnidTimes.start_day != "0000-00-00"
                AND UnidTimes.follow_up = 1
                AND UsersTasks.user_id = '.$user_id.'
                AND UsersTasks.status=1';
        $numitemsalert = $this->modelsManager->executeQuery($phql);
        return $numitemsalert->count();
    }

    public function htmlAlerts($user_id, $today){
        // Obtener las ultimas 10 alertas que son menoes al dia de hoy
//        $phql = 'SELECT
//                Alerts.*
//                FROM Alerts
//                INNER JOIN Tasks ON (Tasks.status = 1 AND Tasks.id = Alerts.change_id)
//                WHERE
//                Alerts.user_id = '.$user_id.'
//                AND Alerts.date <= "'.$today.'"
//                ORDER BY Alerts.was_seen ASC, Alerts.id DESC LIMIT 10 ';
        $phql = 'SELECT
                UnidTimes.*
                FROM UnidTimes
                INNER JOIN Tasks ON (Tasks.status = 1 AND Tasks.id = UnidTimes.task_id)
                WHERE
                Tasks.user_id = '.$user_id.'
                AND UnidTimes.start_day <= "'.$today.'"
                ORDER BY UnidTimes.start_day DESC, UnidTimes.follow_up ASC LIMIT 10 ';
        $pastalerts = $this->modelsManager->executeQuery($phql);

        // Crear el html par alas ultimas 10 alertas
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/';
        $html_alert="";
        foreach ($pastalerts as $alert) {
//            $msg = "";
//            if ($alert->type == "newActivity") {
//                $task = Tasks::findFirst($alert->change_id);
//                $url0 = "checklist";
//                $msg = $task->name;
//            }elseif ($alert->type == "changeActivity") {
//                $task = Tasks::findFirst($alert->change_id);
//                $url0 = "checklist";
//                $msg = $task->name . " ha sido delegado";
//            }
            $task = $alert->tasks;
            $url0 = "checklist";
            $msg = $task->name;
            if ($msg!=""){
                $fecha = explode(' ',$alert->start_day);
                $fecha = explode('-',$fecha[0]);
                $fecha = $fecha[2].'/'.$fecha[1].'/'.$fecha[0];
                if($alert->follow_up!=1)
                    $html_alert.= '<li><a href="'.$url.$url0.'">'.$fecha.' Realizar: '.$msg.'</a></li>';
                else{
                    $html_alert.= '<li><a href="'.$url.$url0.'">'.$fecha.' Realizar: <strong>'.$msg.'</strong></a></li>';
                }
            }
        }
        if ($html_alert!=""){
            $html_alert.= '<li class="divider"></li>';
            $html_alert.= '<li><a href="'.$url.'alerts"><div class="text-center">Ver todo</div></a></li>';
        }
        return $html_alert;
    }

    public function createNotifications(){
//        //Pregruntar si el usuario esta logeado
//        if ($this->session->has("userId")){
//            // Obtener el id del usuario
//            $user_id = $this->session->get("userId");
//            // Obtener todas las alertas que pertenescan al usuario
//            // Obtener todas las alertas que el usuario no ha visto, contarlas
//            $fecha_hoy = new \DateTime('America/Mexico_City');
//            $today = $fecha_hoy->format('Y-m-d'). ' 23:59:59';
//
//            $user = Users::findFirst($user_id);
//
//           // Enviar las dos variables a la vista
//            $this->view->setVars(array(
//                'itemsnotification'         =>  $this->htmlNotifications($user_id),
//                'con_notification'          =>  $this->countNotifications($user_id,$today),
//                'type_role'                 =>  $user->role,
//            ));
//        }
    }

    public function countNotifications($user_id, $today){
        $phql = 'SELECT
                Notifications.*
                FROM Notifications
                WHERE
                Notifications.user_id = '.$user_id.'
                AND Notifications.date <= "'.$today.'"
                AND Notifications.was_seen = 0 ';
        $numitemsalert = $this->modelsManager->executeQuery($phql);
        return $numitemsalert->count();
    }

    public function htmlNotifications($user_id,$doUrl=true){
        // Obtener las ultimas 10 alertas que son menoes al dia de hoy
        $br = " ";
        if ($doUrl){
            $br="<br>";
            $phql = 'SELECT
                Notifications.*
                FROM Notifications
                WHERE
                Notifications.user_id = '.$user_id.'
                ORDER BY Notifications.was_seen ASC, Notifications.id DESC LIMIT 10';
            $pastalerts = $this->modelsManager->executeQuery($phql);
        }else{
            $phql = 'SELECT
                Notifications.*
                FROM Notifications
                WHERE
                Notifications.user_id = '.$user_id.'
                AND Notifications.was_seen = 0
                ORDER BY Notifications.was_seen ASC, Notifications.id DESC ';
            $pastalerts = $this->modelsManager->executeQuery($phql);
        }


        // Crear el html par alas ultimas 10 alertas
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/';
        $html_alert="";
        foreach ($pastalerts as $alert) {
            $user0 = Users::findFirst($alert->change_user_id);
            $url0="";
            $msg="";
            if ($alert->type == "newPost") {
                $dato = Posts::findFirst($alert->change_id);
                if($doUrl){
                    $url0 = 'posts/view/'.$dato->id;
                }
                $msg = '<strong>'.$user0->username.'</strong> ha agregado un nuevo'.$br.'comentario al foro <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "newTeam") {
                $dato = Teams::findFirst($alert->change_id);
                if($doUrl){
                    $url0 = 'teams/view/'.$dato->id;
                }
                $msg = '<strong>'.$user0->username.'</strong> te ha agregado'.$br.'al equipo <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "editTeam") {
                $dato = Teams::findFirst($alert->change_id);
                if($doUrl){
                    $url0 = 'teams/view/'.$dato->id;
                }
                $msg = '<strong>'.$user0->username.'</strong> ha modificado'.$br.'al equipo <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deleteTeam") {
                $dato = Teams::findFirst($alert->change_id);
                if($doUrl){
                    $url0 = 'teams/view/'.$dato->id;
                }
                $msg = '<strong>'.$user0->username.'</strong> te ha sacado'.$br.'del equipo <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "newProject") {
                $dato = Projects::findFirst($alert->change_id);
                if($doUrl){
                    $url0 = 'tasks/index/'.$dato->teams->id.'/'.$dato->id;
                }
                $msg = '<strong>'.$user0->username.'</strong> te ha agregado'.$br.'al proyecto <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "editProject") {
                $dato = Projects::findFirst($alert->change_id);
                if($doUrl){
                    $url0 = 'teams/view/'.$dato->teams->id.'/'.$dato->id;
                }
                $msg = '<strong>'.$user0->username.'</strong> ha modificado'.$br.'al proyecto <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deleteProject") {
                $dato = Projects::findFirst($alert->change_id);
                if($doUrl){
                    $url0 = 'teams/view/'.$dato->teams->id.'/'.$dato->id;
                }
                $msg = '<strong>'.$user0->username.'</strong> te ha sacado'.$br.'del proyecto <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "delegate") {
                $dato = Tasks::findFirst($alert->change_id);
                if($doUrl){
                    $url0 = 'checklist';
                }
                $msg = '<strong>'.$user0->username.'</strong> te ha pedido realizar'.$br.'la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "accepted") {
                $dato = Tasks::findFirst($alert->change_id);
                if($doUrl){
                    $team_id =0;
                    if ($dato->project_id>0){
                        $team_id = $dato->projects->team_id;
                    }elseif ($dato->package_id>0){
                        $team_id = $dato->packages->team_id;
                    }
                    $url0 = 'tasks/view/'.$team_id.'/'.$dato->project_id.'/'.$dato->package_id.'/0/'.$dato->id;
                }
                $msg = '<strong>'.$user0->username.'</strong> ha aceptado'.$br.'la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "rejected") {
                $dato = Tasks::findFirst($alert->change_id);
                if($doUrl){
                    $team_id =0;
                    if ($dato->project_id>0){
                        $team_id = $dato->projects->team_id;
                    }elseif ($dato->package_id>0){
                        $team_id = $dato->packages->team_id;
                    }
                    $url0 = 'tasks/view/'.$team_id.'/'.$dato->project_id.'/'.$dato->package_id.'/0/'.$dato->id;
                }
                $msg = '<strong>'.$user0->username.'</strong> ha rechazado'.$br.'la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "reactived") {
                $dato = Tasks::findFirst($alert->change_id);
                if($doUrl){
                    $url0 = 'checklist';
                }
                $msg = '<strong>'.$user0->username.'</strong> ha reactivado'.$br.'la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "newChecklist") {
                $dato = Packages::findFirst($alert->change_id);
                if($doUrl){
                    $url0 = 'tasks/index/'.$dato->teams->id.'/0/'.$dato->id;
                }
                $msg = '<strong>'.$user0->username.'</strong> ha agregado una tarea'.$br.'al checklist <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "changeUnidTime") {
                $dato = Tasks::findFirst($alert->change_id);
                if($doUrl){
                    $team_id =0;
                    if ($dato->project_id>0){
                        $team_id = $dato->projects->team_id;
                    }elseif ($dato->package_id>0){
                        $team_id = $dato->packages->team_id;
                    }
                }
                $url0 = 'tasks/view/'.$team_id.'/'.$dato->project_id.'/'.$dato->package_id.'/0/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> ha modificado'.$br.'la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deleteTask") {
                $dato = Tasks::findFirst($alert->change_id);
                $url0 = 'index';
                $msg = '<strong>'.$user0->username.'</strong> ha eliminado'.$br.'la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deleteProject") {
                $dato = Projects::findFirst($alert->change_id);
                $url0 = 'index';
                $msg = '<strong>'.$user0->username.'</strong> ha eliminado'.$br.'el proyecto <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deletePackage") {
                $dato = Packages::findFirst($alert->change_id);
                $url0 = 'index';
                $msg = '<strong>'.$user0->username.'</strong> ha eliminado'.$br.'el proyecto <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deleteTeam") {
                $dato = Teams::findFirst($alert->change_id);
                $url0 = 'index';
                $msg = '<strong>'.$user0->username.'</strong> ha eliminado'.$br.'el equipo <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "messageTask") {
                $dato = Tasks::findFirst($alert->change_id);
                $url0 = 'checklist';
                $msg = '<strong>'.$user0->username.'</strong> ha comentado'.$br.'la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "dislikePost") {
                $dato = Posts::findFirst($alert->change_id);
                if($dato->parent_post>0){
                    $dato = Posts::findFirst($dato->parent_post);
                }
                $url0 = 'posts/view/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> no le ha gustado'.$br.'un comentario tuyo';
            } elseif ($alert->type == "likePost") {
                $dato = Posts::findFirst($alert->change_id);
                if($dato->parent_post>0){
                    $dato = Posts::findFirst($dato->parent_post);
                }
                $url0 = 'posts/view/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> le ha gustado'.$br.'un comentario tuyo';
            }



            if($doUrl){
                $html_alert.= '<li><a href="'.$url.$url0.'">'.$msg.'</a></li>';
            }else{
                $html_alert.='<tr style="font-family:Arial;font-size:14px;font-weight:normal;font-weight:normal;color:#666666">';
                $html_alert.='<td height="50px" style="height:50px;border-right:2px solid #ffffff;padding-left:15px;border-bottom:2px solid #ffffff;background-color:#ededed" colspan="3">';
                $html_alert.=$msg;
                $html_alert.='</td>';
                $html_alert.='</tr>';
            }
        }
        if($doUrl){
            $html_alert.= '<li class="divider"></li>';
            $html_alert.= '<li><a href="'.$url.'notifications"><div class="text-center">Ver todo</div></a></li>';
        }


        return $html_alert;
    }

    private $_breadcrumb = array();
    public function addBreadcrumb($name="",$url="#"){
        array_push($this->_breadcrumb, array('name'=>$name, 'url'=>$url));
    }

    public function createHtmlBreadcrumb(){
        $html = '<ol class="breadcrumb">';
        $cont = count($this->_breadcrumb);
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/';
        for ($i=0; $i < $cont; $i++) {
            if ( $i == ($cont - 1) ){
                $html .= '<li class="active">'.$this->_breadcrumb[$i]['name'].'</li>';
            }else{
                $html .= '<li><a href="'.$url.$this->_breadcrumb[$i]['url'].'">'.$this->_breadcrumb[$i]['name'].'</a></li>';
            }
        }
        $html .= '</ol>';

        return $html;
    }

    public function createListError($form){
        $html = '<ul>';
        foreach ($form->getMessages() as $message)
        {
            $html.= '<li>'.$message.'</li>';

        }
        $html.='</ul>';
        $this->flash->error($html);
    }

    public function deleteProject($project_id){
        $user_id = $this->session->get("userId");
        $project = Projects::findFirst($project_id);
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d H:m:s');
        // obtener cada tarea del proyecto
        foreach ($project->tasks as $task){
            // Inhabilitar alerta
            $alerts =  Alerts::find('change_id = '.$task->id);
            foreach ($alerts as $alert){
                $alert->was_seen = 1;
                $alert->save();
            }
            // Inhabilitar la tarea
            $task->status = 0;
            $task->save();
            // no duplicar notificacion para los usuarios
            $users = array();
            foreach (UsersTasks::find('task_id = '.$task->id) as $userstask) {
                $userstask->status=0;
                $userstask->save();
                if (!isset($users[$userstask->user_id])){
                    $users[$userstask->user_id]= $userstask->user_id;
                    $this->sdt->createNotification($userstask->user_id,$user_id,"deleteTask",$task->id,$today);
                }
            }
        }
        // Informar a cada usuario el porque se elimino la tarea
        // y es la eliminacion del proyecto
        foreach (UsersProjects::find('project_id = '.$project_id) as $usersproject) {
            $this->sdt->createNotification($usersproject->user_id,$user_id,"deleteProject",$project_id,$today);
        }
        $project->status = 0;
        $project->save();
    }

    public function deletePackage($package_id){
        $user_id = $this->session->get("userId");
        $package = Packages::findFirst($package_id);
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d H:m:s');
        $tasks = Tasks::find('package_id = '.$package_id);
        foreach ($tasks as $task) {
            // Inhabilitar alerta
            $alerts =  Alerts::find('change_id = '.$task->id);
            foreach ($alerts as $alert){
                $alert->was_seen = 1;
                $alert->save();
            }
            // no duplicar notificacion para los usuarios
            $users = array();
            foreach (UsersTasks::find('task_id = '.$task->id) as $userstask) {
                $userstask->status=0;
                $userstask->save();
                if (!isset($users[$userstask->user_id])){
                    $users[$userstask->user_id]= $userstask->user_id;
                    $this->sdt->createNotification($userstask->user_id,$user_id,"deleteTask",$task->id,$today);
                }
            }
        }
        $usersPackages = UsersPackages::find('package_id = '.$package_id);
        foreach ($usersPackages as $usersPackage) {
            $usersPackage->status = 0;
            $usersPackage->save();
            $this->sdt->createNotification($usersPackage->user_id,$user_id,"deletePackage",$usersPackage->package_id,$today);
        }
        $package->status = 0;
        $package->save();
    }

    public function createCustomization(){
//        $user_id = $this->session->get("userId");
//        $user = Users::findFirst($user_id);
//        $navbar_color = "#000000";
//        $body_color = "#FFFFFF";
//        if (!empty($user->profiles)) {
//            $navbar_color = $user->profiles->navbar_color;
//            $body_color = $user->profiles->body_color;
//        }
//        $this->view->setVars(array(
//            'navbar_color'   => $navbar_color,
//            'body_color'   => $body_color
//        ));
    }

    public function checklistSetting (){
        // obtener ultima configuracion checklist
        $user_id = $this->session->get("userId");
        $checklist = UsersChecklists::findFirst('user_id = '.$user_id);
        if(!$checklist){
            $fecha_hoy = new \DateTime('America/Mexico_City');
            $today = $fecha_hoy->format('Y-m-d');
            $checklist = new UsersChecklists();
            $checklist->user_id = $user_id;
            $checklist->date = $today;
            $checklist->range = 'week';
            $checklist->save();
        }
        $this->view->setVars(array(
            'range'         =>  $checklist->range
        ));
    }
}
