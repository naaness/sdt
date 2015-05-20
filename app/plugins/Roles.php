<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 9/02/15
 * Time: 03:38 PM
 */

use Phalcon\Events\Event,
    Phalcon\Mvc\User\Plugin,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Acl;

/*
* plugin roles para llevar a cabo roles de usuarios en Phalcon
*/
class Roles extends Plugin
{
    /**
     * lógica para crear una aplicación con roles de usuarios
     */
    public function getAcl()
    {
//         if (!isset($this->persistent->acl))
//         {
            //creamos la instancia de acl para crear los roles
            $acl = new Phalcon\Acl\Adapter\Memory();

            //por defecto la acción será denegar el acceso a cualquier zona
            $acl->setDefaultAction(Phalcon\Acl::DENY);

            //registramos los roles que deseamos tener en nuestra aplicación
            $roles = array(
                'superadmin'            => new Phalcon\Acl\Role('superadmin'),
                'admin'                 => new Phalcon\Acl\Role('admin'),
                'registered'            => new Phalcon\Acl\Role('registered'),
                'guest'                 => new Phalcon\Acl\Role('guest')
            );

            // añadimos los roles al acl
            foreach ($roles as $role){
                $acl->addRole($role);
            }

            // zonas accesibles sólo para role admin
            $adminAreas = array(
                'users'         =>  array('index', 'add', 'edit', 'changePasswordAdmin','delegateFixed'),
                'categories'    =>  array('index', 'add', 'edit', 'delete'),
                'items'         =>  array('index', 'add', 'edit', 'delete', 'view'),
                'teams'         =>  array('add','edit'),
                'dailyplanning' =>  array('index','add', 'edit','addSub','editSub','delete')
            );

            //añadimos las zonas de administrador a los recursos de la aplicación
            foreach ($adminAreas as $resource => $actions) {
                $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
            }

            //zonas protegidas sólo para usuarios registrados de la aplicación
            $registeredAreas = array(
                'users'         =>  array('view','fiveSteps','fiveStepsUpdate','fiveStepsText','fiveStepsOnOff','getUsers','getUsersProjects'),
                'contact'       =>  array('index'),
                'profile'       =>  array('index', 'changePassword'),
                'rmregistries'  =>  array('index','get_registries','get_days','search_word','new_registry','update_registry','send_email','get_labels_b_colors','get_label','traslateRMtoHTD','getRegistry','delete_registry','getRegistriesJson','updateRegistry','createRegistry'),
                'rmlabels'      =>  array('index','add','edit'),
                'checklist'     =>  array('index','sdtChecklistJson','addRepeat','getRepeatConf','home','sdtChecklistView','sdtChangeFolloUp','sdtUnitTimeTransfer','sdtAddUnitTime','sdtDeleteUnitTime','sdtChangeDayStart','delegatesToMe','myDelegates','myProjects','projects'),
                'teams'         =>  array('index','view','delete'),
                'projects'      =>  array('index','add','edit','delete','view','getProjects'),
                'packages'      =>  array('index','add','edit','delete','view','createUsePackage','usePackage','noUsePackage'),
                'tasks'         =>  array('index','add','edit','delete','view','getTaskInfo','giveAnswer','getTask','updateTask','newTask','traslateRMtoHTD','reactiveTask','deleteTask','getTaskChat','setTaskChat','getTaskChatScroll','getNewsMessagesTask'),
                'htd'           =>  array('index','getTasks','aupdatePriority','UpdateFollowUp','delegate','oauth2callback','getEvents','get_days'),
                'posts'         =>  array('index','add','edit','view','newPost','like','dislike'),
                'alerts'        =>  array('index'),
                'notifications' =>  array('index','wasSeen','news'),
                'dailyplanning' =>  array('view','getDailyPlanning')
            );

            //añadimos las zonas para usuarios registrados a los recursos de la aplicación
            foreach ($registeredAreas as $resource => $actions) {
                $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
            }

            //zonas públicas de la aplicación
            $publicAreas = array(
                'index'     =>  array('index'),
                'about'     =>  array('index'),
                'login'     =>  array('index','getPublicKey','handshake'),
                'logout'    =>  array('index'),
                'tasks'     =>  array('cronJobSendEmail')
            );

            //añadimos las zonas públicas a los recursos de la aplicación
            foreach ($publicAreas as $resource => $actions) {
                $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
            }

            //damos acceso a todos los usuarios a las zonas públicas de la aplicación
            foreach ($roles as $role) {
                foreach ($publicAreas as $resource => $actions) {
                    foreach ($actions as $action){
                        $acl->allow($role->getName(), $resource, $action);
                    }
                }
            }

            //damos acceso a la zona de admins solo a rol Admin
            foreach ($adminAreas as $resource => $actions){
                foreach ($actions as $action) {
                    $acl->allow('admin', $resource, $action);
                }
            }

            //damos acceso a las zonas de registro tanto a los usuarios
            //registrados como al admin
            foreach ($registeredAreas as $resource => $actions) {
                //damos acceso a los registrados
                foreach ($actions as $action) {
                    $acl->allow('registered', $resource, $action);
                }
                //damos acceso al admin
                foreach ($actions as $action) {
                    $acl->allow('admin', $resource, $action);
                }
            }

            //El acl queda almacenado en sesión
            $this->persistent->acl = $acl;
//         }

        return $this->persistent->acl;
    }

