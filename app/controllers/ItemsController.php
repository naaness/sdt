<?php
use ItemForm as FormItem;
use \Phalcon\Paginator\Adapter\Model as Paginacion;
class ItemsController extends ControllerBase
{

    public function indexAction($id)
    {
        // $categories = Categories::find();
        //Crea un paginador, muestra 3 filas por página
        $paginator = new Paginacion(
            array(
                //obtenemos los productos
                "data" => Items::find(array(
                            "subcategory_id = '".$id."'",
                            "order" => "name"
                        )
                    ),
                "limit"=> 3,
                //variable get page convertida en un integer
                "page" => $this->request->getQuery('page', 'int')
            )
        );

        //pasamos el objeto a la vista con el nombre de $page
        $this->view->page = $paginator->getPaginate();

        // Obtener el id de la categoria
        $subcategory = Subcategories::findFirst($id);

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('Categorias','categories');
        $this->addBreadcrumb('Sub Categorias','subcategories/index/'.$subcategory->category_id);
        $this->addBreadcrumb('Articulos','items/index/'.$subcategory->id);

        $this->view->setVars(array(
            'title_view'=>'Administracion de Articulos',
            'v_session' => $this->session->has("userId"),
            'subcategory' => $subcategory,
            'id'=>$id,
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));
    }

    public function addAction($id)
    {
        $request = $this->request;
        $user_id = $this->session->get("userId");
        if($request->isPost())
        {
            $form = new FormItem();
            if($form->isValid($request->getPost())!= false)
            {
                $datetime = new \DateTime('America/Mexico_City');

                $item = new Items();
                $item->subcategory_id = $id;
                $item->name = $request->getPost('name');
                $item->description = $request->getPost('description');
                $item->created = $datetime->format('Y-m-d H:i:s');
                $item->modified = $datetime->format('Y-m-d H:i:s');
                $item->status = 1;
                $item->user_create = $user_id;
                $item->user_modify = $user_id;
                $item->url_photo = $request->getPost('url_photo');
                $item->price = $request->getPost('price');
                if (!$item->save()) {
                    foreach ($form->getMessages() as $message)
                    {
                        $this->flash->error($message);
                    }
                }else{
                    $this->flash->success('El articulo ha sido creado');

                }
                //return $this->dispatcher->forward(array("action" => "index",$id));
                return $this->response->redirect('items/index/'.$id);
            }
            else
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message);
                }
            }
        }

        // Obtener el id de la categoria
        $subcategory = Subcategories::findFirst($id);

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('Categorias','categories');
        $this->addBreadcrumb('Sub Categorias','subcategories/index/'.$subcategory->category_id);
        $this->addBreadcrumb('Articulos','items/index/'.$subcategory->id);
        $this->addBreadcrumb('Crear Articulo');

        $this->view->setVars(array(
            'title_view'=>'Crear Sub Categoria',
            'v_session' => $this->session->has("userId"),
            'id'=>$id,
            'form'      => new FormItem(),
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));
    }


    public function editAction($id=null)
    {
        $request = $this->request;
        $user_id = $this->session->get("userId");
        if($request->isPost())
        {
            $form = new FormItem();
            if($form->isValid($request->getPost())== true)
            {
                $datetime = new \DateTime('America/Mexico_City');


                $id = $this->request->getPost("id", "int");
                $item = Items::findFirstById($id);

                $item->name = $request->getPost('name');
                $item->description = $request->getPost('description');
                $item->modified = $datetime->format('Y-m-d H:i:s');
                $item->url_photo = $request->getPost('url_photo');
                $item->price = $request->getPost('price');

                // Ajustes para que phalcon reconozca lo elementos check
                $item->status = 1;
                if ($request->getPost('status')==''){
                    $item->status = 0;
                }
                $item->user_modify = $user_id;
                if (!$item->save()) {
                    foreach ($form->getMessages() as $message)
                    {
                        $this->flash->error($message);
                    }
                }else{
                    $this->flash->success('El articulo ha sido editado');
                }

                // return $this->dispatcher->forward(array("action" => "index",$subcategory->category_id));
                return $this->response->redirect('items/index/'.$item->subcategory_id);
            }
            else
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message);
                }
            }
        }

        $item = Items::findFirst($id);
        $subcategory = Subcategories::findFirst($item->subcategory_id);
        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('Categorias','categories');
        $this->addBreadcrumb('Sub Categorias','subcategories/index/'.$subcategory->category_id);
        $this->addBreadcrumb('Articulos','items/index/'.$item->subcategory_id);
        $this->addBreadcrumb('Editar Articulo','items/edit/'.$id);


        $this->view->setVars(array(
            'title_view'=>'Administracion de Articulos',
            'v_session' => $this->session->has("userId"),
            'categories'=> $subcategory,
            'id'=> $id,
            'form'      => new FormItem($item),
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));
    }

    public function deleteAction($id=null)
    {
        $item = Items::findFirstById($id);
        $id=0;
        if (count($item)>0){
            $id=$item->subcategory_id;
            $item->delete();
            $this->flash->success('Se ha eliminado el Articulo');
        }
        //return $this->dispatcher->forward(array("action" => "index"));
        return $this->response->redirect('items/index/'.$id);
    }

    public function getItemAction()
    {
        $this->view->disable();
        $request = $this->request;
        if($this->request->isAjax() == true)
        {
            $array = array();
            $item = Items::findFirstById($request->get('id'));
            $array['name']=$item->name;
            $array['description']=$item->description;
            $array['price']=$item->price;
            $array['url_photo']=$item->url_photo;

            $this->response->setJsonContent($array);
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }else
        {
            $this->response->setStatusCode(404, "Not Found1");
        }
    }

}

