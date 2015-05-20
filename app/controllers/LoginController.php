<?php
use LoginForm as FormLogin;
class LoginController extends ControllerBase
{

    public function indexAction()
    {
        if ($this->session->has("userId")) {
            return $this->response->redirect($this->auth->redirectUrl());
        }else{

            //si es una petición post
            if ($this->request->isPost()==false){
                if ($this->auth->hasRememberMe()) {
                    return $this->auth->loginWithRememberMe();
                }
            }else{
                $jc = $this->jcrypt;
                $jc->go();

                $form = new FormLogin();
                // si el formulario no pasa la validación que le hemos impuesto
                if ($form->isValid($this->request->getPost()) == false){
                    $this->createListError($form);
                }else{
                    $authen = $this->auth->check(array(
                        'username_email' => $this->request->getPost('username_email', array('striptags', 'trim')),
                        'password' => $this->request->getPost('password', array('striptags', 'trim')),
                        'remember' => $this->request->getPost('remember', array('striptags', 'trim'))
                    ));
                    echo("cookie 2: ".$this->cookies->get('RMT')->getValue());
                    if($authen==true){
                        return $this->response->redirect($this->auth->redirectUrl());
                    }
                }
            }
            echo("cookie 2: ".$this->cookies->get('RMT')->getValue());

            $this->view->setVars(array(
                'title_view'=>  'Logearse',
                'form'      =>  new FormLogin()
            ));

            $this->assets
            ->addJs('js/jCryption/jquery.jcryption.3.1.0.js')
                ->addJs('js/jCryption/jcrypt_form.js');

        }
    }
    public function getPublicKeyAction(){
        $jc = $this->jcrypt;
        $jc->getPublicKey();
    }

    public function handshakeAction(){
        $jc = $this->jcrypt;
        $jc->handshake();
    }
}

