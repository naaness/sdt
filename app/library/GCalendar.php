<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 6/03/15
 * Time: 03:51 PM
 */
require_once __DIR__ . '/../../vendor/google-api-php-client/autoload.php';
use Phalcon\Mvc\User\Component;

class GCalendar extends Component
{
    private $_client_id;
    private $_client_secret;
    private $_redirect_uri;
    private $controller;
    public $components = array('RequestHandler');

    public function startup(Controller $controller){
        $this->controller = $controller;
    }

    public function initElement(){
        $Base_url = 'http://'.$_SERVER['HTTP_HOST'].'/';
        if ( !(strrpos($Base_url, "http://sdt-dev.sinergiatilabs.com") === false))  {
            $this->_client_id = '651889759410-dqgekui62ao2n2a04rrhq958qpn560sj.apps.googleusercontent.com';
            $this->_client_secret = 'RsXoTVVDbz8-gKzUjHeRZ5OF';
            $this->_redirect_uri = 'http://sdt-dev.sinergiatilabs.com/htd/oauth2callback';
        }elseif ( !(strrpos($Base_url, "http://sdt.sinergiatilabs.com") === false))  {
            $this->_client_id = '651889759410-1orepbkurbviodnf0gje8a55tok2dmev.apps.googleusercontent.com';
            $this->_client_secret = 'x0N-_XJb-6KJXwsybIC8cqKf';
            $this->_redirect_uri = 'http://sdt.sinergiatilabs.com/htd/oauth2callback';
        }elseif ( !(strrpos($Base_url, "sdt.sinergiafc.com") === false))  {
            $this->_client_id = '651889759410-roevj60g73t7i6cvqifbbv52gvkgld7h.apps.googleusercontent.com';
            $this->_client_secret = 'Kr9feZc4FlEfU35BIRhF43jK';
            $this->_redirect_uri = 'http://sdt.sinergiafc.com/sdt_live/gl_callback';
        }else if ( !(strrpos($Base_url, "http://sdt.com") === false))  {
            $this->_client_id = '651889759410-hc2vn30fdtl96eq93o7b36d9kqnpv7o8.apps.googleusercontent.com';
            $this->_client_secret = 'hT86q1JJ7i-t7oT2OD91kc4q';
            $this->_redirect_uri = 'http://sdt.com/htd/oauth2callback';
        }else if ( !(strrpos($Base_url, "cake-sdt.com") === false))  {
            $this->_client_id = '651889759410-4n9hlkq2gm4kuh4kcrek77v60r1t1n73.apps.googleusercontent.com';
            $this->_client_secret = 'WrbLmu7o_kCpourvlc60U2Wk';
            $this->_redirect_uri = 'http://cake-sdt.com/Htd/oauth2callback';
        }else if ( !(strrpos($Base_url, "sdt.com") === false))  {
            $this->_client_id = '657023763381-virqe6ijf0ifl56licrs732qrgh534tu.apps.googleusercontent.com';
            $this->_client_secret = 'qEnGGLnH82tF8f1xAC6hyOEj';
            $this->_redirect_uri = 'http://sdt.com/sdt_live/gl_callback';
        }else if ( !(strrpos($Base_url, "sdt-dev.sinergiafc.com") === false))  {
            $this->_client_id = '651889759410-kvldlpk4n7qub5m4ji2m6dvvovb925as.apps.googleusercontent.com';
            $this->_client_secret = 'QXLNG2mntWi6I4cJbaxQrz0e';
            $this->_redirect_uri = 'http://sdt-dev.sinergiafc.com/Htd/oauth2callback';
        }else if ( !(strrpos($Base_url, "sdt.artesan.us") === false))  {
            $this->_client_id = '651889759410-vajqj7lb9271ufe788hj0dck5saf73tt.apps.googleusercontent.com';
            $this->_client_secret = '5S8y7wX6Kj5sryTSZyRwvwMX';
            $this->_redirect_uri = 'http://sdt.artesan.us/sdt_live/gl_callback';
        }
    }

