<?php

class RmRegistries extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $created;

    /**
     *
     * @var string
     */
    public $modified;

    /**
     *
     * @var string
     */
    public $day;

    /**
     *
     * @var integer
     */
    public $order_r;

    /**
     *
     * @var string
     */
    public $numbering;

    /**
     *
     * @var string
     */
    public $registry;

    /**
     *
     * @var integer
     */
    public $checked;

    /**
     *
     * @var integer
     */
    public $rm_label_id;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var integer
     */
    public $acordion;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id'        => 'id',
            'user_id'   => 'user_id',
            'created'   => 'created',
            'modified'  =>  'modified',
            'day'       => "day",
            'order_r'   => 'order_r',
            'numbering' => 'numbering', 
            'registry'  => 'registry',
            'checked'   => 'checked',
            'rm_label_id' => 'rm_label_id', 
            'status' => 'status',
            'acordion'  =>  'acordion'
        );
    }

    /*
    * @desc - antes de pasar la validación
    */
    public function beforeValidationOnCreate() {
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d H:m:s');
        if(!$this->created){
            $this->created = $today;
        }
        if(!$this->modified){
            $this->modified = $today;
        }
    }

    public function beforeCreate() {
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d H:m:s');
        if(!$this->modified){
            $this->modified = $today;
        }
        if(!$this->created){
            $this->created = $today;
        }
    }

    public function beforeUpdate() {
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d H:m:s');
        $this->modified = $today;
    }

    public function initialize(){
        $this->belongsTo("user_id", "Users", "id");
        $this->belongsTo('rm_label_id','RmLabels','id');
        $this->hasMany("id", "RmRegistriesTasks", "rm_registry_id");
    }
}
