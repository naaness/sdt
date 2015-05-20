<?php
 use Phalcon\Forms\Form,
     Phalcon\Forms\Element\Text,
     Phalcon\Forms\Element\Hidden,
     Phalcon\Forms\Element\Password,
     Phalcon\Forms\Element\Submit,
     Phalcon\Forms\Element\Select,
     Phalcon\Validation\Validator\PresenceOf,
     Phalcon\Validation\Validator\Email,
     Phalcon\Validation\Validator\Identical,
     Phalcon\Validation\Validator\StringLength,
     Phalcon\Validation\Validator\Confirmation;

    class RegisterForm extends Form
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

            // Agregar el campo password
            $password = new  Password('password',
                array(
                    'id'    =>  'password',
                    'class' =>  'form-control'
                )
            );

            $password->setLabel('Contraseña');

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

            $this->add($password);

            // Agregar el campo confirmPassword
            $confirmpassword = new  Password('confirmPassword',
                array(
                    'id'    =>  'confirmPassword',
                    'class' =>  'form-control'
                )
            );

            $confirmpassword->setLabel('Confirmar Contraseña');



            $this->add($confirmpassword);

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

            $role->addValidators(
                array(
                    new PresenceOf(
                        array(
                            'message'            => 'El campo Contraseña es requerido'
                        )
                    ),
                )
            );

            $this->add($role);

            // Agregar submit
            $this->add(new Submit('submit', array(
                'class'     =>  'btn btn-primary btn-sm btn-block',
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
