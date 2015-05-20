<?php

class RmregistriesController extends ControllerBase
{

    public function indexAction()
    {
        $this->assets
            ->addCss('css/jQueryUI.min.css')
            ->addCss('css/rm/rm.css');

        $this->assets
            ->addJs('js/jQueryUI.js')
            ->addJs('js/calendarioUI.js')
            ->addJs('js/jquery.zeroclipboard.js')
            ->addJs('js/jszip.js')
            ->addJs('js/xlsx.js')
            ->addJs('js/moment.min.js')
            ->addJs('js/loadAnimation.js')
            ->addJs('js/sdt_general/dateFormatString.js')
            ->addJs('js/jCryption/jquery.jcryption.3.1.0.js')
            ->addJs('js/jCryption/cryptoJs.min.js')
            ->addJs('js/rm/rm_var_global.js')
            ->addJs('js/jCryption/cryptoStorage.js')
            ->addJs('js/rm/sdt_rm_live.js')
            ->addJs('js/rm/sdt_rm_sync.js');



        $user_id = $this->session->get("userId");

        $fecha_hoy = new \DateTime('America/Mexico_City');

        $code_token = $this->_generateCode(60);
        // Actualizar usuario

//        $user = Users::findFirst($user_id);
//        $user->code_token = $code_token;
//        $user->save();

        // crear cinta de navegacion
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');

        $user = Users::findFirst($user_id);

        if ($user->profiles->subject_email==null){
            $subject = 'SDT USERNAME TIME';
        }else{
            $subject = $user->profiles->subject_email;
        }
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $usernme = $user->username;
        if (isset($user->profiles->id)){
            $usernme = $user->profiles->name.' '.$user->profiles->last_name;
        }

        $security_j='<script type="text/javascript">';
        $security_j.='var s_c_j = "'.sha1($user->password).'"';
        $security_j.='</script>';

        $this->view->setVars(array(
            'title_view'    =>  'Registro Maestro',
            'v_session'     =>  $has_user,
            'fechahoy'      =>  $fecha_hoy->format('d/m/Y'),
            'fechahoySmart' =>  $this->sdt->dateStringSmart($fecha_hoy->format('d/m/Y')),
            'code_token'    =>  $code_token,
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'name_user'     =>  $usernme,
            'username'      =>  $user->username,
            'subject_email' =>  $subject,
            'security_j'    =>  $security_j
        ));
    }

//    /**
//     * get_registries method
//     *
//     * @throws NotFoundException
//     * @param string $date
//     * @return json/html
//     */

    public function get_registriesAction(){
        $request = $this->request;
        if($this->request->isAjax() == true)
        {
            // verificar si es el ultimo ingreso de usuario
            $user_id = $this->session->get("userId");
//            $token = $this->request->data('token');
//            $user = $this->RmRegistry->User->find('first',array(
//                'conditions'=>array(
//                    'User.id'=>$user_id
//                )
//            ));
//            if ($user['User']['rm_token']!=$token) {
//                $this->autoRender=false;
//                return '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span>Se cerro la aplicacion porque su cuenta fue abierta en otro lugar.</div>';
//                exit();
//            }

            // Obtener la fecha y encontrar entre fechas
            $date = $request->get('date');

            // Obtener las etiquetas que tiene asociadas a este usuario
//            $this->loadModel('RmLabel');
//            $labels = $this->RmLabel->find('list', array(
//                    'order'=>array('RmLabel.name ASC'),
//                    'conditions'=>array(
//                        'RmLabel.user_id'=>$user_id
//                    )
//                )
//            );
//            $this->set('registries', $this->_registries_row($date));
//            $this->set('labels', $labels);

//            $labels = RmLabels::findByUserId($user_id);
            $labels = RmLabels::findByUserId($user_id);
            $values = $this->_registries_row($date);
            $this->view->setVars(array(
                'v_session'     =>  $user_id,
                'registries'    =>  $values,
                'cont'          =>  count($values),
                'labels'       =>  $labels
            ));
//            $this->view->disable();
//            $this->response->setJsonContent($this->_registries_row($date));
//            $this->response->setStatusCode(200,'Ok');
//            $this->response->send();
        }else{
            $this->view->disable();
            $this->response->setStatusCode(404, "Not Found1");
        }
    }

