<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 17/03/15
 * Time: 10:27 AM
 */

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\TextArea,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\StringLength;

class PostForm extends Form
{
    public function initialize()
    {
        // Identificador del post
        $id = new Hidden('id');
        $this->add($id);

        // campo user name y su validacion
        $name = new Text('name',
            array(
                'id'    =>  'name',
                'class' =>  'form-control'
            )
        );
        $name->setLabel('Titulo');
        $name->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Titulo es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '100',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Titulo no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Titulo no puede tener mas de 50 caracteres'
                    )
                )
            )
        );
        $this->add($name);

        // campo descripcion y su validacion
        $description = new TextArea('post',
            array(
                'id'    =>  'post',
                'class' =>  'ckeditor'
            )
        );
        $description->setLabel('Mensaje');
        $this->add($description);

        // Agregar submitbtn
        $this->add(new Submit('submit', array(
            'class'     =>  'form-control btn-sm btn-default',
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
