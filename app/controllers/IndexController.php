<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
            $user = Users::findFirst($this->session->get("userId"));
            $username = " ".$user->username;
            if (!empty($user->profiles)) {
                $username=" ".$user->profiles->name . ' '.$user->profiles->last_name;
            }
            $remember = RememberTokens::find('user_id ='.$this->session->get("userId"));
            $remember = $remember->getLast();
            $diffe = $remember->created_at-(time() - (86400 * 8));
            if($diffe>=0){
                $dias = $this->sdt->getDaysDiff($diffe);
                $diffe-=$dias*(3600*24);
                $horas = $this->sdt->getHoursDiff($diffe);
                $diffe-=$horas*(3600);
                $minutos = $this->sdt->getMinutesDiff($diffe);
                $diffe-=$minutos*(60);
                $segundos = $diffe;
                $cosas = "Tiempo de session : ".$dias. ' dias, '.$horas. ' horas, '.$minutos. ' minutos, '.$segundos. ' segundos';
            }else{
                $cosas='';
            }
        }else{
            if ($this->auth->hasRememberMe()) {
                return $this->auth->loginWithRememberMe();
            }
        }
        $this->view->setVars(array(
            'title_view'=>  'Home',
            'v_session' =>  $has_user,
            'username'  =>  $username,
            'cosas'     =>  $cosas
        ));
    }

}

