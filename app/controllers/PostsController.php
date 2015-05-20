<?php
use \Phalcon\Paginator\Adapter\Model as Paginacion;
use PostForm as FormPost;

class PostsController extends ControllerBase
{
    public function indexAction()
    {
        // $categories = Categories::find();
        //Crea un paginador, muestra 3 filas por página
        $paginator = new Paginacion(
            array(
                //obtenemos los productos
                "data" => Posts::find(array(
                            "parent_post = 0",
                            "status      = 1",
                            "order"         => "created"
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
        $this->addBreadcrumb('Foro','post');

        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Post',
            'v_session' => $has_user,
            'breadcrumb'=>$this->createHtmlBreadcrumb(),
            'name'      =>  'Foros',
            'user_id'   =>  $this->session->get("userId")
        ));
    }

    public function addAction()
    {
        $request = $this->request;
        $user_id = $this->session->get("userId");
        if($request->isPost())
        {
            $form = new FormPost();
            if($form->isValid($request->getPost())!= false)
            {
                $datetime = new \DateTime('America/Mexico_City');

                $post = new Posts();
                $post->user_id = $user_id;
                $post->name = $request->getPost('name');
                $post->created = $datetime->format('Y-m-d H:i:s');
                $post->modified = $datetime->format('Y-m-d H:i:s');
                $post->status = 1;
                $post->post = $request->getPost('post');
                $post->parent_post = 0;
                $post->parent_post_near = 0;
                $post->order_r = "";
                $post->count_likes = 0;
                $post->count_dislikes = 0;
                $post->count_views = 0;
                $post->count_answers = 0;
                if (!$post->save()) {
                    $this->createListError($post);
                }else{
                    $this->flash->success('La Post ha sido creado');

                }
                return $this->dispatcher->forward(array("action" => "index"));
            }
            else
            {
                $this->createListError($form);
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('Foro','posts');
        $this->addBreadcrumb('Crear Foro','Posts/add');
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Crear Post',
            'v_session' => $has_user,
            'form'      => new FormPost(),
            'breadcrumb'=>$this->createHtmlBreadcrumb()
        ));

        $this->assets
            ->addJs('js/ckeditor/ckeditor.js');
    }

    public function editAction($post_id=0)
    {
        $request = $this->request;
        $user_id = $this->session->get("userId");
        if($request->isPost())
        {
            $form = new FormPost();
            if($form->isValid($request->getPost())!= false)
            {
                $datetime = new \DateTime('America/Mexico_City');

                $post = Posts::findFirst($request->getPost('id'));
                $post->name = $request->getPost('name');
                $post->modified = $datetime->format('Y-m-d H:i:s');
                $post->status = 1;
                $post->post = $request->getPost('post');
                if (!$post->save()) {
                    $this->createListError($post);
                }else{
                    $this->flash->success('La Post ha sido atualizado');

                }
                return $this->dispatcher->forward(array("action" => "index"));
            }
            else
            {
                $this->createListError($form);
            }
        }

        // User el Breadcrumbs de bootstrap
        $this->addBreadcrumb('Foro','posts');
        $this->addBreadcrumb('Editar Foro','posts/edit/'.$post_id);

        $post = Posts::findFirst($post_id);
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'=>'Crear Post',
            'v_session' => $has_user,
            'form'      => new FormPost($post),
            'breadcrumb'=>$this->createHtmlBreadcrumb(),
            'post_id'   =>  $post_id
        ));

        $this->assets
            ->addJs('js/ckeditor/ckeditor.js');
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function viewAction($post_id=0) {

        $post   = Posts::findFirst($post_id);
        $posts  = Posts::find(
            array(
                'parent_post = '.$post_id,
                "order" => " order_r ASC"
            )
        );


        // Cuenta como una visita al foro
        // Incremento en 1 y actualizo
        $post->count_views = $post->count_views+1;
        $post->save();

        // crear cinta de navegacion
        $this->addBreadcrumb('Foro','posts');
        $this->addBreadcrumb('Ver Foro','Posts/view');
        $has_user = $this->session->has("userId");
        if ($has_user){
            $this->createAlerts();
            $this->createNotifications();
            $this->createCustomization();
        }
        $this->view->setVars(array(
            'title_view'    =>  'Ver Post',
            'v_session'     =>  $has_user,
            'post'          =>  $post,
            'posts'         =>  $posts,
            'breadcrumb'    =>  $this->createHtmlBreadcrumb(),
            'post_id'       =>  $post_id,
            'months'        =>  $this->sdt->nameMonths()
        ));

        $this->assets
            ->addJs('js/ckeditor/ckeditor.js')
            ->addJs('js/post.js');

    }


    /**
     * newPost method
     *
     * @throws NotFoundException
     * @param string $id, $post
     * @return json
     */
    public function newPostAction(){
        $this->view->disable();
        $request = $this->request;
        if($this->request->isAjax() == true){
            $user_id = $this->session->get("userId");
            $id = $request->get('id');
            $post = $request->get('post');
            $post_id = $request->get('id_post');
            // Actualizo la cantidad de respuestas
            // Primero obtengo el valor actual de respuestas
            $_post0 = Posts::findFirst($post_id);
            // Incremento en 1 y actualizo
            $_post0->count_answers =  $_post0->count_answers +1;
            $_post0->count_views -=  1;
            $_post0->save();

            // Generar el orden
            // Cuento cuantos respuestas tiene el padre
            $contP = Posts::count('parent_post_near = '.$id);
            $_post = Posts::findFirst($id);
            if ($_post->order_r=="") {
                $orden=$contP;
            }else{
                $orden=$_post->order_r.'.'.$contP;
            }
            // crear el post
            $_datos = array(
                'user_id'=>$user_id,
                'name'=>'-',
                'post'=>$post,
                'parent_post'=>$post_id,
                'parent_post_near'=>$id,
                'count_likes'=>0,
                'count_dislikes'=>0,
                'count_views'=>0,
                'count_answers'=>0,
                'order'=>$orden
            );

            $datetime = new \DateTime('America/Mexico_City');

            // Generar notificacion a todos los miembros que han participado en el post
            $posts0 = Posts::find('parent_post_near = '.$id);
            $user_arr = array();
            foreach( $posts0 as $post0){
                if (!isset($user_arr[$post0->user_id])){
                    if($post0->user_id!=$user_id){
                        // Para cada usuario se debe crear una notificacion
                        $this->sdt->createNotification($post0->user_id,$user_id,"newPost",$post0->id,$datetime->format("Y-m-d H:m:s"));
                    }
                    $user_arr[$post0->user_id] = $post0->user_id;
                }
            }
            if($_post->user_id!=$user_id){
                if (!isset($user_arr[$_post->user_id])){
                    // Para cada usuario se debe crear una notificacion
                    $this->sdt->createNotification($_post->user_id,$user_id,"newPost",$_post0->id,$datetime->format("Y-m-d H:m:s"));
                }
            }

            $newpost = new Posts();
            $newpost->user_id = $user_id;
            $newpost->name = "-";
            $newpost->created = $datetime->format('Y-m-d H:i:s');
            $newpost->modified = $datetime->format('Y-m-d H:i:s');
            $newpost->status = 1;
            $newpost->post = $post;
            $newpost->parent_post = $post_id;
            $newpost->parent_post_near = $id;
            $newpost->order_r = $orden;
            $newpost->count_likes = 0;
            $newpost->count_dislikes = 0;
            $newpost->count_views = 0;
            $newpost->count_answers = 0;
            if ($newpost->save()==false) {
                $this->createListError($newpost);
            }
            $this->response->setJsonContent($_datos);
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();

        }else{
            $this->response->setJsonContent("None");
            $this->response->setStatusCode(200,'Ok');
            $this->response->send();
        }
    }

    public function likeAction($post_id=0){
        $post = Posts::findFirst($post_id);
        if($post){
            $user_id = $this->session->get("userId");
            $postlike = Postlikes::findFirst('user_id = '.$user_id.' AND post_id = '.$post_id);
            if(!$postlike){
                $postdislike = Postdislikes::findFirst('user_id = '.$user_id.' AND post_id = '.$post_id);
                if(!$postdislike){
                    $postlike = new Postlikes();
                    $postlike->user_id = $user_id;
                    $postlike->post_id = $post_id;
                    if($postlike->save()==true){
                        $post->count_likes+=1;
                        $post->save();

                        $datetime = new \DateTime('America/Mexico_City');
                        $this->sdt->createNotification($post->user_id,$user_id,"likePost",$post->id,$datetime->format("Y-m-d H:m:s"));
                    }
                }
            }
            if($post->parent_post>0){
                $post = Posts::findFirst($post->parent_post);
                $post->count_views -= 1;
                $post->save();
                return $this->response->redirect('posts/view/'.$post->id);
            }
            $post->count_views-=1;
            $post->save();
            return $this->response->redirect('posts/view/'.$post_id);
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    public function dislikeAction($post_id=0){
        $post = Posts::findFirst($post_id);
        if($post){
            $user_id = $this->session->get("userId");
            $postdislike = Postdislikes::findFirst('user_id = '.$user_id.' AND post_id = '.$post_id);
            if(!$postdislike){
                $postlike = Postlikes::findFirst('user_id = '.$user_id.' AND post_id = '.$post_id);
                if(!$postlike){
                    $postdislike = new Postlikes();
                    $postdislike->user_id = $user_id;
                    $postdislike->post_id = $post_id;
                    if($postdislike->save()==true){
                        $post->count_dislikes+=1;
                        $post->save();

                        $datetime = new \DateTime('America/Mexico_City');
                        $this->sdt->createNotification($post->user_id,$user_id,"dislikePost",$post->id,$datetime->format("Y-m-d H:m:s"));
                    }
                }
            }
            if($post->parent_post>0){
                $post = Posts::findFirst($post->parent_post);
                $post->count_views -= 1;
                $post->save();
                return $this->response->redirect('posts/view/'.$post->id);
            }
            $post->count_views-=1;
            $post->save();
            return $this->response->redirect('posts/view/'.$post_id);
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }

    private $_post = array();
    public function getAction()
    {
        $this->view->disable();
        if($this->request->isGet()== true){
            $posts = Posts::find();
            foreach( $posts as $post){
                $this->_post[] = $post;
            }
            $this->response->setJsonContent(array('posts'=>$this->_post));
            $this->response->setStatusCode(200,'OK');
            $this->response->send();
        }else{
            $this->response->setStatusCode(404,'Not Found');
        }
    }
}

