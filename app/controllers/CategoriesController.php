<?php
use CategoryForm as FormCategory;
//obtenemos el adaptador que crea la paginación en Phalcon
use \Phalcon\Paginator\Adapter\Model as Paginacion;

class CategoriesController extends ControllerBase
{

    public function indexAction()
    {
        // $categories = Categories::find();
        //Crea un paginador, muestra 3 filas por página
        $paginator = new Paginacion(
            array(
                //obtenemos los productos
                "data" => Categories::find(array(
                            "order" => "name"
                        )
                    ),
                //limite por página
                "limit"=> 3,
                //variable get page convertida en un integer
                "page" => $this->request->getQuery('page', 'int')
            )
        );

        //pasamos el objeto a la vista con el nombre de $page
        $this->view->page = $paginator->getPaginate();

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('[ HTD','Htd');
        $this->addBreadcrumb('Checklist','Checklist');
        $this->addBreadcrumb('RM ]','RmRegistries');

        $this->view->setVars(array(
            'title_view'=>'Administracion de Articulos',
            'v_session' => $this->getDI()->getSession()->get('userId'),
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));
    }

    public function viewAction($id=null)
    {
        $categories = Categories::findFirst($id);
        $this->view->setVars(array(
            'title_view'=>'Administracion de Articulos',
            'v_session' => $this->session->has("userId"),
            'categories'=>$categories
        ));
    }

    public function editAction($id=null)
    {
        $request = $this->request;
        $user_id = $this->session->get("userId");
        if($request->isPost())
        {
            $form = new FormCategory();
            if($form->isValid($request->getPost())== true)
            {
                $datetime = new \DateTime('America/Mexico_City');


                $id = $this->request->getPost("id", "int");
                $category = Categories::findFirstById($id);

                $category->name = $request->getPost('name');
                $category->description = $request->getPost('description');
                $category->modified = $datetime->format('Y-m-d H:i:s');

                // Ajustes para que phalco reconozca lo elementos check
                $category->status = 1;
                if ($request->getPost('status')==''){
                    $category->status = 0;
                }
                $category->user_modify = $user_id;
                if (!$category->save()) {
                    foreach ($form->getMessages() as $message)
                    {
                        $this->flash->error($message);
                    }
                }else{
                    $this->flash->success('La categoria ha sido editada');

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
        $this->addBreadcrumb('Categorias','categories');
        $this->addBreadcrumb('Editar Categoria');

        $categories = Categories::findFirst($id);
        $this->view->setVars(array(
            'title_view'=>'Administracion de Articulos',
            'v_session' => $this->session->has("userId"),
            'categories'=> $categories,
            'form'      => new FormCategory($categories),
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));
    }
    public function deleteAction($id=null)
    {
        $category = Categories::findFirstById($id);
        if (count($category)>0){
            $category->delete();
            $this->flash->success('Se ha eliminado la categoria');
        }
        return $this->dispatcher->forward(array("action" => "index"));
    }
    public function addAction()
    {
        $request = $this->request;
        $user_id = $this->session->get("userId");
        if($request->isPost())
        {
            $form = new FormCategory();
            if($form->isValid($request->getPost())!= false)
            {
                $datetime = new \DateTime('America/Mexico_City');

                $category = new Categories();
                $category->name = $request->getPost('name');
                $category->description = $request->getPost('description');
                $category->created = $datetime->format('Y-m-d H:i:s');
                $category->modified = $datetime->format('Y-m-d H:i:s');
                $category->status = 1;
                $category->user_create = $user_id;
                $category->user_modify = $user_id;
                if (!$category->save()) {
                    foreach ($form->getMessages() as $message)
                    {
                        $this->flash->error($message);
                    }
                }else{
                    $this->flash->success('La categoria ha sido creado');

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
        $this->addBreadcrumb('Categorias','categories');
        $this->addBreadcrumb('Editar Categoria');

        $this->view->setVars(array(
            'title_view'=>'Crear Categoria',
            'v_session' => $this->session->has("userId"),
            'form'      => new FormCategory(),
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));
    }

    public function sendEmailAction(){
        // Obtener el usuario
        $user_id = $this->session->get("userId");
        $user = Users::findFirst($user_id);

        $this->getDI()->getMail()->send(
            array(
                $user->email => $user->username
            ),
            "Please confirm your email",
            'confirmation',
            array(
                'confirmUrl' => '/confirm/5646546546546/' . 'gaminoso@yahoo.es'
            )
        );
        // return $this->response->redirect('categories');

        // return $this->dispatcher->forward(array("action" => "index"));

        $this->flash->success("Fue enviado un mensaje a ".$user->email);

        $this->view->setVars(array(
            'title_view'=>'Email'
        ));
    }
}
