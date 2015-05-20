<?php

use RegisterForm as FormRegister; //User el formulario registro

class SigninController extends \Phalcon\Mvc\Controller
{

    public function indexAction(){
        $this->view->setVars(array(
            'title_view'=>'Registrarse',
            'form'      => new FormRegister(),
            'v_session' => $this->getDI()->getSession()->get('userId')
        ));
    }

    public function saveAction(){
        $request = $this->request;
        if($request->isPost())
        {
            $form = new FormRegister();
            if ($form->isValid($this->request->getPost()) == false)
            {
                $datetime = new \DateTime('America/Mexico_City');

                $user = new Users();
                $user->username = $request->getPost('username');
                $user->password = $request->getPost('password');
                $user->email = $request->getPost('email');
                $user->active = 1;
                $user->created_at = $datetime->format('Y-m-d H:i:s');
                // Ya se valido desde el formulario, ahora desde la base de datos
                if($user->validation()==true)
                {
                    $user->save();
                    $this->flash->success('El usuario ha sido creado');
                    $this->response->redirect('signin');
                }
                else
                {// Obtengo los mensajes enviados desde la validacion del formulario
                    foreach ($user->getMessages() as $message)
                    {
                        $this->flash->error($message);
                    }
                    return $this->dispatcher->forward(array("action" => "index"));
                }
            }
            else
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message);
                }
                //con forward mandamos a la acción index con los errores del formulario
                return $this->dispatcher->forward(array("action" => "index"));
            }
        }
        else
        {
            $this->response->redirect('signin/index');
        }
    }
}

