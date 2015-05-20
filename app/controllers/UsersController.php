<?php
use \Phalcon\Paginator\Adapter\Model as Paginacion;
use RegisterForm as FormRegister; //User el formulario registro
use PasswordForm as FormPassword; //User el formulario para cambiode contraseña por el usuairo
use EditUserForm as FormEditUser; //User el formulario para cambiode contraseña por el usuairo
use PasswordAdminForm as FormPasswordAdmin;

class UsersController extends ControllerBase {
    public function indexAction(){
//        //Crea un paginador, muestra 3 filas por página
//        $paginator = new Paginacion(
//            array(
//                //obtenemos los productos
//                "data" => Users::find(array(
//                            "order" => "username"
//                        )
//                    ),
//                //limite por página
//                "limit"=> 10,
//                //variable get page convertida en un integer
//                "page" => $this->request->getQuery('page', 'int')
//            )
//        );
//
//        //pasamos el objeto a la vista con el nombre de $page
//        $this->view->page = $paginator->getPaginate();

        $users = Users::find(array('order'=>'username'));

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('Categorias','categories');

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $roles = array(
            "admin"=>"Administrador",
            "registered"=>"Registrado"
        );
        $this->view->setVars(array(
            'title_view'=>  'Administracion de Usuarios',
            'v_session' =>  $has_user,
            'users'     =>  $users,
            'roles'     =>  $roles
        ));


        $this->assets
            ->addCss('css/table/theme.bootstrap.css')
            ->addCss('css/table/jquery.tablesorter.pager.css');

        $this->assets
            ->addJs('js/table/jquery.tablesorter.js')
            ->addJs('js/table/jquery.tablesorter.widgets.js')
            ->addJs('js/table/jquery.tablesorter.pager.js')
            ->addJs('js/table/jquery.table.execute.js');
    }

    public function addAction()
    {
        $request = $this->request;
        if($request->isPost())
        {
            $form = new FormRegister();
            if ($form->isValid($this->request->getPost()) != false)
            {
                $datetime = new \DateTime('America/Mexico_City');

                $user = new Users();
                $user->username = $request->getPost('username');
                $user->password = $request->getPost('password');
                $user->email = $request->getPost('email');
                $user->active = 1;
                $user->created_at = $datetime->format('Y-m-d H:i:s');
                $user->role = $request->getPost('role');
                $user->status = 1;
                $user->created =$datetime->format('Y-m-d H:i:s');
                $user->modified = $datetime->format('Y-m-d H:i:s');
                $user->step_of_day = 1;
                $user->sync_calendar =0;
                $user->rm_token="-";
                $user->ch_token="-";
                $user->htd_token='-';
                // Ya se valido desde el formulario, ahora desde la base de datos
                if($user->validation()==true)
                {
                    if($user->save()){
                        $this->flash->success('El usuario ha sido creado');
                        return $this->dispatcher->forward(array("action" => "index"));
                    }else{
                        $this->createListError($user);
                    }
                }else{
                    $this->createListError($user);
                    return $this->dispatcher->forward(array("action" => "index"));
                }
            }else{
                $this->createListError($form);
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('Usuarios','users');
        $this->addBreadcrumb('Crear Usuario');

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Crear Usuario',
            'form'      => new FormRegister(),
            'v_session' => $has_user,
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));
    }

