<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 30/04/15
 * Time: 12:09 PM
 */

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\StringLength;

use SDT\Elements\NumberElement;

class DailyPlanningForm extends Form
{
    public function initialize() {
        // Identificador de la categoria
        $id = new Hidden('id');
        $this->add($id);

        $dailyPlanning = DailyPlanning::find(array('order'=>'order_r DESC'));
        $last  = $dailyPlanning->getFirst();
        $order=1;
        if($last){
            if(strpos($last->order_r,'.')!==false){
                $order = explode($last->order_r,'.');
                $order = (int) $order[0];
            }else{
                $order = (int) $last->order_r;
            }
            $order = $order+1;
        }
        // campo orden
        $order_r = new NumberElement('order_r',
            array(
                'id'    =>  'order_r',
                'class' =>  'form-control',
                'min'   =>  1,
                'value' =>  $order
            )
        );
        $order_r->setLabel('Numeración');
        $this->add($order_r);

        // campo user name y su validacion
        $message = new Text('message',
            array(
                'id'    =>  'message',
                'class' =>  'form-control'
            )
        );
        $message->setLabel('Mensaje');
        $message->addValidators(
            array(
                new PresenceOf(
                    array(
                        'message'            => 'El campo Mensaje es requerido'
                    )
                ),
                new StringLength(
                    array(
                        'max'               =>  '200',
                        'min'               =>  '2',
                        'messageMinimum'    =>  'El campo Mensaje no puede tener menos de 2 caracteres',
                        'messageMaximum'    =>  'El campo Mensaje no puede tener mas de 200 caracteres'
                    )
                )
            )
        );
        $this->add($message);

        // campo submensaje y su validacion
        $submensaje = new Text('submessage',
            array(
                'id'    =>  'submessage',
                'class' =>  'form-control'
            )
        );
        $submensaje->setLabel('Submensaje');
        $this->add($submensaje);

        // Agregar submit
        $this->add(new Submit('submit', array(
            'class'     =>  'btn btn-default btn-block',
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
