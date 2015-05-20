<?php

class ContactController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $this->view->setVars(array(
            'title_view'=>'Tejidos',
            'v_session' => $this->session->has("userId")
        ));
    }

}

