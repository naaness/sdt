<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 2/03/15
 * Time: 11:02 AM
 */
use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\TextArea,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\StringLength;

class TeamForm extends Form
{
    public function initialize($dates, $options)
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
        $name->setLabel('Nombre del Equipo');
        $name->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Nombre del Equipo es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '60',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Nombre del Equipo no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Nombre del Equipo no puede tener mas de 60 caracteres'
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



        // Agregar submit
        $this->add(new Submit('submit', array(
            'class'     =>  'btn btn-primary btn-sm btn-block',
            'value'     =>  'Enviar'
        )));

        // usuarios del sistema

        if (isset($options)){
        }else{
            $phql = 'SELECT
			Users.id, IFNULL( CONCAT(Profiles.name," ", Profiles.last_name), Users.username) as usernameX
			FROM Users
			LEFT JOIN Profiles  ON Profiles.user_id = Users.id
			AND Users.status = 1
            ORDER BY usernameX ASC';
            $users = $this->modelsManager->executeQuery($phql);
            $options = array();
            foreach ($users as $user){
                $options[$user->id]=$user->usernameX;
            }
            $users = new Select('users_ids[]', $options, array(
                'useEmpty'  => false,
                'emptyText' => 'Please Select...',
                'using'     => array('id', 'usernameX'),
                'class'     =>  'form-control',
                'size'      => 20,
                'multiple'  => true,
                'id'        => 'users_ids'
            ));
        }

        $users->setLabel('Usuarios');
        $this->add($users);

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
