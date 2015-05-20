<?php
use LabelForm as FormLabel;
use \Phalcon\Paginator\Adapter\Model as Paginacion;

class RmlabelsController extends ControllerBase
{

    public function indexAction()
    {
        $user_id = $this->session->get("userId");
        //Crea un paginador, muestra 3 filas por página
        $paginator = new Paginacion(
            array(
                //obtenemos los productos
                "data" => RmLabels::findByUserId($user_id ,array(
                            "order" => "name ASC"
                        )
                    ),
                //limite por página
                "limit"=> 10,
                //variable get page convertida en un integer
                "page" => $this->request->getQuery('page', 'int')
            )
        );

        //pasamos el objeto a la vista con el nombre de $page
        $this->view->page = $paginator->getPaginate();

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');
        $this->addBreadcrumb('Etiquetas','rmlabels');
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Listado de Etiquetas',
            'v_session' => $has_user,
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));

    }

    public function addAction(){
        $request = $this->request;
        $user_id = $this->session->get("userId");
        if($request->isPost())
        {
            $form = new FormLabel();
            if($form->isValid($request->getPost())!= false)
            {
                $category = new RmLabels();
                $category->user_id = $user_id;
                $category->name = $request->get('name');
                $category->color = $request->get('color');
                $category->b_color = $request->get('b_color');
                $category->b_color_checked = $request->get('b_color_checked');
                $category->rm_font_id = $request->get('rm_font_id');
                $category->rm_size_id = $request->get('rm_size_id');
                if (!$category->save()) {
                    foreach ($form->getMessages() as $message)
                    {
                        $this->flash->error($message);
                    }
                }else{
                    $this->flash->success('La Etiqueta ha sido creado');

                }
                return $this->dispatcher->forward(array("action" => "index"));
            }
            else
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message);
                }
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');
        $this->addBreadcrumb('Etiquetas','rmlabels');
        $this->addBreadcrumb('Crear Etiqueta','rmLabels/add');

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Crear Etiqueta',
            'v_session' => $has_user,
            'form'      => new FormLabel(),
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));

        // Agregar los archivos js necesarios

        $this->assets
            ->addJs('js/raphael-min.js')
            ->addJs('js/colorpicker.js')
            ->addJs('js/sdt_rm_label.js');
    }

    public function editAction($id){
        $request = $this->request;

        if($request->isPost())
        {
            $form = new FormLabel();
            if($form->isValid($request->getPost())!= false)
            {
                $label = RmLabels::findFirst($request->get('id'));
                $label->name = $request->get('name');
                $label->color = $request->get('color');
                $label->b_color = $request->get('b_color');
                $label->b_color_checked = $request->get('b_color_checked');
                $label->rm_font_id = $request->get('rm_font_id');
                $label->rm_size_id = $request->get('rm_size_id');
                if (!$label->save()) {
                    foreach ($form->getMessages() as $message)
                    {
                        $this->flash->error($message);
                    }
                }else{
                    $this->flash->success('La Etiqueta ha sido actualizada');

                }
                return $this->dispatcher->forward(array("action" => "index"));
            }
            else
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message);
                }
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('[ HTD','htd');
        $this->addBreadcrumb('Checklist','checklist');
        $this->addBreadcrumb('RM ]','rmregistries');
        $this->addBreadcrumb('Etiquetas','rmlabels');
        $this->addBreadcrumb('Editar Etiqueta','rmlabels/edit');

        $labels = RmLabels::findFirst($id);

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>  'Editar Etiqueta',
            'v_session' =>  $has_user,
            'form'      =>  new FormLabel($labels),
            'breadcrumb'=>  $this->createHtmlBreadcrumb(),
            'label'     =>  $labels
        ));

        // Agregar los archivos js necesarios

        $this->assets
            ->addJs('js/raphael-min.js')
            ->addJs('js/colorpicker.js')
            ->addJs('js/sdt_rm_label.js');
    }

    public function traslateRMtoHTD(){
        $this->view->disable();
        $request = $this->request;
        if($this->request->isAjax() == true){
            if (!($request->has('name') && $request->has('description') && $request->has('id_rm') && $request->has('date') && $request->has('priority'))) {
                $user_id = $this->session->get("userId");
                // Crear Tarea
                $fecha_hoy = new \DateTime('America/Mexico_City');

                $task = new Tasks();
                $task->project_id = 0;
                $task->package_id = 0;
                $task->name = $request->get('name');
                $task->description = $request->get('description');
                $task->created = $fecha_hoy->format('Y-m-d H:M::S');
                $task->modified = $fecha_hoy->format('Y-m-d H:M::S');
                $task->status = 1;
                $task->user_id = $user_id;
                $task->priority_id = $request->get('priority');
                $task->percent = '00.00';
                $task->task_id_parent = 0;

                if ($task->save()) {
                    $task = Tasks::find();
                    $last_rm = $task->getLast();
                    $last_task_id = $last_rm->id;
                    // Crear la relacion usuario tarea
                    $usertask = new UsersTasks();
                    $usertask->user_id = $user_id;
                    $usertask->task_id = $last_task_id;

                    $usertask->save();

                    // Crear relacion rm_registro y tarea
                    $rmregistrytask = new  RmRegistriesTasks();
                    $rmregistrytask->rm_registry_id = $request->get('id_rm');
                    $rmregistrytask->task_id = $last_task_id;
                    $rmregistrytask->created = $fecha_hoy->format('Y-m-d H:M::S');
                    $rmregistrytask->modified = $fecha_hoy->format('Y-m-d H:M::S');
                    $rmregistrytask->day = $fecha_hoy->format('Y-m-d H:M::S');

                    $rmregistrytask->save();

                    // Crear una unidad de tiempo para esa tarea
                    $date = explode('/', $request->get('date'));
                    $datos_ = array(
                        'task_id'=>$last_task_id,
                        'start_day'=>$date[2].'-'.$date[1].'-'.$date[0],
                        'follow_up'=>1,
                        'unit_time_id_parent'=>0
                    );
                    $this->Task->UnidTime->read(null, null);
                    $this->Task->UnidTime->create();
                    $this->Task->UnidTime->set($datos_);
                    $this->Task->UnidTime->save();

                    $unidtime = new UnidTimes();
                    $unidtime->task_id = $last_task_id;
                    $unidtime->created = $fecha_hoy->format('Y-m-d H:M::S');
                    $unidtime->modified = $fecha_hoy->format('Y-m-d H:M::S');
                    $unidtime->start_day = $date[2].'-'.$date[1].'-'.$date[0];
                    $unidtime->priority_id = $request->get('priority');
                    $unidtime->save();

                    $this->sdt->createAlert($unidtime,$user_id,"newActivity",$task);

                    $this->response->setJsonContent("Ok");
                    $this->response->setStatusCode(200,'Ok');
                    $this->response->send();
                }else{
                    $this->response->setJsonContent("None");
                    $this->response->setStatusCode(200,'Ok');
                    $this->response->send();
                }
            }else{
                $this->response->setJsonContent("None");
                $this->response->setStatusCode(200,'Ok');
                $this->response->send();
            }
        }else{
            $this->response->setJsonContent("None");
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }

}