    /**
     * Esta acción se ejecuta antes de ejecutar cualquier acción en la aplicación
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {

        // //esta sesión sólo la tendrá el admin
        // $admin = $this->session->get('admin');
        // //esta sesión sólo la tendrá el usuario registrado
        // $registered = $this->session->get('registered');

        // //si no es admin ni un usuario registrado es guest
        // if (!$admin && !$registered)
        // {
        //     $role = 'Guest';
        // }
        // //si es admin
        // else if($admin)
        // {
        //     $role = 'Admin';
        // }
        // //en otro caso es un usuario registrado
        // else
        // {
        //     $role = 'Registered';
        // }

        // Modificacion del plugin
        $has_user = $this->session->has('usu_role');
        if(!$has_user){
            $role = 'guest';
        }else{
            $role = $this->session->get('usu_role');
            if(!$role){
                $role = 'guest';
            }
        }

        //nombre del controlador al que intentamos acceder
        $controller = $dispatcher->getControllerName();

        //nombre de la acción a la que intentamos acceder
        $action = $dispatcher->getActionName();

        //obtenemos la Lista de Control de Acceso(acl) que hemos creado
        $acl = $this->getAcl();

        //boolean(true | false) si tenemos permisos devuelve true en otro caso false
        $allowed = $acl->isAllowed($role, $controller, $action);

        //si el usuario no tiene acceso a la zona que intenta acceder
        //le mostramos el contenido de la función index del controlador index
        //con un mensaje flash

        if($allowed==''){
            $allowed=0;
        }
        // $this->flash->error("Verificando acceso " . $this->dispatcher->getControllerName().'/'.$this->dispatcher->getActionName().'/'.implode("/",$this->dispatcher->getParams()) . ' --  '.$allowed . ' != ' . Acl::ALLOW . '  '.$role);


        if ($allowed != Acl::ALLOW){
            if ($has_user){
//                 $this->flash->error("Zona restringida, no puedes entrar aquí!" . $controller . " ".$action);
                $this->flash->error("No tienes permisos para acceder");
                if($this->session->has("ctl")){
                    $dispatcher->forward(
                        array(
                            'controller'    => $this->session->get("ctl"),
                            'action'        => $this->session->get("act"),
                            'params'        => $this->session->get("par")
                        )
                    );
                }else{
                    // return $this->response->redirect('index');
                    $dispatcher->forward(
                        array(
                            'controller' => 'index',
                            'action' => 'index'
                        )
                    );
                }
            }else{
                // return $this->response->redirect('index');
                if($this->dispatcher->getControllerName()!="login"){
                    $request = new Phalcon\Http\Request();
                    if(!$request->isAjax() && !$request->isGet()&& !$request->isPost()){
                        $this->session->set("ctl", $this->dispatcher->getControllerName());
                        $this->session->set("act", $this->dispatcher->getActionName());
                        $this->session->set("par", $this->dispatcher->getParams());
                    }
                }
                $dispatcher->forward(
                    array(
                        'controller' => 'login',
                        'action' => 'index'
                    )
                );
            }
            return false;
        }
        return true;

    }
}