    public function getRegistriesJsonAction(){
        $this->view->disable();
        $r = $this->request;
        if($r->isAjax() == true) {
            $user_id = $this->session->get("userId");
            $date = $r->get('date');
            $labels = RmLabels::find(array('user_id='.$user_id,'order'=>'name ASC'));
            $values = $this->_registries_row($date);
            $fecha_hoy = new \DateTime('America/Mexico_City');
            $datos = array(
                'labels'    =>  $labels->toArray(),
                'rms'       =>  $values,
                'time'      =>  $fecha_hoy->format('Y-m-d H:m:s')
            );
            $this->response->setJsonContent($datos);
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->view->disable();
            $this->response->setStatusCode(404, "Not Found1");
        }
    }

    public function _generateCode($length) {
        $key = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
        $max = strlen($pattern)-1;
        for($i=0;$i < $length;$i++) $key .= $pattern{mt_rand(0,$max)};
        return $key;
    }

    private function _registries_row($date){
        $user_id = $this->session->get("userId");
        $date = explode("/", $date);

        $_day = $date[0];
        $_month = $date[1];
        $_year = $date[2];

        // $phql = "SELECT Cars.*, Brands.* FROM Cars LEFT JOIN Brands";
        $phql = 'SELECT
			RmRegistries.*
			FROM RmRegistries
			WHERE
			RmRegistries.user_id = "'.$user_id.'" AND
			RmRegistries.status = 1 AND
			DAY(RmRegistries.day) = "'.$_day.'" AND
			MONTH(RmRegistries.day) = "'.$_month.'" AND
			YEAR(RmRegistries.day) = "'.$_year.'"
			ORDER BY RmRegistries.order_r ASC';
        $datos = $this->modelsManager->executeQuery($phql);


//        $datos = RmRegistries::findByUserId($user_id);
        return $datos->toArray();
    }