    public function getEventsByDay($day){
        $this->initElement();

        $client = new Google_Client();
        $client->setClientId($this->_client_id);
        $client->setClientSecret($this->_client_secret);
        $client->setRedirectUri($this->_redirect_uri);
        $client->setScopes(array("https://www.googleapis.com/auth/calendar","https://www.googleapis.com/auth/calendar.readonly"));

        if (isset($_REQUEST['logout'])) {
            unset($_SESSION['access_token']);
        }

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
        }

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
        } else {
            return 'Nada';
        }

        if ($client->getAccessToken()) {

            $cal = new Google_Service_Calendar($client);
            $calendarList = $cal->calendarList->listCalendarList();

            $day = explode(' ', $day);
            $day = explode('-', $day[0]);
            $day = $day[2].'/'.$day[1].'/'.$day[0];

            $today = explode('/', $day);
            $fechaStart = mktime(1, 0, 0, $today[1], $today[0], $today[2]);
            $fechaEnd = mktime(23, 59, 59, $today[1], $today[0], $today[2]);
            //echo date(DateTime::ATOM,$fechaStart) ."<br>";
            //echo date(DateTime::ATOM,$fechaEnd) ."<br>";
            $optParamsEvents = array(
                "orderBy"=>"startTime",
                "singleEvents"=>true,
                "timeMin"=>date(DateTime::ATOM,$fechaStart) ,
                "timeMax"=>date(DateTime::ATOM,$fechaEnd)
            );

            $datos = array();
            while(true) {
                foreach ($calendarList->getItems() as $calendar) {
                    if (isset($calendar->selected)) {
                        $eventsX = $cal->events->listEvents($calendar->id,$optParamsEvents);
                        while(true) {
                            foreach ($eventsX->getItems() as $eventX) {
                                $datos[]=array(
                                    "id"=>$eventX->id,
                                    "title"=>$eventX->getSummary(),
                                    "start"=>$this->tiempoFullCalendar($eventX->getStart()->getDateTime()),
                                    "end"=>$this->tiempoFullCalendar($eventX->getEnd()->getDateTime()),
                                    "className"=>"google-color".$calendar->getColorid(),
                                    "url"=>$eventX->getHtmlLink()
                                );
                            }
                            $pageTokenX = $eventsX->getNextPageToken();
                            if ($pageTokenX) {
                                $optParamsX = array('pageToken' => $pageTokenX);
                                $eventsX = $cal->events->listEvents($calendar->getId(), $optParamsX);
                            } else {
                                break;
                            }
                        }
                    }
                }
                $pageToken = $calendarList->getNextPageToken();
                if ($pageToken) {
                    $optParams = array('pageToken' => $pageToken);
                    $calendarList = $cal->calendarList->listCalendarList($optParams);
                } else {
                    break;
                }
            }
        }
        return $datos;
    }

    public function gl_callback(){
        $this->initElement();
        $client = new Google_Client();
        $client->setClientId($this->_client_id);
        $client->setClientSecret($this->_client_secret);
        $client->setRedirectUri($this->_redirect_uri);
        $client->setScopes(array("https://www.googleapis.com/auth/calendar","https://www.googleapis.com/auth/calendar.readonly"));

        $calendar_service = new Google_Service_Calendar($client);
        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
            // si se acepto la peticion asociar la aplicacion a la cuenta de google
            // verificar de donde se llego antes de llegar aqui
            // 1 si viene de login -> ir a sdt_live
            // 2 si viene del sincronizar_sdt_live -> ir a sincronizar_sdt_live
            // 3 si viene de sincronizar -> ir a sincronizar
            // $datos=array(
            // 	"id"=>$this->_id_usuario,
            // 	"sincronizar_calendar"=>1
            // );
            $user_id = $this->session->get('userId');
            $user = Users::findFirst($user_id);
            $user->sync_calendar = 2;
            $user->save();
            return $this->response->redirect('htd/index');
        }else{
            // 1 si viene de login -> ir a sdt_live, y llamar solo eventos internos del programa, cambiar el valor a no sincronizar
            // 2 si viene del sincronizar_sdt_live -> ir a sdt_live no volver a intentarlo, cambiar el valor a no sincronizar
            // 3 si viene de sincronizar -> ir a sincronizar, cambiar el valor a no sincronizar
            // $datos=array(
            //	"id"=>$this->_id_usuario,
            //	"sincronizar_calendar"=>0
            //);
            return $this->response->redirect('/');
        }
        // $this->_sdt->actualizarUsuario($datos);
        // $this->redireccionar($vista);
        // // $this->redireccionar("sdt_live");
        // exit();
    }
    public function getAuthUrl(){
        $this->initElement();

        $client = new Google_Client();
        $client->setClientId($this->_client_id);
        $client->setClientSecret($this->_client_secret);
        $client->setRedirectUri($this->_redirect_uri);
        $client->setScopes(array("https://www.googleapis.com/auth/calendar","https://www.googleapis.com/auth/calendar.readonly"));
        // $client->grantType("refresh_token");

        $authUrl = '';
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
            if ($client->isAccessTokenExpired()) {
                $this->response->redirect($client->createAuthUrl());
                $authUrl="";
            }
        } else {
            $authUrl = $client->createAuthUrl();
        }
        return $authUrl;
    }

    private function obtenerCadena($contenido,$inicio,$fin){
        $r = explode($inicio, $contenido);
        if (isset($r[1])){
            $r = explode($fin, $r[1]);
            return $r[0];
        }
        return '';
    }

    private function tiempoFullCalendar($tiempo){
        //echo $tiempo ."<br>";
        $t = explode("T", $tiempo);
        for ($i=0; $i <count($t) ; $i++) {
            $horas= $t[$i];
        }
        $r = explode("-", $horas);
        return $t[0]." ".$r[0];
    }

    public function getColors(){
        $this->initElement();

        $client = new Google_Client();
        $client->setClientId($this->_client_id);
        $client->setClientSecret($this->_client_secret);
        $client->setRedirectUri($this->_redirect_uri);
        $client->setScopes(array("https://www.googleapis.com/auth/calendar","https://www.googleapis.com/auth/calendar.readonly"));

        $calendar_service = new Google_Service_Calendar($client);

        if (isset($_REQUEST['logout'])) {
            unset($_SESSION['access_token']);
        }

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->getAccessToken();
        }

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
        } else {
            return 'Nada';
        }

        $colors = $calendar_service->colors->get();

        // Print available calendarListEntry colors.
        $html = '';
        foreach ($colors->getCalendar() as $key => $color) {
            $html.= '.google-color'.$key.', .google-color'.$key.' div, .google-color'.$key.' span  {<br>';
            $html.= '  background-color: <span style="color:'.$color->getBackground().'">'.$color->getBackground().';</span><br>';
            $html.= '  border-color: <span style="color:'.$color->getBackground().'">'.$color->getBackground().';</span><br>';
            $html.= '  color: <span style="color:'.$color->getForeground().'">'.$color->getForeground().';</span><br>}<br>';
        }
        if ($html!="") {
            $html= "Calendarios<br>".$html;
            $html.= "<br>Eventos<br>";
        }
        // Print available event colors.
        foreach ($colors->getEvent() as $key => $color) {
            $html.= '.google-color'.$key.' {<br>';
            $html.= '  background-color: <span style="color:'.$color->getBackground().'">'.$color->getBackground().';</span><br>';
            $html.= '  color: <span style="color:'.$color->getForeground().'">'.$color->getForeground().';</span><br>}<br>';
        }
        return $html;
    }
}