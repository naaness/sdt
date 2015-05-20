<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 2/04/15
 * Time: 09:59 PM
 */
use Phalcon\Mvc\User\Component;
/**
 *
 * Administrador Autenticacion/Identificacion en Phalcon
 */
class Auth extends Component
{
    /**
     * Verifica las credenciales del usuairo
     *
     * @param array $credentials
     * @return boolan
     */
    public function check($credentials)
    {
        // Verifica si el username o correo existen
        $user = Users::findFirstByEmail($credentials['username_email']);
        if ($user == false) {
            $user = Users::findFirstByUsername($credentials['username_email']);
            if ($user == false) {
                $this->flash->error("Username/Email o contraseña incorrectos");
                // return $this->dispatcher->forward(array("action" => "index"));
                return false;
            }
        }
        // verifica si la contraseña existe
        if (sha1(md5($credentials['password'])) != $user->password){
            $this->flash->error("Username/Email o contraseña incorrectos");
            // return $this->dispatcher->forward(array("action" => "index"));
            return false;
        }
        // Check if the user was flagged
        $this->checkUserFlags($user);
        // Register the successful login
        $this->saveSuccessLogin($user);
        // Check if the remember me was selected
        if (isset($credentials['remember'])) {
            $this->createRememberEnviroment($user);
        }
        //creamos la sesión del usuario con su email
        $this->session->set("userId", $user->id);
        $this->session->set("email", $user->email);
        $this->session->set("usu_role", $user->role);
        return true;
    }
    /**
     *
     *
     *
     */
    public function saveSuccessLogin($user)
    {
        $successLogin = new SuccessLogins();
        $successLogin->user_id = $user->id;
        $successLogin->ip_address = $this->request->getClientAddress();
        $successLogin->user_agent = $this->request->getUserAgent();
        if (!$successLogin->save()) {
            $messages = $successLogin->getMessages();
            throw new Exception($messages[0]. ' sadasda');
        }
    }
    /**
     * Implements login throttling
     * Reduces the efectiveness of brute force attacks
     *
     * @param int $userId
     */
    public function registerUserThrottling($userId)
    {
        $failedLogin = new FailedLogins();
        $failedLogin->usersId = $userId;
        $failedLogin->ipAddress = $this->request->getClientAddress();
        $failedLogin->attempted = time();
        $failedLogin->save();
        $attempts = FailedLogins::count(array(
            'ipAddress = ?0 AND attempted >= ?1',
            'bind' => array(
                $this->request->getClientAddress(),
                time() - 3600 * 6
            )
        ));
        switch ($attempts) {
            case 1:
            case 2:
                // no delay
                break;
            case 3:
            case 4:
                sleep(2);
                break;
            default:
                sleep(4);
                break;
        }
    }
    /**
     * Crea el entorno y configuracion para recuerdame con cookies y generando token
     *
     *
     */
    public function createRememberEnviroment(Users $user)
    {
        $userAgent = $this->request->getUserAgent();
        $token = md5($user->email . $user->password . $userAgent);
        $remember = new RememberTokens();
        $remember->user_id = $user->id;
        $remember->token = $token;
        $remember->user_agent = $userAgent;
        if ($remember->save() != false) {
            // echo("</br>1111");
            if ($this->cookies->has('RMU')) {
                $this->cookies->get('RMU')->delete();
            }
            if ($this->cookies->has('RMT')) {
                $this->cookies->get('RMT')->delete();
            }
            $expire = time() + 86400 * 8;
            $this->cookies->set('RMU', $user->id, $expire,'/');
            $this->cookies->set('RMT', $token, $expire,'/');
        }
    }
    /**
     * Check if the session has a remember me cookie
     *
     * @return boolean
     */
    public function hasRememberMe()
    {
        return $this->cookies->has('RMU');
    }
    /**
     * Logs on using the information in the coookies
     *
     * @return Phalcon\Http\Response
     */
    public function loginWithRememberMe()
    {
        // echo("</br>cosas raras");
        $userId = $this->cookies->get('RMU')->getValue();
        $cookieToken = $this->cookies->get('RMT')->getValue();
        $user = Users::findFirstById($userId);
        if ($user) {

            $userAgent = $this->request->getUserAgent();
            $token = md5($user->email . $user->password . $userAgent);
            if ($cookieToken == $token) {
                $remember = RememberTokens::find(array(
                    'user_id = ?0 AND token = ?1',
                    'bind' => array(
                        $user->id,
                        $token
                    )
                ));
                $remember = $remember->getLast();
                if ($remember) {
                    // echo("</br>cosas 3");
                    // Check if the cookie has not expired
                    if ((time() - (86400 * 8)) < $remember->created_at) {
                        // Check if the user was flagged
                        $this->checkUserFlags($user);
                        //creamos la sesión del usuario con su email
                        $this->session->set("userId", $user->id);
                        $this->session->set("email", $user->email);
                        $this->session->set("usu_role", $user->role);
                        // Register the successful login
                        $this->saveSuccessLogin($user);
                        return $this->response->redirect($this->redirectUrl());
                    }
                }
                // destruir todas las variables de sesion
                $this->remove();
            }else{
                $this->remove();
            }
        }else{
            $this->cookies->get('RMU')->delete();
            $this->cookies->get('RMT')->delete();
        }
        return $this->response->redirect('login');
    }
    /**
     * Verifica si el estado del usuario baneado/inactivo/suspendido
     *
     *
     */
    public function checkUserFlags(Users $user)
    {
        if ($user->status == 0) {
            $this->flash->error("Su cuenta no esta activa");
            return $this->dispatcher->forward(array("action" => "index"));
        }
        if ($user->banned == 2) {
            $this->flash->error("Su cuenta ha baneada");
            return $this->dispatcher->forward(array("action" => "index"));
        }
        if ($user->suspended == 3) {
            $this->flash->error("Su cuenta ha sido suspendida");
            return $this->dispatcher->forward(array("action" => "index"));
        }
    }
    /**
     * Returns the current identity
     *
     * @return array
     */
    public function getIdentity()
    {
        return $this->session->get('auth-identity');
    }
    /**
     * Returns the current identity
     *
     * @return string
     */
    public function getName()
    {
        $identity = $this->session->get('auth-identity');
        return $identity['name'];
    }
    /**
     * Removes the user identity information from session
     */
    public function remove()
    {
//        echo("cookie 3: ".$this->cookies->has('RMU'));
//        exit;
        if ($this->cookies->has('RMU')) {
            $this->cookies->get('RMU')->delete();
        }
        if ($this->cookies->has('RMT')) {
            $this->cookies->get('RMT')->delete();
        }
        if ($this->session->has("userId")) {
            $this->session->remove("userId");
        }
        if ($this->session->has("email")) {
            $this->session->remove("email");
        }
        if ($this->session->has("usu_role")) {
            $this->session->remove("usu_role");
        }
        if ($this->session->has("ctr")) {
            $this->session->remove("ctr");
        }
        if ($this->session->has("act")) {
            $this->session->remove("act");
        }
        if ($this->session->has("par")) {
            $this->session->remove("par");
        }
    }
    /**
     * Auths the user by his/her id
     *
     * @param int $id
     */
    public function authUserById($id)
    {
        $user = Users::findFirstById($id);
        if ($user == false) {
            throw new Exception('The user does not exist');
        }
        $this->checkUserFlags($user);
        $this->session->set('auth-identity', array(
            'id' => $user->id,
            'name' => $user->name,
            'profile' => $user->profile->name
        ));
    }
    /**
     * Get the entity related to user in the active identity
     *
     *
     */
    public function getUser()
    {
        $identity = $this->session->get('auth-identity');
        if (isset($identity['id'])) {
            $user = Users::findFirstById($identity['id']);
            if ($user == false) {
                throw new Exception('The user does not exist');
            }
            return $user;
        }
        return false;
    }

    public function redirectUrl(){
        if($this->session->has("ctl")){
            $para="";
            if($this->session->get("par")!=""){
                $para = '/'.implode("/",$this->session->get("par"));
            }
            $action = '/'.$this->session->get("act");
            if($para==""){
                $action="";
            }
            $ruta = $this->session->get("ctl").$action.$para;
            if ($this->session->has("ctl")) {
                $this->session->remove("ctl");
            }
            if ($this->session->has("act")) {
                $this->session->remove("act");
            }
            if ($this->session->has("par")) {
                $this->session->remove("par");
            }
            if ($this->session->has("jCryptionKey")) {
                $this->session->remove("jCryptionKey");
            }

            return $ruta;
        }else{
            return 'index';
        }
    }
}