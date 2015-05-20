<?php
use \Phalcon\Paginator\Adapter\Model as Paginacion;
class NotificationsController extends ControllerBase
{

    public function indexAction()
    {
        $user_id = $this->session->get("userId");
        // Obtener todas las alertas que el usuario no ha visto, contarlas
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d'). ' 23:59:59';
        // Obtener las ultimas 10 alertas que son menoes al dia de hoy
        $phql = 'SELECT
                Notifications.*
                FROM Notifications
                WHERE
                Notifications.user_id = '.$user_id.'
                ORDER BY Notifications.id DESC';
        $pastalerts = $this->modelsManager->executeQuery($phql);

        $paginator = new Paginacion(
            array(
                "data" => $pastalerts,
                "limit"=> 10,
                //variable get page convertida en un integer
                "page" => $this->request->getQuery('page', 'int')
            )
        );

        $this->view->page = $paginator->getPaginate();

        $items = $this->view->page->items;
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/';
        $html_alert="";
        foreach ($items as $alert) {
            $user0 = Users::findFirst($alert->change_user_id);
            if ($alert->type == "newPost") {
                $post = Posts::findFirst($alert->change_id);
                $url0 = 'posts/view/'.$post->id;
                $msg = '<strong>'.$user0->username.'</strong> ha agregado un nuevo comentario al foro <strong>'.$post->name.'</strong>';
            } elseif ($alert->type == "newTeam") {
                $dato = Teams::findFirst($alert->change_id);
                $url0 = 'teams/view/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> te ha agregado al equipo <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "editTeam") {
                $dato = Teams::findFirst($alert->change_id);
                $url0 = 'teams/view/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> ha modificado al equipo <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deleteTeam") {
                $dato = Teams::findFirst($alert->change_id);
                $url0 = 'teams/view/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> te ha sacado del equipo <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "newProject") {
                $dato = Projects::findFirst($alert->change_id);
                $url0 = 'tasks/index/'.$dato->teams->id.'/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> te ha agregado al proyecto <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "editProject") {
                $dato = Projects::findFirst($alert->change_id);
                $url0 = 'teams/view/'.$dato->teams->id.'/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> ha modificado al proyecto <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deleteProject") {
                $dato = Projects::findFirst($alert->change_id);
                $url0 = 'teams/view/'.$dato->teams->id.'/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> te ha sacado del proyecto <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "delegate") {
                $dato = Tasks::findFirst($alert->change_id);
                $url0 = 'checklist';
                $msg = '<strong>'.$user0->username.'</strong> te ha delegado la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "accepted") {
                $dato = Tasks::findFirst($alert->change_id);
                $team_id =0;
                if ($dato->project_id>0){
                    $team_id = $dato->projects->team_id;
                }elseif ($dato->package_id>0){
                    $team_id = $dato->packages->team_id;
                }
                $url0 = 'tasks/view/'.$team_id.'/'.$dato->project_id.'/'.$dato->package_id.'/0/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> ha aceptado la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "rejected") {
                $dato = Tasks::findFirst($alert->change_id);
                $team_id =0;
                if ($dato->project_id>0){
                    $team_id = $dato->projects->team_id;
                }elseif ($dato->package_id>0){
                    $team_id = $dato->packages->team_id;
                }
                $url0 = 'tasks/view/'.$team_id.'/'.$dato->project_id.'/'.$dato->package_id.'/0/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> ha rechazado la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "reactived") {
                $dato = Tasks::findFirst($alert->change_id);
                $url0 = 'checklist';
                $msg = '<strong>'.$user0->username.'</strong> ha reactivado la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "newChecklist") {
                $dato = Packages::findFirst($alert->change_id);
                $url0 = 'tasks/index/'.$dato->teams->id.'/0/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> ha agregado una tarea al checklist <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "changeUnidTime") {
                $dato = Tasks::findFirst($alert->change_id);
                $team_id =0;
                if ($dato->project_id>0){
                    $team_id = $dato->projects->team_id;
                }elseif ($dato->package_id>0){
                    $team_id = $dato->packages->team_id;
                }
                $url0 = 'tasks/view/'.$team_id.'/'.$dato->project_id.'/'.$dato->package_id.'/0/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> ha modificado la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deleteTask") {
                $dato = Tasks::findFirst($alert->change_id);
                $url0 = 'index';
                $msg = '<strong>'.$user0->username.'</strong> ha eliminado la tarea <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deleteProject") {
                $dato = Projects::findFirst($alert->change_id);
                $url0 = 'index';
                $msg = '<strong>'.$user0->username.'</strong> ha eliminado el proyecto <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deletePackage") {
                $dato = Packages::findFirst($alert->change_id);
                $url0 = 'index';
                $msg = '<strong>'.$user0->username.'</strong> ha eliminado el checklist <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "deleteTeam") {
                $dato = Teams::findFirst($alert->change_id);
                $url0 = 'index';
                $msg = '<strong>'.$user0->username.'</strong> ha eliminado el equipo de trabajo <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "messageTask") {
                $dato = Tasks::findFirst($alert->change_id);
                $url0 = 'checklist';
                $msg = '<strong>'.$user0->username.'</strong> ha comentado la tarea  <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "dislikePost") {
                $dato = Posts::findFirst($alert->change_id);
                if($dato->parent_post>0){
                    $dato = Posts::findFirst($dato->parent_post);
                }
                $url0 = 'posts/view/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> no le ha gustado tu comentario en el foro  <strong>'.$dato->name.'</strong>';
            } elseif ($alert->type == "likePost") {
                $dato = Posts::findFirst($alert->change_id);
                if($dato->parent_post>0){
                    $dato = Posts::findFirst($dato->parent_post);
                }
                $url0 = 'posts/view/'.$dato->id;
                $msg = '<strong>'.$user0->username.'</strong> le ha gustado tu comentario en el foro  <strong>'.$dato->name.'</strong>';
            }


            $fecha0 = explode(' ',$alert->date);
            $fecha = explode('-',$fecha0[0]);
            $html_alert.= '<a href="'.$url.$url0.'" class="list-group-item">('.$fecha[2].'/'.$fecha[1].'/'.$fecha[0].') '.$msg.'</a>';
        }

        $this->addBreadcrumb('Mis Notificaciones','notifications');

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'    =>  'Notificaciones',
            'v_session'     =>  $has_user,
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'html_not'      =>  $html_alert
        ));
    }

    public function wasSeenAction()
    {
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true) {
            // Obtener el id del usuario
            $user_id = $this->session->get("userId");
            // Obtener todas las alertas que pertenescan al usuario
            $allalerts = Notifications::findByUserId($user_id);
            if ($allalerts->count()>0){
                $phql = 'SELECT
                    Notifications.*
                    FROM Notifications
                    WHERE
                    Notifications.user_id = '.$user_id.'
                    AND Notifications.was_seen = 0';
                $pastalerts = $this->modelsManager->executeQuery($phql);
                foreach ($pastalerts as $alert) {
                    $alert->was_seen = 1;
                    $alert->save();
                }
                $this->response->setJsonContent('None');
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();

//                $this->response->setStatusCode(404,'Not Found');
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    public function newsAction() {
        $this->view->disable();
        $user_id = $this->session->has("userId");
        if($user_id){
            $request = $this->request;
            if($request->isAjax() == true) {
                $datos = array();
                $alerts = array();
                $notifications = array();
                $user_id = $this->session->get("userId");
                $fecha_hoy = new \DateTime('America/Mexico_City');
                $today = $fecha_hoy->format('Y-m-d');

                $alerts['count'] =   $this->countAlerts($user_id, $today);
                if($request->get('cont')!=$alerts['count']){
                    $alerts['html']  =   $this->htmlAlerts($user_id, $today);
                    $datos["alerts"] = $alerts;
                }
                $notifications['count'] =   $this->countNotifications($user_id, $today);

                if($request->get('cont2')!=$notifications['count']){
                    $notifications['html']  =   $this->htmlNotifications($user_id, true);
                    $datos["notifications"] = $notifications;
                }
                if(count($datos)>0){
                    $this->response->setJsonContent($datos);
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
}

