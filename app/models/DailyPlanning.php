<?php

class DailyPlanning extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $message;

    /**
     *
     * @var string
     */
    public $submessage;

    /**
     *
     * @var integer
     */
    public $header;

    /**
     *
     * @var string
     */
    public $order_r;

    /**
     *
     * @var integer
     */
    public $r_parent_id;

    /**
     *
     * @var string
     */
    public $date;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'message' => 'message', 
            'submessage' => 'submessage', 
            'header' => 'header', 
            'order_r' => 'order_r',
            'r_parent_id' => 'r_parent_id',
            'date'=>'date'
        );
    }

    public function beforeValidationOnCreate()
    {
        if(!$this->r_parent_id){
            $this->r_parent_id=0;
        }
        if(!$this->r_next_id){
            $this->r_next_id=0;
        }
        if(!$this->date){
            $this->date = date('Y-m-d H:i:s');
        }
    }
    public function initialize() {
        $this->hasMany("id", "DailyUsers", "daily_planing_id");
    }

    public function beforeCreate()
    {
        $filter = new Phalcon\Filter();
        if($this->message){
            $this->message = $filter->sanitize($this->message,"string");
        }
        if($this->submessage){
            $this->submessage = $filter->sanitize($this->submessage,"string");
        }
        if($this->order_r){
            $this->order_r = $filter->sanitize($this->order_r,"string");
        }
        if($this->header){
            $this->header = $filter->sanitize($this->header,"int");
        }
        if($this->r_parent_id){
            $this->r_parent_id = $filter->sanitize($this->r_parent_id,"int");
        }
        if($this->date){
            $this->date = $filter->sanitize($this->date,"string");
        }
    }

    public function beforeUpdate()
    {
        // Set the modification date
        $this->date = date('Y-m-d H:i:s');
        $filter = new Phalcon\Filter();
        if($this->message){
            $this->message = $filter->sanitize($this->message,"string");
        }
        if($this->submessage){
            $this->submessage = $filter->sanitize($this->submessage,"string");
        }
        if($this->order_r){
            $this->order_r = $filter->sanitize($this->order_r,"string");
        }
        if($this->header){
            $this->header = $filter->sanitize($this->header,"int");
        }
        if($this->r_parent_id){
            $this->r_parent_id = $filter->sanitize($this->r_parent_id,"int");
        }
        if($this->date){
            $this->date = $filter->sanitize($this->date,"string");
        }
    }

}