    /**
     * get_days method
     *
     * @throws NotFoundException
     * @param string $date
     * @return json
     */
    public function get_daysAction (){
        $this->view->disable();
        $request = $this->request;
        if($this->request->isAjax() == true){
            // $this->_lastSessionAJAX();
            $user_id = $this->session->get("userId");
            $date = $request->get('date');

            if ($date!=""){
                $fecha = explode("/", $date);
                $datos = RmRegistries::find(
                    array(
                        'user_id = :user_id: AND status = 1 AND MONTH(day) = :date: AND YEAR(day) = :date2: ',
                        "bind" => array(
                            'user_id'       =>  $user_id,
                            'date'          =>  $fecha[1],
                            'date2'         =>  $fecha[2]
                        )
                    )
                );
                $days=array();
                $days['days']=array();
                foreach ($datos as $dato) {
                    $dia = substr($dato->day,8,2);
                    if (!isset($days['days'][$dia])) {
                        $days['days'][$dia]=true;
                    }
                    if (!$dato->checked) {
                        $days['days'][$dia]=false;
                    }
                }
                $this->response->setJsonContent($days);
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }


    /**
     * delete_registry method
     *
     * @throws NotFoundException
     * @param string $date
     * @return json
     */
    public function delete_registryAction(){
        $user_id = $this->session->has("userId");
        if($user_id){
            $user_id = $this->session->get("userId");
            $this->view->disable();
            $request = $this->request;
            if($this->request->isAjax() == true){
                // $this->_lastSessionAJAX();
                $rmregistry = RmRegistries::findFirst($request->get('id'));
                if ($user_id==$rmregistry->user_id){
                    $rmregistry->status =0;
                    if ($rmregistry->save()) {
                        $this->response->setJsonContent("Ok");
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
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }

    }

    /**
     * search_word method
     *
     * @throws search_word
     * @param string $word
     * @return json
     */
    public function search_wordAction(){
        $this->view->disable();
        $request = $this->request;
        if($this->request->isAjax() == true){
//            $this->_lastSessionAJAX();
            $word = $request->get('word');
            if (strlen($word)>1) {
                $user_id = $this->session->get("userId");

                $registros = RmRegistries::find(
                    array(
                        "user_id = :user_id: AND status = 1 AND registry LIKE '%".$word."%' ",
                        "bind" => array(
                            'user_id'       =>  $user_id
                        ),
                        "order" => "day DESC, order_r ASC"
                    )
                );

                $lineas = array();
                for ($i=0; $i < count($registros); $i++) {
                    $text_temp1 = trim(htmlspecialchars_decode($registros[$i]->registry));
                    $text_temp2 = strip_tags($text_temp1);
                    $posi = strpos(strtolower($text_temp2), strtolower($word));
                    if ($posi=== false) {
                    }else{
                        $txt_ini="";
                        if ($posi>40) {
                            $pos_ini=$posi-30;
                            $txt_ini="...";
                        }else{
                            $pos_ini=0;
                        }
                        $txt_fin="";
                        if ((strlen($text_temp2)-$pos_ini)>60) {
                            $pos_fin=60;
                            $txt_fin="...";
                        }else{
                            $pos_fin=strlen($text_temp2)-$pos_ini+1;
                        }
                        $text_temp1 = $txt_ini.substr ($text_temp2,$pos_ini, $pos_fin).$txt_fin;
                        // $diviciones = explode($palabra, $text_temp1);
                        $diviciones = preg_split("/".$word."/i", $text_temp1);
                        $text_temp1="";
                        for ($j=0; $j < count($diviciones); $j++) {
                            if ($j<count($diviciones)-1) {
                                $text_temp1.=$diviciones[$j].'<span class="wordSel">'.strtolower($word).'</span>';
                            }else{
                                $text_temp1.=$diviciones[$j];
                            }
                        }
                        // $date_r = CakeTime::format($registros[$i]['RmRegistry']["day"], '%d/%m/%Y');
                        $date_r = new DateTime($registros[$i]->day);
                        $date_r = $date_r->format('d/m/Y');
                        $lineas[] = array(
                            "html"=>$text_temp1,
                            "day"=>$date_r,
                            "url"=>$date_r
                        );
                    }
                }
                // return json_encode($lineas);
                $this->response->setJsonContent($lineas);
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    /**
     * new_registry method
     *
     * @throws NotFoundException
     * @param string $date,$new_registry,$created,$day,$order,$numbering,$registry,$checked,$rm_label_id,$status
     * @return json/html
     */

    public function new_registryAction(){
        $this->view->disable();
        $request = $this->request;
        if($this->request->isAjax() == true){
            // $this->_lastSessionAJAX();
            if (!($request->has('date') && $request->has('order') && $request->has('numbering') && $request->has('registry') && $request->has('checked') && $request->has('rm_label_id') ) ) {
                $this->response->setJsonContent("None");
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
                exit;
            }
            // $fecha = $this->request->data('date');
            // $fecha = explode("/", $fecha);
            // $fecha = mktime(0, 0, 0, $fecha[1], $fecha[0], $fecha[2]);
            // $hora =$this->Time->format('d/m/Y',time(),null,'GMT-8');


            $fecha = $request->get('date');
            $user_id = $this->session->get("userId");
            $fecha = explode("/", $fecha);

            $day = $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' 01:02:03';

            $fecha_hoy = new \DateTime('America/Mexico_City');

            $registry = new RmRegistries();
            $registry->user_id = $user_id;
            $registry->created = $fecha_hoy->format('Y-m-d H:m:s');
            $registry->day = $day;
            $registry->order_r = $request->get('order');
            $registry->numbering = $request->get('numbering');
            $registry->registry = $request->get('registry');
            $registry->checked = $request->get('checked');
            $registry->rm_label_id = $request->get('rm_label_id');
            $registry->status = 1;
            if ($registry->save()) {
                $registries = RmRegistries::find();
                $last_rm = $registries->getLast();
                $this->response->setJsonContent($last_rm->id);
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    /**
     * get_registries method
     *
     * @throws NotFoundException
     * @param string $date
     * @return json/html
     */

    public function update_registryAction(){
        $this->view->disable();
        $request = $this->request;
        if($this->request->isAjax() == true){
            // $this->_lastSessionAJAX();
            $id = $request->get('id');
            $registry = RmRegistries::findFirst($id);
            $registry->order_r = $request->get('order');
            $registry->numbering = $request->get('numbering');
            $registry->registry = $request->get('registry');
            $registry->checked = $request->get('checked');
            $registry->rm_label_id = $request->get('rm_label_id');
            $registry->status = 1;

            if ($registry->save()){
                $this->response->setJsonContent("Ok");
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    /**
     * get_label method
     *
     * @throws get_labels_b_colors
     * @param void
     * @return void
     */
    public function get_labels_b_colorsAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true){
            // Retornar los valores
            $user_id = $this->session->get("userId");
            $phql = 'SELECT
			RmLabels.*, RmSizes.*, RmFonts.*
			FROM RmLabels
			LEFT JOIN RmSizes
			LEFT JOIN RmFonts
			WHERE
			RmLabels.user_id = "'.$user_id.'"';
            $datos = $this->modelsManager->executeQuery($phql);

            $this->response->setJsonContent($datos->toArray());
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    /**
     * delete_registry method
     *
     * @throws search_word
     * @param string $word
     * @return json
     */
    public function send_emailAction(){
        $this->view->disable();
        $request = $this->request;
        if($this->request->isAjax() == true){
            // $this->_lastSessionAJAX();

            $user_id = $this->session->get("userId");

            $day = $request->get('day');
            $emails = $request->get('emails');
            $subject = $request->get('subject');

            $date = explode("/", $day);

            $_day = $date[0];
            $_month = $date[1];
            $_year = $date[2];

            $phql = 'SELECT
			RmRegistries.*, RmLabels.*, RmFonts.*, RmSizes.*
			FROM RmRegistries
			LEFT JOIN RmLabels
			LEFT JOIN RmFonts ON RmLabels.rm_font_id = RmFonts.id
			LEFT JOIN RmSizes ON RmLabels.rm_size_id = RmSizes.id
			WHERE
			RmRegistries.user_id = "'.$user_id.'" AND
			DAY(RmRegistries.day) = "'.$_day.'" AND
			MONTH(RmRegistries.day) = "'.$_month.'" AND
			YEAR(RmRegistries.day) = "'.$_year.'"
			ORDER BY RmRegistries.order_r ASC';
            $datos = $this->modelsManager->executeQuery($phql);

            // Crear cuerpo del correo
            $html="";
            $html.='<table>';
            foreach ($datos as $dato) {
                $html.='<tr style="';
                if ($dato->rmRegistries->checked){
                    $html.='background-color:#f1f1f1';
                }
                $html.='">';
                $html.='<td width="10px">';
                $html.='<div class="btn-group text-center" data-toggle="buttons">';
                $html.='<label class="btn btn-default ';
                if ($dato->rmRegistries->checked) {
                    $html.='active';
                }
                $html.='">';
                $html.='</label>';
                $html.='</div>';
                $html.='</td>';
                $html.='<td>';
                $html.='<div style="float:left;">';
                $niv = explode(".", $dato->rmRegistries->numbering);
                $niv= 12*(count($niv)-1);
                $html.='<div class="numerar" style="margin-left:'.$niv.'px">'.$dato->rmRegistries->numbering.'</div>';
                $html.='</div>';
                $niv2 = explode(".", $dato->rmRegistries->numbering);
                $niv2= 20+30*(count($niv2)-1);
                if ($dato->rmRegistries->rm_label_id==0) {
                    $html.='<div class="conpizarron" style="margin-left:'.$niv2.'px;color:#000000;border-radius:3px;">';
                    $html.='<div class="pizarron" style="margin-left:5px;font-family:Klavika;font-size:14;font-weight:boldword-break: break-all;';
                }else{
                    $html.='<div class="conpizarron" style="margin-left:'.$niv2.'px;color:'.$dato->rmLabels->color.';background-color:'.$dato->rmLabels->b_color.';border-radius:3px">';
                    $html.='<div class="pizarron" style="margin-left:5px;font-family:'.$dato->rmFonts->name.';font-size:'.$dato->rmSizes->size.'px;word-break: break-all;';
                }
                if ($dato->rmRegistries->checked==0){
                    $html.='font-weight: bold';
                }
                $html.='" contenteditable="true">'.trim(htmlspecialchars_decode($dato->rmRegistries->registry));
                $html.='</div>';
                $html.='</div>';
                $html.='</td>';
                $html.='</tr>';
            }
            $html.='</table>';


            // Multiples correos
            $emails = explode(",", $emails);


            $user = Users::findFirst($user_id);

            // dar valores a las palabras claves en el asunto
            $subject = preg_replace('/TIME/', $day, $subject);
            $subject = preg_replace('/USERNAME/', $user->username, $subject);
            $subject = preg_replace('/DATE/', $this->sdt->dateStringSmart($day), $subject);

            for ($i=0; $i <count($emails) ; $i++) {
                $this->getDI()->getMail()->send(
                    'SDT ' . $user->username . ' ' . $day,
                    array(
                        $emails[$i] => $user->username
                    ),
                    $subject,
                    'rm',
                    array(
                        'html_rm' => $html
                    )
                );
            }

            $this->response->setJsonContent('Ok');
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    /**
     * get_label method
     *
     * @throws NotFoundException
     * @param string $eti
     * @return json
     */
    public function get_labelAction(){
        $this->view->disable();
        $request = $this->request;
        if($this->request->isAjax() == true){
            // Tomar id de la Etiqueta
            $value = $request->get('eti');
            // Retornar los valores

            $phql = 'SELECT
			RmLabels.*, RmSizes.*, RmFonts.*
			FROM RmLabels
			LEFT JOIN RmSizes
			LEFT JOIN RmFonts
			WHERE
			RmLabels.id = "'.$value.'"';
            $datos = $this->modelsManager->executeQuery($phql);

            $this->response->setJsonContent($datos->toArray());
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    /**
     * get_registries method
     *
     * @throws NotFoundException
     * @param string $date
     * @return json/html
     */

    public function getRegistryAction(){
        $request = $this->request;
        if($this->request->isAjax() == true){

            // verificar si es el ultimo ingreso de usuario
            $user_id = $this->session->get("userId");
            // Obtener la fecha y encontrar entre fechas
            $id = $request->get('id');

            // Obtener las etiquetas que tiene asociadas a este usuario

            $phql = 'SELECT
			RmLabels.*, RmSizes.*, RmFonts.*
			FROM RmLabels
			LEFT JOIN RmSizes
			LEFT JOIN RmFonts
			WHERE
			RmLabels.user_id = "'.$user_id.'"';
            $labels = $this->modelsManager->executeQuery($phql);

            $registries = $this->_registry_row($id);
            if ($registries->count()>0) {
                foreach ($registries as $registry){
                    $fecha0 = $registry->rmRegistries->day;
                }
                $fecha = explode(' ', $fecha0);
                $fecha = explode('-', $fecha[0]);
                $this->view->setVars(array(
                    'v_session'     =>  $user_id,
                    'registries'    =>  $registries,
                    'cont'          =>  count($registries),
                    'labels'        =>  $labels,
                    'name_rm'       =>  'Registro del Dia: '.$fecha[2].'/'.$fecha[1].'/'.$fecha[0]
                ));

                $this->view->pick("rmregistries/get_registries");
            }else{
                $this->autoRender=false;
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->autoRender=false;
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    private function _registry_row($id){
        $user_id = $this->session->get("userId");
        $phql = 'SELECT
        RmRegistries.*, RmLabels.*
        FROM RmRegistries
        LEFT JOIN RmLabels ON RmRegistries.rm_label_id = RmLabels.id
        WHERE
        RmRegistries.id = "'.$id.'"
        AND RmRegistries.status = 1';
        $datos = $this->modelsManager->executeQuery($phql);

        return $datos;
    }

    public function updateRegistryAction(){
        $this->autoRender=false;
        $request = $this->request;
        if($request->isAjax()){
            $copyPost = $_POST;
            $jc = $this->jcrypt;
            $jc->go();
            if (!$request->has('id')) {
                $this->response->setStatusCode(404,print_r($copyPost));
            }
            $user_id                =   $this->session->get("userId");
            $rm = RmRegistries::findFirst($request->get('id'));
            if($rm->user_id==$user_id){
                if($request->has('status')){
                    $rm->status  =   $request->get('status');
                }
                if($request->has('numbering')){
                    $rm->numbering  =   $request->get('numbering');
                }
                if($request->has('order_r')){
                    $rm->order_r  =   $request->get('order_r');
                }
                if($request->has('acordion')){
                    $rm->acordion  =   $request->get('acordion');
                }
                if($request->has('rm_label_id')){
                    $rm->rm_label_id  =   $request->get('rm_label_id');
                }
                if($request->has('registry')){
                    $rm->registry  =   $request->get('registry');
                }
                if($request->has('checked')){
                    $rm->checked  =   $request->get('checked');
                }
                if($rm->save()){
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
    public function createRegistryAction(){
        $this->autoRender=false;
        $request = $this->request;
        if($request->isAjax() == true){
            $jc = $this->jcrypt;
            $jc->go();
            if (!($request->has('date') && $request->has('order_r') && $request->has('numbering') && $request->has('acordion') && $request->has('registry') && $request->has('checked') && $request->has('rm_label_id') ) ) {
                $this->response->setStatusCode(404,'Not Found');
                exit;
            }

            $fecha                  =   $request->get('date');
            $user_id                =   $this->session->get("userId");
            $fecha                  =   explode("/", $fecha);

            $day                    =   $fecha[2].'-'.$fecha[1].'-'.$fecha[0].' 01:02:03';

            $registry               =   new RmRegistries();
            $registry->user_id      =   $user_id;
            $registry->day          =   $day;
            $registry->order_r      =   $request->get('order_r');
            $registry->numbering    =   $request->get('numbering');
            $registry->registry     =   $request->get('registry');
            $registry->checked      =   $request->get('checked');
            $registry->acordion     =   $request->get('acordion');
            $registry->rm_label_id  =   $request->get('rm_label_id');
            $registry->status       =   1;
            if ($registry->save()) {
                $this->response->setJsonContent($registry->id);
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }else{
                $this->response->setStatusCode(404,'Not Found');
            }
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }
}

