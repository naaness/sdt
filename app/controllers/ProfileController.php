<?php
use EditUserForm as FormEditUser; //User el formulario para cambiode contraseña por el usuairo
use ProfileForm as FormProfile;
use PasswordForm as FormPassword;
class ProfileController extends ControllerBase
{

    public function indexAction()
    {

        $id = $this->session->get("userId");
        $request = $this->request;
        if($request->isPost())
        {
            if ($this->request->getPost('typeForm') == 'user') {
                $form = new FormEditUser();
                if($form->isValid($request->getPost())== true)
                {
                    $user = Users::findFirstById($id);

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
                        $user->save();
                        $this->flash->success('El Usuario ha sido actualizado');
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
            }elseif ($this->request->getPost('typeForm') == 'profile') {
                $form = new FormProfile();
                if($form->isValid($request->getPost())== true)
                {
                    $profile = Profiles::findFirstByUser_id($id);
                    if (empty($profile)) {
                        // Si es vacio crear un perfil
                        $profile = new Profiles();
                        $profile->user_id = $id;
                        $profile->name = $request->getPost('name');
                        $profile->last_name = $request->getPost('last_name');
                        $profile->description = $request->getPost('description');

                        $profile->about_bio = $request->getPost('about_bio');
                        $profile->about_job = $request->getPost('about_job');
                        $profile->position = $request->getPost('position');
                        $profile->company = $request->getPost('company');
                        $profile->phone = $request->getPost('phone');
                        $profile->mobile_phone = $request->getPost('mobile_phone');

                        $profile->url_photo = $request->getPost('url_photo');
                        $profile->navbar_color = $request->getPost('navbar_color');
                        $profile->body_color = $request->getPost('body_color');
                        if ($profile->save()){;
                            $this->flash->success('El perfil del Usuario ha sido creado');
                        }
                        else
                        {
                            $this->createListError($profile);
                        }
                    }else{
                        // Actualizar el perfil
                        $profile->name = $request->getPost('name');
                        $profile->last_name = $request->getPost('last_name');
                        $profile->description = $request->getPost('description');

                        $profile->about_bio = $request->getPost('about_bio');
                        $profile->about_job = $request->getPost('about_job');
                        $profile->position = $request->getPost('position');
                        $profile->company = $request->getPost('company');
                        $profile->phone = $request->getPost('phone');
                        $profile->mobile_phone = $request->getPost('mobile_phone');

                        $profile->url_photo = $request->getPost('url_photo');
                        $profile->subject_email = $request->getPost('subject_email');
                        $profile->navbar_color = $request->getPost('navbar_color');
                        $profile->body_color = $request->getPost('body_color');
                        if ($profile->save()){;
                            $this->flash->success('El perfil del Usuario ha sido actualizado');
                        }
                        else
                        {
                            $this->createListError($profile);
                        }
                    }
                }
                else
                {
                    $this->createListError($form);
                }
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('Usuarios','users');
        $this->addBreadcrumb('Editar Usuario');

        $user = Users::findFirstById($id);
        $profile = Profiles::findFirstByUser_id($id);
        $datetime = new \DateTime('America/Mexico_City');
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>  'Editar Perfil',
            'v_session' =>  $has_user,
            'user'      =>  $user,
            'id'        =>  $id,
            'time'      =>  $datetime->format('d/m/Y'),
            'fechahoySmart' =>  $this->sdt->dateStringSmart($datetime->format('d/m/Y')),
            'profile'   =>  $profile,
            'username'  =>  $user->username
        ));

        $this->forms->set('formedit', new FormEditUser($user));
        if (empty($profile)) {
            $this->forms->set('profile', new FormProfile());
        }else{
            $this->forms->set('profile', new FormProfile($profile));
        }
        $this->assets
            ->addJs('js/raphael-min.js')
            ->addJs('js/colorpicker.js')
            ->addJs('js/profile.js');
    }

    public function changePasswordAction(){
        $id = $this->session->get("userId");
        $request = $this->request;
        if($request->isPost())
        {
            $form = new FormPassword();
            if($form->isValid($request->getPost())== true)
            {
                $user = Users::findFirstById($id);
                $user->password = sha1(md5($request->getPost('newpassword')));
                if($user->save()){
                    $this->flash->success('La contraseña se ha actualizado');
                }else{
                    $this->flash->error('Ha ocurrido un problema mientras se actualizaba');
                }
            }
        }
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Cambiar Contraseña',
            'v_session' => $has_user,
            'form' => new FormPassword()
        ));
    }

}

