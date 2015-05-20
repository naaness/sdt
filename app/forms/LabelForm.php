<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 25/02/15
 * Time: 03:35 PM
 */

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\StringLength;

class LabelForm extends Form
{
    public function initialize()
    {

        // Identificador de la subcategoria
        $id = new Hidden('id');
        $this->add($id);

        // campo user name y su validacion
        $name = new Text('name',
            array(
                'id'    =>  'name',
                'class' =>  'form-control'
            )
        );
        $name->setLabel('Nombre de la Etiqueta');
        $name->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Nombre es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '60',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Nombre del Articulo no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Nombre del Articulo no puede tener mas de 60 caracteres'
                    )
                )
            )
        );
        $this->add($name);

        // campo color de letra
        $color = new Text('color',
            array(
                'id'        =>  'color',
                'class'     =>  'form-control',
                'value'     =>  '#000000',
                'style'     =>  'display: none;'
            )
        );
        $color->setLabel('Color de la letra');
        $color->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Color es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'min'               =>  '7',
                        'messageMinimum'    =>  'El campo Color del Articulo no puede tener menos de 7 caracteres',
                    )
                )
            )
        );
        $this->add($color);

        // color de fondo
        $b_color = new Text('b_color',
            array(
                'id'    =>  'b_color',
                'class'     =>  'form-control',
                'value'   =>  '#9B9B9A',
                'style'     =>  'display: none;'
            )
        );
        $b_color->setLabel('Color de fondo');
        $b_color->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Color de fondo es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'min'               =>  '7',
                        'messageMinimum'    =>  'El campo Color de fondo no puede tener menos de 7 caracteres',
                    )
                )
            )
        );
        $this->add($b_color);

        // color de fondo cuando es seleccionado
        $b_color_checked = new Text('b_color_checked',
            array(
                'id'    =>  'b_color_checked',
                'class'     =>  'form-control',
                'value'   =>  '#dddddc',
                'style'     =>  'display: none;'
            )
        );
        $b_color_checked->setLabel('Color de fondo claro');
        $b_color_checked->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Color de fondo claro es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'min'               =>  '7',
                        'messageMinimum'    =>  'El campo Color de fondo claro no puede tener menos de 7 caracteres',
                    )
                )
            )
        );
        $this->add($b_color_checked);

        // fuente de letra
        $font = new Select('rm_font_id', RmFonts::find(), array(
            'useEmpty' => false,
            'emptyText' => 'Please Select...',
            'using' => array('id', 'name'),
            'id'    =>  'b_color_checked',
            'class' =>  'form-control'
        ));

        $font->setLabel('Fuente');
        $this->add($font);

        // tamaño de letra
        $size = new Select('rm_size_id', RmSizes::find(), array(
            'useEmpty' => false,
            'emptyText' => 'Please Select...',
            'using' => array('id', 'size'),
            'id'    =>  'b_color_checked',
            'class' =>  'form-control'
        ));

        $size->setLabel('Tamaño');
        $this->add($size);

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