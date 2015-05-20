<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 4/03/15
 * Time: 10:38 PM
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

class PackageForm extends Form
{
    public function initialize($dates, $team_id)
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
        $name->setLabel('Nombre del Checklist');
        $name->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Nombre del Checklist es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '60',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Nombre del Checklist no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Nombre del Checklist no puede tener mas de 60 caracteres'
                    )
                )
            )
        );
        $this->add($name);

        // campo user name y su validacion
        $code = new Text('code',
            array(
                'id'    =>  'code',
                'class' =>  'form-control'
            )
        );
        $code->setLabel('Codigo');
        $code->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Codigo es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '4',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Codigo no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Codigo no puede tener mas de 4 caracteres'
                    )
                )
            )
        );
        $this->add($code);

        // campo descripcion y su validacion
        $description = new TextArea('description',
            array(
                'id'    =>  'description',
                'class' =>  'form-control'
            )
        );
        $description->setLabel('Descripcion');
        $this->add($description);

        // Buscar los usuario que pertenecen al equipo de trabajo
        $phql = 'SELECT
			Users.*, UsersTeams.*
			FROM UsersTeams
			INNER JOIN Users ON Users.id = UsersTeams.user_id
			WHERE
			UsersTeams.team_id = '.$team_id.'
			ORDER BY Users.username ASC ';
        $results = $this->modelsManager->executeQuery($phql);
        // Transformar a un arreglo que se pase como opciones del select multiple
        $options = array();
        foreach ($results as $option){
            $options[$option->users->id]=$option->users->username;
        }

        // Usuarios del equipo
        $user = new Select('user_id', $options, array(
            'useEmpty'  => false,
            'emptyText' => 'Please Select...',
            'using'     => array('id', 'username'),
            'class'     =>  'form-control',
        ));
        $user->setLabel('Lider del Proyecto');
        $this->add($user);

        // Agregar submit
        $this->add(new Submit('submit', array(
            'class'     =>  'btn btn-primary btn-block',
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