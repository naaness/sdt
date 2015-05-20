<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 12/02/15
 * Time: 04:57 PM
 */

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\TextArea,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\StringLength;

class ProfileForm extends Form
{
    public function initialize($dates=null)
    {
        // campo user name y su validacion
        $name = new Text('name',
            array(
                'id'    =>  'name',
                'class' =>  'form-control'
            )
        );
        $name->setLabel('Nombres');
        $name->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Nombres es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '50',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Nombres no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Nombres no puede tener mas de 50 caracteres'
                    )
                )
            )
        );
        $this->add($name);

        // campo user last_name y su validacion
        $last_name = new Text('last_name',
            array(
                'id'    =>  'last_name',
                'class' =>  'form-control'
            )
        );
        $last_name->setLabel('Apellidos');
        $last_name->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Apellidos es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '50',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Apellidos no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Apellidos no puede tener mas de 50 caracteres'
                    )
                )
            )
        );
        $this->add($last_name);

        // campo descripcion y su validacion
        $description = new TextArea('description',
            array(
                'id'    =>  'description',
                'class' =>  'form-control'
            )
        );
        $description->setLabel('Acerca de mi');
        $this->add($description);

        // campo biografia
        $bio = new TextArea('about_bio',
            array(
                'id'    =>  'about_bio',
                'class' =>  'form-control'
            )
        );
        $bio->setLabel('Mi trayectoria profesional');
        $this->add($bio);

        // campo biografia
        $job= new TextArea('about_job',
            array(
                'id'    =>  'about_job',
                'class' =>  'form-control'
            )
        );
        $job->setLabel('Acerca de mi trabajo');
        $this->add($job);

        // campo telefono
        $phone = new Text('phone',
            array(
                'id'    =>  'phone',
                'class' =>  'form-control'
            )
        );
        $phone->setLabel('Teléfono');
        $phone->addValidators(
            array(
                new StringLength(
                    array(
                        'max'               =>  '20',
                        'messageMaximum'    =>  'El campo Teléfono no puede tener mas de 20 caracteres'
                    )
                )
            )
        );
        $this->add($phone);

        // campo celular
        $mobile_phone = new Text('mobile_phone',
            array(
                'id'    =>  'mobile_phone',
                'class' =>  'form-control'
            )
        );
        $mobile_phone->setLabel('Celular');
        $mobile_phone->addValidators(
            array(
                new StringLength(
                    array(
                        'max'               =>  '20',
                        'messageMaximum'    =>  'El campo Celular no puede tener mas de 20 caracteres'
                    )
                )
            )
        );
        $this->add($mobile_phone);

        // campo puesto
        $position = new Text('position',
            array(
                'id'    =>  'position',
                'class' =>  'form-control'
            )
        );
        $position->setLabel('Cargo o Puesto');
        $position->addValidators(
            array(
                new StringLength(
                    array(
                        'max'               =>  '100',
                        'messageMaximum'    =>  'El campo Puesto no puede tener mas de 100 caracteres'
                    )
                )
            )
        );
        $this->add($position);

        // campo compañia
        $company = new Text('company',
            array(
                'id'    =>  'company',
                'class' =>  'form-control'
            )
        );
        $company->setLabel('Empresa');
        $company->addValidators(
            array(
                new StringLength(
                    array(
                        'max'               =>  '100',
                        'messageMaximum'    =>  'El campo Empresa no puede tener mas de 100 caracteres'
                    )
                )
            )
        );
        $this->add($company);

        // Url de la imagen del articulo
        $url_photo = new Text('url_photo',
            array(
                'id'    =>  'url_photo',
                'class' =>  'form-control'
            )
        );
        $url_photo->setLabel('Url de la imagen');
        $url_photo->addValidators(
            array(
                new StringLength(
                    array(
                        'max'               =>  '200',
                        'min'               =>  '0',
                        'messageMinimum'    =>  'El campo Apellidos no puede tener menos de 0 caracteres',
                        'messageMaximum'    =>  'El campo Url no puede tener mas de 50 caracteres'
                    )
                )
            )
        );
        $this->add($url_photo);

        // Url de la imagen del articulo
        $nulo = true;

        if ($dates!=null){
            if ($dates->subject_email!=""){
                $nulo=false;
            }
        }

        if ($nulo){
            $subject_email = new Text('subject_email',
                array(
                    'id'        =>  'subject_email',
                    'class'     =>  'form-control',
                    'value'     =>  'SDT USERNAME TIME'
                )
            );
        }else{
            $subject_email = new Text('subject_email',
                array(
                    'id'        =>  'subject_email',
                    'class'     =>  'form-control'
                )
            );
        }
        $subject_email->setLabel('Asunto Correo RM');
        $this->add($subject_email);


        // Color de navegacion
        $navbar_color = new Text('navbar_color',
            array(
                'id'    =>  'navbar_color',
                'class' =>  'form-control',
                'value' =>  '#000000'
            )
        );
        $navbar_color->setLabel('Color de la barra de navegacion');
        $navbar_color->addValidators(
            array(
                new StringLength(
                    array(
                        'max'               =>  '7',
                        'min'               =>  '7',
                        'messageMinimum'    =>  'Es necesario 7',
                        'messageMaximum'    =>  'Es necesario 7'
                    )
                )
            )
        );
        $this->add($navbar_color);

        // Color de navegacion
        $body_color = new Text('body_color',
            array(
                'id'    =>  'body_color',
                'class' =>  'form-control',
                'value' =>  '#FFFFFF'
            )
        );
        $body_color->setLabel('Color del cuerpo');
        $body_color->addValidators(
            array(
                new StringLength(
                    array(
                        'max'               =>  '7',
                        'min'               =>  '7',
                        'messageMinimum'    =>  'Es necesario 7',
                        'messageMaximum'    =>  'Es necesario 7'
                    )
                )
            )
        );
        $this->add($body_color);

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

        $this->add(new Hidden('typeForm', array('value'=>'profile')));
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
