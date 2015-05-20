<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 29/03/15
 * Time: 06:00 PM
 */

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\Identical;

class DeleteForm extends Form
{
    public function initialize()
    {
        // Identificador del post
        $id = new Hidden('id');
        $this->add($id);

        // Agregar submit
        $this->add(new Submit('submit', array(
            'class'     =>  'form-control btn btn-danger',
            'value'     =>  'Eliminar'
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
