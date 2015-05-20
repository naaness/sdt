<?php

class LogoutController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $this->auth->remove();
        return $this->response->redirect('index');
    }

}

