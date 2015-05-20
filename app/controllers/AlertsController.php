<?php
use \Phalcon\Paginator\Adapter\Model as Paginacion;
class AlertsController extends ControllerBase
{

    public function indexAction()
    {
        $user_id = $this->session->get("userId");
        // Obtener todas las alertas que el usuario no ha visto, contarlas
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d'). ' 23:59:59';
        // Obtener las ultimas 10 alertas que son menoes al dia de hoy
//        $phql = 'SELECT
//                Alerts.*
//                FROM Alerts
//                WHERE
//                Alerts.user_id = '.$user_id.'
//                AND Alerts.date <= "'.$today.'"
//                ORDER BY Alerts.was_seen ASC, Alerts.id DESC';
//        $pastalerts = $this->modelsManager->executeQuery($phql);

        $phql = 'SELECT
                UnidTimes.*
                FROM UnidTimes
                INNER JOIN Tasks ON (Tasks.status = 1 AND Tasks.id = UnidTimes.task_id)
                WHERE
                Tasks.user_id = '.$user_id.'
                AND UnidTimes.start_day <= "'.$today.'"
                ORDER BY UnidTimes.start_day DESC, UnidTimes.follow_up ';
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
            $msg = '<strong>'.$alert->tasks->name.'</strong>';
            if($alert->follow_up!=1){
                $resol = '<span class="label label-success"> Resuelta : Si</span>';
            }else{
                $resol = '<span class="label label-danger"> Resuelta : No</span>';
            }
            $fecha = explode(' ',$alert->start_day);
            $fecha = explode('-',$fecha[0]);
            $html_alert.= '<a href="'.$url.$url0.'" class="list-group-item">('.$fecha[2].'/'.$fecha[1].'/'.$fecha[0].') '.$resol.' '.$msg.'</a>';
        }

        $this->addBreadcrumb('Mis Alertas','alerts');

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }

        $this->view->setVars(array(
            'title_view'    =>  'Alertas',
            'v_session'     =>  $has_user,
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'html_alert'      =>  $html_alert
        ));
    }

}