    public function editAction($id=null)
    {
        $request = $this->request;
        $user_id = $this->session->get("userId");
        if($request->isPost())
        {
            $form = new FormEditUser();
            if($form->isValid($request->getPost())== true)
            {
                $user = Users::findFirst($id);

                $user->role = $request->getPost('role');
                // Ajustes para que phalcon reconozca lo elementos check
                $user->active = 1;
                if ($request->getPost('active')==''){
                    $user->active = 0;
                }
                if($user->rm_token ==""){
                    $user->rm_token =".";
                }
                if($user->ch_token==""){
                    $user->ch_token=".";
                }
                if($user->htd_token==""){
                    $user->htd_token=".";
                }


                // Ya se valido desde el formulario, ahora desde la base de datos

                // Si el usuario ya tiene ese username o correo no compararlo con otros
                $validateUsername = true;
                if ($request->getPost('username') != $user->username){
                    $user->username = $request->getPost('username');
                    $validateUsername = $user->validationUsername();
                }
                $validateEmail = true;
                if ($request->getPost('email') != $user->email){
                    $user->email = $request->getPost('email');
                    $validateEmail = $user->validationEmail();
                }

                if( ($validateUsername && $validateEmail)==true)
                {
                    $user->username = $request->getPost('username');
                    $user->email = $request->getPost('email');
                    $user->status = $request->getPost('status');
                    $user->status = 1;
                    if ($request->getPost('status')=='' || $request->getPost('status')=='0'){
                        $user->status = 0;
                    }
                    $user->save();
                    if ($user->save()==false){
                        $this->createListError($user);
                    }else{
                        $this->flash->success('El Usuario ha sido actualizado ');
                    }
                    //$this->response->redirect('users');
                }
                else
                {// Obtengo los mensajes enviados desde la validacion del formulario
                    $this->createListError($user);
                }
            }
            else
            {
                $this->createListError($form);
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('Usuarios','users');
        $this->addBreadcrumb('Editar Usuario');

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $user = Users::findFirstById($id);
        $this->view->setVars(array(
            'title_view'=>'Administracion de Articulos',
            'v_session' => $has_user,
            'user'      => $user,
            'id'        => $id,
            'form'      => new FormEditUser($user),
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));
    }

    public function viewAction($id=0){
        if ($id){
            $this->assets
                ->addCss('css/userView.css')
                ->addJs('js/userView.js');
            $user_id = $this->session->get("userId");
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
            $this->view->setVars(array(
                'title_view'=>'Perfil de usuario',
                'v_session' => $this->session->has("userId"),
                'user'      =>  Users::findFirst($id)
            ));

        }else{
            $this->response->redirect('index');
        }
    }

    public function changePasswordAction($id=null)
    {
        $user_id = $this->session->get("userId");
        if($id==$user_id){
            $request = $this->request;
            if($request->isPost())
            {
                $form = new FormPassword();
                if($form->isValid($request->getPost())== true)
                {
                    // Evualiza que la nueva contraseña sea igual a la confirmacion
                    $newPassword = $request->getPost('newpassword');
                    $confirmPassword = $request->getPost('confirmPassword');
                    if($newPassword == $confirmPassword){
                        $user = Users::findFirstById($id);
                        // La contraseña debe ser igual a la anterior
                        $actualPassword = $request->getPost('password');
                        if( sha1(md5($actualPassword)) == $user->password ){
                            $datetime = new \DateTime('America/Mexico_City');
                            $user->password = $newPassword;
                            if (!$user->save()) {
                                $this->flash->success('La contraseña ha sido actualizada');
                            }
                        }else{
                            $this->flash->error('Constraseña incorrecta');
                        }
                    }else{
                        $this->flash->error('La contraseñas no coinciden');
                    }
                }
                else
                {
                    foreach ($form->getMessages() as $message)
                    {
                        $this->flash->error($message);
                    }
                }
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('Usuarios','users');
        $this->addBreadcrumb('Cambiar Contraseña');

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Administracion de Usuarios',
            'v_session' => $has_user,
            'form'      => new FormPassword(),
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));
    }

    public function changePasswordAdminAction($id=null)
    {
        $request = $this->request;
        if($request->isPost())
        {
            $form = new FormPasswordAdmin();
            if($form->isValid($request->getPost()) != false)
            {
                // Evualiza que la nueva contraseña sea igual a la confirmacion
                $newPassword = $request->getPost('password');
                $user = Users::findFirstById($id);
                $user->password = sha1(md5($newPassword));
                if ($user->email==""){
                    $user->email = "sincorreo@correo.com";
                }
                if ($user->rm_token==""){
                    $user->rm_token = "rm_token";
                }
                if ($user->ch_token ==""){
                    $user->ch_token = "ch_token";
                }
                if ($user->htd_token ==""){
                    $user->htd_token = "htd_token";
                }
                if ($user->role == 1){
                    $user->role = "registered";
                }
                if ($user->save()) {
                    $this->flash->success('La contraseña ha sido actualizada');
                }else{
                    $this->createListError($user);
                    $this->flash->error('Ha ocurrido un error al momento de actualizar la Contraseña');
                }
            }
            else
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message);
                }
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('Usuarios','users');
        $this->addBreadcrumb('Cambiar Contraseña');

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Administracion de Usuarios',
            'v_session' => $has_user,
            'id'        => $id,
            'form'      => new FormPasswordAdmin(),
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));
    }

    public function fiveStepsAction(){
        $this->view->disable();
        $request = $this->request;
        if($request->isAjax() == true){
            // El usuario tiene habilitado 5 pasos?
            $user_id = $this->session->get("userId");
            $user_id = $this->session->get("userId");
            $dus = DailyUsers::find('user_id = '.$user_id);
            if($dus->count()>0){
                $phql = 'SELECT
                DailyPlanning.*
                FROM DailyPlanning
                ORDER BY DailyPlanning.date DESC';
                $dps = $this->modelsManager->executeQuery($phql);
                $last_dp = $dps->getFirst();
                $phql = 'SELECT
                DailyUsers.*
                FROM DailyUsers
                WHERE DailyUsers.parent_id = 0 AND DailyUsers.user_id ='.$user_id.'
                ORDER BY DailyUsers.date_copy DESC';
                $dus = $this->modelsManager->executeQuery($phql);
                $last_du = $dus->getFirst();
//                echo($last_dp->date .'!='. $last_du->date_copy);
//                exit;
                if($last_dp->date != $last_du->date_copy){
                    if($dps->count()>$dus->count()){
                        foreach ($dps as $dp){
                            $du_t = DailyUsers::findFirst('user_id ='.$user_id.' AND daily_planing_id = '.$dp->id);
                            if(!$du_t){
                                $new_du = new DailyUsers();
                                $new_du->user_id = $user_id;
                                $new_du->daily_planing_id = $dp->id;
                                $new_du->message = $dp->message;
                                $new_du->submessage = $dp->submessage;
                                $new_du->order_r = $dp->order_r;
                                $new_du->head = $dp->header;
                                $new_du->parent_id = 0;
                                $new_du->date_copy = $dp->date;
                                $new_du->save();
                            }
                        }
                    }
                    foreach ($dus as $du){
                        if(isset($du->dailyPlanning->id)){
                            if($du->dailyPlanning->date!=$du->date_copy){
                                $du->message = $du->dailyPlanning->message;
                                $du->submessage = $du->dailyPlanning->submessage;
                                $du->order_r = $du->dailyPlanning->order_r;
                                $du->head = $du->dailyPlanning->header;
                                $du->parent_id = 0;
                                $du->date_copy = $du->dailyPlanning->date;
                                $du->save();
                            }
                        }else{
                            $du->delete();
                        }
                    }
                }
            }else{//copy
                $dps = DailyPlanning::find();
                foreach ($dps as $dp){
                    $new_du = new DailyUsers();
                    $new_du->user_id = $user_id;
                    $new_du->daily_planing_id = $dp->id;
                    $new_du->message = $dp->message;
                    $new_du->submessage = $dp->submessage;
                    $new_du->order_r = $dp->order_r;
                    $new_du->head = $dp->header;
                    $new_du->parent_id = 0;
                    $new_du->date_copy = $dp->date;
                    $new_du->save();
                }
            }
            $phql = 'SELECT
                DailyUsers.*
                FROM DailyUsers
                WHERE DailyUsers.user_id ='.$user_id.'
                ORDER BY DailyUsers.order_r ASC';
            $dus = $this->modelsManager->executeQuery($phql);
            $fecha_hoy = new \DateTime('America/Mexico_City');
            $today = $fecha_hoy->format('Y-m-d');
            $user = Users::findFirst($user_id);
            if($today != $user->date_step_day){
                foreach ($dus as $du){
                    $du->checked =0;
                    $du->save();
                }
                $user->date_step_day = $today;
                $user->save();
            }
            $datos = array(
                "stepDays"      =>  $dus->toArray(),
                "step_of_day"   =>  $user->step_of_day
            );
            $this->response->setJsonContent($datos);
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->response->setJsonContent("None");
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }

    public function fiveStepsUpdateAction(){
        $this->view->disable();
        $request = $this->request;
        if($this->request->isAjax() == true){
            if ( $request->has('id') && $request->has('valor') ) {
                $id=$request->get('id');
                $valor=$request->get('valor');
                $user_id = $this->session->get("userId");
                $du = DailyUsers::findFirst('user_id = '.$user_id.' AND id = '.$id);
                if(isset($du)){
                    $du->checked = $valor;
                    $du->save();
                }
                $this->response->setJsonContent("Ok");
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }else{
                $this->response->setJsonContent("None");
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }
        }else{
            $this->response->setJsonContent("None");
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }

    public function fiveStepsTextAction(){
        $this->view->disable();
        $request = $this->request;
        if($this->request->isAjax() == true){
            if ( $request->has('text') ) {
                $text=$request->get('text');
                $user_id = $this->session->get("userId");
                $stepDays = StepDays::findByUserId($user_id);
                if ($stepDays->count()>0) {
                    foreach ($stepDays as $stepDay) {
                        $stepDay->text_12 = $text;
                        $stepDay->save();
                    }
                    $this->response->setJsonContent("Ok");
                    $this->response->setStatusCode(200,'Ok');
                    $this->response->send();
                }else{
                    $fecha_hoy = new \DateTime('America/Mexico_City');
                    $newstepday = new StepDays();
                    $newstepday->user_id = $user_id;
                    $newstepday->date = $fecha_hoy->format("Y-m-d H:m:s");
                    $newstepday->cb_1 = 0;
                    $newstepday->cb_2 = 0;
                    $newstepday->cb_3 = 0;
                    $newstepday->cb_4 = 0;
                    $newstepday->cb_5 = 0;
                    $newstepday->cb_6 = 0;
                    $newstepday->cb_7 = 0;
                    $newstepday->cb_8 = 0;
                    $newstepday->cb_9 = 0;
                    $newstepday->cb_10 = 0;
                    $newstepday->cb_11 = 0;
                    $newstepday->cb_12 = 0;
                    $newstepday->text_12 = $text;
                    $newstepday->save();
                    $this->response->setJsonContent("Ok");
                    $this->response->setStatusCode(200,'Ok');
                    $this->response->send();
                }
            }else{
                $this->response->setJsonContent("None");
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }
        }else{
            $this->response->setJsonContent("None");
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }

    public function fiveStepsOnOffAction(){
        $this->autoRender=false;
        if($this->request->isAjax() == true){
            // El usuario tiene habilitado 5 pasos?
            $user_id = $this->session->get("userId");
            $user = Users::findFirst($user_id);
            $stado='Desactivar ';
            $stado_b=1;
            if ($user->step_of_day==1) {
                $stado='Activar';
                $stado_b=0;
            }
            $user->step_of_day = $stado_b;
            if ($user->email==""){
                $user->email = "sincorreo@correo.com";
            }
            if ($user->rm_token==""){
                $user->rm_token = "rm_token";
            }
            if ($user->ch_token ==""){
                $user->ch_token = "ch_token";
            }
            if ($user->htd_token ==""){
                $user->htd_token = "htd_token";
            }
            if ($user->role == 1){
                $user->role = "registered";
            }
            if ($user->save()==false){
                $this->createListError($user);
            }

            $this->response->setJsonContent($stado);
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->response->setJsonContent("None");
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }
    /*
     * Funcion usada para poblar la tabla de delegates
    */
    public function delegateFixedAction(){
        $this->sdt->fixDelegate();
    }

    public function getUsersAction(){
        $this->autoRender=false;
        if($this->request->isAjax() == true){
            // Obtener los equipos de trabajo que pertenecen
            $user_id = $this->session->get("userId");
            $phql = 'SELECT
			Users.id, IFNULL( CONCAT(Profiles.name," ", Profiles.last_name), Users.username) as username
			FROM Users
			LEFT JOIN Profiles  ON Profiles.user_id = Users.id
			LEFT JOIN UsersTeams as ut1 ON ut1.user_id = Users.id
			LEFT JOIN UsersTeams as ut2 ON ut1.team_id = ut2.team_id
			WHERE ut2.user_id = '.$user_id.'
			AND Users.status = 1
			GROUP BY Users.id
            ORDER BY username ASC';
            $users = $this->modelsManager->executeQuery($phql);
            $datos = array(
                'user_id'=>$user_id,
                'users'=>$users->toArray()
            );
            $this->response->setJsonContent($datos);
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    public function getUsersProjectsAction(){
        $this->autoRender=false; // $request->has('valor')
        if($this->request->isAjax() == true){
            // Obtener los equipos de trabajo que pertenecen
            $user_id = $this->session->get("userId");
            $phql = 'SELECT
			Users.id, IFNULL( CONCAT(Profiles.name," ", Profiles.last_name), Users.username) as username
			FROM Users
			LEFT JOIN Profiles  ON Profiles.user_id = Users.id
			LEFT JOIN UsersProjects ON UsersProjects.user_id = Users.id
			WHERE UsersProjects.project_id = '.$this->request->get('id').'
			AND Users.status = 1
			GROUP BY Users.id
            ORDER BY username ASC';
            $users = $this->modelsManager->executeQuery($phql);
            $datos = array(
                'user_id'=>$user_id,
                'users'=>$users->toArray()
            );
            $this->response->setJsonContent($datos);
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }
}

