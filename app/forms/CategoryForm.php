<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 4/02/15
 * Time: 10:27 PM
 */
 use Phalcon\Forms\Form,
     Phalcon\Forms\Element\Text,
     Phalcon\Forms\Element\TextArea,
     Phalcon\Forms\Element\Check,
     Phalcon\Forms\Element\Hidden,
     Phalcon\Forms\Element\Submit,
     Phalcon\Validation\Validator\PresenceOf,
     Phalcon\Validation\Validator\Identical,
     Phalcon\Validation\Validator\in ,
     Phalcon\Validation\Validator\StringLength;

    class CategoryForm extends Form
    {
        public function initialize()
        {
            // Identificador de la categoria
            $id = new Hidden('id');
            $this->add($id);


            // campo user name y su validacion
            $name = new Text('name',
                array(
                    'id'    =>  'name',
                    'class' =>  'form-control'
                )
            );
            $name->setLabel('Nombre de la Categoria');
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
                            'messageMinimum'    =>  'El campo Nombre de Categoria no puede tener menos de 2 caracteres',
                            'messageMaximum'    =>  'El campo Nombre de Categoria no puede tener mas de 50 caracteres'
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
