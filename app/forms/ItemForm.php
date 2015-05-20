<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 7/02/15
 * Time: 11:59 PM
 */
use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\TextArea,
    Phalcon\Forms\Element\Check,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\Regex,
    Phalcon\Validation\Validator\StringLength;

class ItemForm extends Form
{
    public function initialize()
    {
        $user = new Users();

        // Identificador de la categoria
        $category_id = new Hidden('subcategory_id');
        $this->add($category_id);

        // Identificador de la subcategoria
        $id = new Hidden('id');
        $this->add($id);

        // campo user name y su validacion
        $name = new Text('name',
            array(
                'id'    =>  'name',
                'class' =>  'form-control'
            )
        );
        $name->setLabel('Nombre del Articulo');
        $name->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Nombre es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '50',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Nombre del Articulo no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Nombre del Articulo no puede tener mas de 50 caracteres'
                    )
                )
            )
        );
        $this->add($name);

        // campo descripcion y su validacion
        $description = new TextArea('description',
            array(
                'id'    =>  'description',
                'class' =>  'form-control'
            )
        );
        $description->setLabel('Descripcion');
        $this->add($description);

        // campo status y su validacion
        $status = new Check ('status',
            array(
                'id'    =>  'status'
            )
        );
        $status->setLabel('Activo');
        $this->add($status);

        // Url de la imagen del articulo
        $url = new Text('url_photo',
            array(
                'id'    =>  'url_photo',
                'class' =>  'form-control'
            )
        );
        $url->setLabel('Url de la imagen del articulo');
        $url->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Url es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '400',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Url no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Url no puede tener mas de 400 caracteres'
                    )
                )
            )
        );
        $this->add($url);

        // Precio del articulo
        $price = new Text('price',
            array(
                'id'    =>  'price',
                'class' =>  'form-control'
            )
        );
        $price->setLabel('Precio');
        $price->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Precio es requerido'
                    )
                ),
                new Regex(
                    array(
                        'pattern' => '/^-?[0-9]+([,\.][0-9]*)?$/',
                        'message' => 'Ingrese un valor decimal'
                    )
                )
            )
        );
        $this->add($price);

        // Agregar submit
        $this->add(new Submit('submit', array(
            'class'     =>  'btn btn-primary btn-lg btn-block',
            'value'     =>  'Enviar'
        )));


        // Para evitar atacque csrf
        $csrf = new Hidden('csrf');

        $csrf->addValidator( new Identical(array(
                    'value'     =>  $this->security->getSessionToken(),
                    'message'   => 'Problemas con el formulario'
                )
            )
        );

        $this->add($csrf);
    }

    public function message ($name)
    {
        if($this->hasMessagesFor($name) )
        {
            foreach($this->hasMessagesFor($name) as $message)
            {
                $this->flash->error($message);
            }
        }
    }
}