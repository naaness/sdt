<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 12/02/15
 * Time: 11:36 AM
 */

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Check,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Submit,
    Phalcon\Forms\Element\Select,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\StringLength;

class EditUserForm extends Form
{
    public function initialize()
    {
        // campo user name y su validacion
        $username = new Text('username',
            array(
                'id'    =>  'username',
                'class' =>  'form-control'
            )
        );

        $username->setLabel('Nombre de Usuario');

        $username->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Nombre de Usuario es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '50',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Nombre de Usuario no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Nombre de Usuario no puede tener mas de 50 caracteres'
                    )
                )
            )
        );

        $this->add($username);

        // Campo Email y validaciones (Se llama aqui la libreria porque ya existe una libreria llamada Email, que es un validaor)
        $email = new  Phalcon\Forms\Element\Email('email',
            array(
                'id'    =>  'email',
                'class' =>  'form-control'
            )
        );

        $email->setLabel('Correo');

        $email->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Correo es requerido'
                    )
                ),
                new Email(
                    array(
                        'message'            => 'El campo Correo no tiene un formato valido'
                    )
                )
            )
        );

        $this->add($email);

        // Agregar el campo role
        $role = new  Select('role',
            array(
                'registered'    =>  'Registrado',
                'admin' =>  'Administrador'
            ),
            array(
                'id'    =>  'role',
                'class' =>  'form-control'
            )
        );

        $role->setLabel('Role');

        $this->add($role);

        // campo status y su validacion
        $status = new Check ('status',
            array(
                'id'    =>  'status',
                'value' =>  1
            )
        );
        $status->setLabel('Activo');
        $this->add($status);

        // Agregar submit
        $this->add(new Submit('submit', array(
            'class'     =>  'btn btn-primary btn-sm btn-block',
            'value'     =>  'Enviar'
        )));


        // Para evitar ataque csrf
        $csrf = new Hidden('csrf');

        $csrf->addValidator( new Identical(array(
                    'value'     =>  $this->security->getSessionToken(),
                    'message'   => 'Problemas con el formulario'
                )
            )
        );

        $this->add($csrf);

        $this->add(new Hidden('typeForm', array('value'=>'user')));
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
