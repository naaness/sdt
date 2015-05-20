<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 2/02/15
 * Time: 01:56 PM
 */
use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Submit,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\StringLength,
    Phalcon\Validation\Validator\Confirmation,
    Phalcon\Validation\Validator\Identical;

class PasswordAdminForm extends Form
{

    public function initialize()
    {
        //añadimos el campo password
        $password = new Password('password',
            array(
                'id'    =>  'password',
                'class' =>  'form-control',
                'placeholder' => 'Password'
            )
        );

        //añadimos la validación como campo requerido al password
        $password->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Contraseña es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '50',
                        'min'               =>  '4',
                        'messageMinimum'    =>  'El campo Contraseña no puede tener menos de 4 caracteres',
                        'messageMaximum'    =>  'El campo Contraseña no puede tener mas de 50 caracteres'
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

        //label para el Password
        $password->setLabel('Contraseña');

        //hacemos que se pueda llamar a nuestro campo password
        $this->add($password);

        //añadimos el campo password
        $sonfirmpassword = new Password('confirmPassword',
            array(
                'id'    =>  'confirmPassword',
                'class' =>  'form-control',
                'placeholder' => 'Password'
            )
        );

        //añadimos la validación como campo requerido al password
        $sonfirmpassword->addValidator(
            new PresenceOf(array(
                'message' => 'Debes confirmar la contraseña es requerido'
            ))
        );

        //label para el Password
        $sonfirmpassword->setLabel('Confirmar Contraseña');

        //hacemos que se pueda llamar a nuestro campo password
        $this->add($sonfirmpassword);




        //prevención de ataques csrf, genera un campo de este tipo
        //<input value="dcf7192995748a80780b9cc99a530b58" name="csrf" id="csrf" type="hidden" />
        // Para evitar atacque csrf
        $csrf = new Hidden('csrf');

        $csrf->addValidator( new Identical(array(
                    'value'     =>  $this->security->getSessionToken(),
                    'message'   => 'Problemas con el formulario'
                )
            )
        );

        $this->add($csrf);

        //añadimos un botón de tipo submit
        $this->add(new Submit('submit', array(
            'class'     =>  'btn btn-primary btn-sm btn-block',
            'value'     =>  'Enviar'
        )));
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