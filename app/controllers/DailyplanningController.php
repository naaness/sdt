<?php
use DailyPlanningForm as FormDailyPlanning;
use DeleteForm as FormDelete;
class DailyplanningController extends ControllerBase
{

    public function indexAction(){
        $this->addBreadcrumb('Pleneacion diaria','dailyplanning');
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $dps = DailyPlanning::find(
            array(
                'order'=>'order_r ASC'
            ));
        $this->view->setVars(array(
            'v_session'     =>  $has_user,
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'title_view'    =>  'Plantilla Planeacion Basica',
            'dps'           =>  $dps
        ));
    }
    public function addAction(){
        $request = $this->request;
        if($request->isPost()){
            $form = new FormDailyPlanning();
            if ($form->isValid($this->request->getPost()) != false){
                $dp_old = DailyPlanning::findFirst('order_r = '.$request->getPost('order_r'));
                if(!$dp_old){
                    $dp = new DailyPlanning();
                    $dp->message        =   $request->getPost('message');
                    $dp->submessage     =   $request->getPost('submessage');
                    $dp->header         =   1;
                    $dp->order_r        =   $request->getPost('order_r');
                    if($dp->save()){
                        $this->flash->success('Se ha creado el paso');
                        return $this->dispatcher->forward(array("action" => "index"));
                    }else{
                        $this->createListError($dp);
                    }
                }else{
                    $this->flash->error('Ya existe la numeracion ('.$request->getPost('order_r').')');
                }
            }else{
                $this->createListError($form);
            }
        }
        $this->addBreadcrumb('Pleneacion diaria','dailyplanning');
        $this->addBreadcrumb('Agregar paso del dia','dailyplanning/add');
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'v_session' =>  $has_user,
            'form'      => new FormDailyPlanning(),
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'title_view'    =>  'Plantilla Planeacion Basica',
        ));
        $this->assets
            ->addJs('js/daily_planning/daily_planning.js');
    }
    public function editAction($id){
        $request = $this->request;
        if($request->isPost()){
            $form = new FormDailyPlanning();
            if ($form->isValid($this->request->getPost()) != false){
                $dp_old = DailyPlanning::findFirst('order_r = '.$request->getPost('order_r').' AND id != '.$request->getPost('id'));
                if(!$dp_old){
                    $dp = DailyPlanning::findFirst($request->getPost('id'));
                    $dp->message        =   $request->getPost('message');
                    $dp->submessage     =   $request->getPost('submessage');
                    $dp->header         =   1;
                    $dp->order_r        =   $request->getPost('order_r');
                    if($dp->save()){
                        $dps= DailyPlanning::find(
                            array(
                                'r_parent_id = '.$dp->id,
                                'order' => 'order_r ASC'
                            )
                        );
                        $order=0;
                        foreach ($dps as $child_dp){
                            $order+=1;
                            $child_dp->order_r=$dp->order_r.'.'.$order;
                            $child_dp->save();
                        }
                        $this->flash->success('Se ha creado el paso');
                        return $this->dispatcher->forward(array("action" => "index"));
                    }else{
                        $this->createListError($dp);
                    }
                }else{
                    $this->flash->error('Ya existe la numeracion ('.$request->getPost('order_r').')');
                }
            }else{
                $this->createListError($form);
            }
        }
        $this->addBreadcrumb('Pleneacion diaria','dailyplanning');
        $this->addBreadcrumb('Editar paso del dia','dailyplanning/edit');
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'v_session'     =>  $has_user,
            'form'          =>  new FormDailyPlanning(DailyPlanning::findFirst($id)),
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'title_view'    =>  'Editar Plantilla Planeacion Basica',
            'id'            =>  $id
        ));
        $this->assets
            ->addJs('js/daily_planning/daily_planning.js');
    }

    public function addSubAction($id){
        $request = $this->request;
        if($request->isPost()){
            $form = new FormDailyPlanning();
            if ($form->isValid($this->request->getPost()) != false){
                $dp = DailyPlanning::findFirst($id);

                $dp_child = DailyPlanning::find(array(
                    "r_parent_id =".$id,
                    "order" => "order_r DESC",
                ));
                $last  = $dp_child->getLast();
                $order=$dp->order_r.'.1';
                $id_next = 0;

                if($last){
                    $order = explode('.',$last->order_r);
                    $order = $order[1];
                    $order = $order+1;
                    $order = $dp->order_r.'.'.$order;
                    $id_next = $last->id;
                }

                $dp = new DailyPlanning();
                $dp->message        =   $request->getPost('message');
                $dp->submessage     =   $request->getPost('submessage');
                $dp->header         =   0;
                $dp->order_r        =   $order;
                $dp->r_parent_id    =   $id;
                $dp->r_next_id      =   $id_next;
                if($dp->save()){
                    $this->flash->success('Se ha creado el subpaso');
                    return $this->dispatcher->forward(array("action" => "index"));
                }else{
                    $this->createListError($dp);
                }
            }else{
                $this->createListError($form);
            }
        }
        $this->addBreadcrumb('Pleneacion diaria','dailyplanning');
        $this->addBreadcrumb('Agregar Sub paso del dia','dailyplanning/edit');
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'v_session'     =>  $has_user,
            'form'          =>  new FormDailyPlanning(),
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'title_view'    =>  'Agregar sub paso',
            'id'            =>  $id
        ));
        $this->assets
            ->addJs('js/daily_planning/daily_planning.js');
    }

    public function editSubAction($id){
        $request = $this->request;
        if($request->isPost()){
            $form = new FormDailyPlanning();
            if ($form->isValid($this->request->getPost()) != false){
                $dp = DailyPlanning::findFirst($id);
                $dp->message        =   $request->getPost('message');
                $dp->submessage     =   $request->getPost('submessage');
                if($dp->save()){
                    $this->flash->success('Se ha editado el subpaso');
                    return $this->dispatcher->forward(array("action" => "index"));
                }else{
                    $this->createListError($dp);
                }
            }else{
                $this->createListError($form);
            }
        }
        $this->addBreadcrumb('Pleneacion diaria','dailyplanning');
        $this->addBreadcrumb('Editar Sub paso del dia','dailyplanning/edit');
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'v_session'     =>  $has_user,
            'form'          =>  new FormDailyPlanning(DailyPlanning::findFirst($id)),
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'title_view'    =>  'Agregar sub paso',
            'id'            =>  $id
        ));
        $this->assets
            ->addJs('js/daily_planning/daily_planning.js');
    }

    public function deleteAction($id){
        $request = $this->request;
        if($request->isPost()){
            $form = new FormDelete();
            if($form->isValid($request->getPost())!= false){
                $dps = DailyPlanning::find('r_parent_id = '.$id);
                foreach ($dps as $child_dp){
                    $child_dp->delete();
                }
                $dp = DailyPlanning::findFirst($id);
                if($dp->r_parent_id>0){
                    $dp_parent = DailyPlanning::findFirst($dp->r_parent_id);
                    $dp_parent->save();
                }
                $dp->delete();
                $this->flash->success('El proyecto ha sido eliminado');
                return $this->dispatcher->forward(array("action" => "index"));
            }
        }
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }

        $this->addBreadcrumb('Pleneacion diaria','dailyplanning');
        $this->addBreadcrumb('Eliminar','dailyplanning/edit');
        $has_user = $this->session->has("userId");
        $this->view->setVars(array(
            'v_session'     =>  $has_user,
            'form'          =>  new FormDelete(DailyPlanning::findFirst($id)),
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'title_view'    =>  'Eliminar paso',
            'id'            =>  $id
        ));
        $this->assets
            ->addJs('js/daily_planning/daily_planning.js');
    }

    public function getDailyPlanningAction(){
        $this->autoRender=false;
        if($this->request->isAjax() == true){
            $user_id = $this->session->get("userId");
            $dus = DailyUsers::find('user_id = '.$user_id);
            if($dus->count()>0){
//                $dps = DailyPlanning::find(array('order '=>'order_r DESC'));
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
            $this->response->setJsonContent($dus->toArray());
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }
}

