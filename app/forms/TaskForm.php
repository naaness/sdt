<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 3/03/15
 * Time: 04:33 PM
 */

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\TextArea,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Submit,
    Phalcon\Forms\Element\Select,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\StringLength;

class TaskForm extends Form
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
        $name->setLabel('Nombre de la Tarea');
        $name->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Nombre de la Tarea es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '60',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Nombre de la Tarea no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Nombre de la Tarea no puede tener mas de 60 caracteres'
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

        if ($options['team_id']==0 && $options['project_id']==0 && $options['package_id']==0) { // Mostras solo el usuario,
            // porque sera una tarea individual, llamar por ahora a todos los usuarios del sistema
            $users = Users::find();
        }elseif ($options['team_id'] && $options['project_id']) { // Mostrar todos los usuario del proyecto
            // Buscar los usuario que pertenecen al equipo de trabajo
            $phql = 'SELECT
			Users.*, UsersProjects.*
			FROM UsersProjects
			INNER JOIN Users ON Users.id = UsersProjects.user_id
			WHERE
			UsersProjects.project_id = '.$options['project_id'].'
			ORDER BY Users.username ASC ';
            $results = $this->modelsManager->executeQuery($phql);
            // Transformar a un arreglo que se pase como opciones del select multiple
            $users = array();
            foreach ($results as $option){
                $users[$option->users->id]=$option->users->username;
            }
        }elseif ($options['team_id'] && $options['package_id']) { // El usuairo debe ser 0
            $users = array('0'=>'Sin responsable');
        }elseif ($options['team_id'] && $options['package_id']) { // El responsable sera del equipo, y es el creador de dicha tarea
            $package = Packages::findFirst($options['package_id']);
            $users = array($package->id=>$package->users->name);
        }

        // Crear el arreglo multiple
        $users = new Select('user_id', $users, array(
            'useEmpty'  => false,
            'emptyText' => 'Please Select...',
            'using'     => array('id', 'username'),
            'class'     =>  'form-control'
        ));
        $users->setLabel('Usuario responsable');
        $this->add($users);

        // Crear prioridad
        $priority = new Select('priority_id', Priorities::find(), array(
            'useEmpty'  => false,
            'emptyText' => 'Please Select...',
            'using'     => array('id', 'name'),
            'class'     =>  'form-control'
        ));
        $priority->setLabel('Prioridad');
        $this->add($priority);

        // Crear status
        $status = new Select('status', array(1=>'Aceptado',2=>'En espera',3=>'Rechazado'), array(
            'useEmpty'  => false,
            'emptyText' => 'Please Select...',
            'using'     => array('id', 'name'),
            'class'     =>  'form-control'
        ));
        $status->setLabel('Estado');
        $this->add($status);

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
//            foreach($this->hasMessagesFor($name) as $message)
//            {
//                $this->flash->error($message);
//            }
//            $html = '<ul>';
//            foreach ($this->getMessages($name) as $message)
//            {
//                $html.= '<li>'.$message.'</li>';
//
//            }
//            $html.='</ul>';
//            $this->flash->error($html);
        }
    }
}