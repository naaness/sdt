<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 1/02/15
 * Time: 04:01 PM
 */
class AboutController extends \Phalcon\Mvc\Controller
{
    public function indexAction()
    {
        $this->view->setVars(array(
            'title_view'=>'Tejidos',
            'v_session' => $this->session->has("userId")
        ));
    }
}