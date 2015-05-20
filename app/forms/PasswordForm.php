<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 11/02/15
 * Time: 02:00 PM
 */
use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\StringLength,
    Phalcon\Validation\Validator\Confirmation;

class PasswordForm extends Form
{
    public function initialize()
    {
        // Agregar el campo nuevo password
        $newpassword = new  Password('newpassword',
            array(
                'id'    =>  'newpassword',
                'class' =>  'form-control'
            )
        );

        $newpassword->setLabel('Nueva Contraseña');

        $newpassword->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Nueva Contraseña es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '50',
                        'min'               =>  '4',
                        'messageMinimum'    =>  'El campo Nueva Contraseña no puede tener menos de 4 caracteres',
                        'messageMaximum'    =>  'El campo Nueva Contraseña no puede tener mas de 50 caracteres'
                    )
                ),
                new Confirmation(
                    array(
                        'message'           =>  'El campo confirmar Contraseña no coincide',
                        'with'              =>  'confirmPassword'
                    )
                )
            )
        );

        $this->add($newpassword);

        // Agregar el campo confirmPassword
        $confirmpassword = new  Password('confirmPassword',
            array(
                'id'    =>  'confirmPassword',
                'class' =>  'form-control'
            )
        );

        $confirmpassword->setLabel('Confirmar Contraseña');

        $this->add($confirmpassword);

        // Agregar submit
        $this->add(new Submit('submit', array(
            'class'     =>  'btn btn-primary btn-block',
            'value'     =>  'Cambiar Contraseña'
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