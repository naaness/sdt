<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 2/02/15
 * Time: 01:56 PM
 */
use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Check,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Submit,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Identical;

class LoginForm extends Form
{

    public function initialize()
    {
        //añadimos el campo email
        $username_email = new Text('username_email',
            array(
                'id'            =>  'username_email',
                'class'         =>  'form-control',
                'placeholder'   =>  'Nombre de Usuario o Correo'
            )
        );

        //añadimos la validación para un campo de tipo email y como campo requerido
        $username_email->addValidators(
            array(
                new PresenceOf(array(
                        'message' => 'El nombre de de Usuario o Correo es requerido'
                    )
                ),
            )
        );

        //label para el email
        $username_email->setLabel('Nombre de Usuario o Correo');

        //hacemos que se pueda llamar a nuestro campo email
        $this->add($username_email);

        //añadimos el campo password
        $password = new Password('password',
            array(
                'id'    =>  'password',
                'class' =>  'form-control',
                'placeholder' => 'Password'
            )
        );

        //añadimos la validación como campo requerido al password
        $password->addValidator(
            new PresenceOf(array(
                'message' => 'El password es requerido'
            ))
        );

        //label para el Password
        $password->setLabel('Password');

        //hacemos que se pueda llamar a nuestro campo password
        $this->add($password);

        // Remember
        $remember = new Check('remember', array(
            'value' => 'yes'
        ));
        $remember->setLabel('Recuerdame');
        $this->add($remember);

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
            'value'     =>  'Logear'
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