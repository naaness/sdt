<?php

class DailyUsers extends \Phalcon\Mvc\Model
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
     * @var integer
     */
    public $daily_planing_id;

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
    public $checked;

    /**
     *
     * @var string
     */
    public $order_r;

    /**
     *
     * @var string
     */
    public $head;

    /**
     *
     * @var integer
     */
    public $parent_id;

    /**
     *
     * @var string
     */
    public $date_copy;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'daily_planing_id' => 'daily_planing_id', 
            'message' => 'message', 
            'submessage' => 'submessage',
            'checked' => 'checked',
            'order_r' => 'order_r',
            'head'  => 'head',
            'parent_id' => 'parent_id', 
            'date_copy' => 'date_copy'
        );
    }
    public function beforeValidationOnCreate()
    {
        if(!$this->checked){
            $this->checked=0;
        }
        $filter = new Phalcon\Filter();
        if($this->user_id){
            $this->user_id = $filter->sanitize($this->user_id,"int");
        }
        if($this->daily_planing_id){
            $this->daily_planing_id = $filter->sanitize($this->daily_planing_id,"int");
        }
        if($this->message){
            $this->message = $filter->sanitize($this->message,"string");
        }
        if($this->submessage){
            $this->submessage = $filter->sanitize($this->submessage,"string");
        }
        if($this->checked){
            $this->checked = $filter->sanitize($this->checked,"int");
        }
        if($this->order_r){
            $this->order_r = $filter->sanitize($this->order_r,"string");
        }
        if($this->head){
            $this->head = $filter->sanitize($this->head,"int");
        }
        if($this->parent_id){
            $this->parent_id = $filter->sanitize($this->parent_id,"int");
        }
        if($this->date_copy){
            $this->date_copy = $filter->sanitize($this->date_copy,"string");
        }
    }

    public function initialize() {
        $this->belongsTo("daily_planing_id", "DailyPlanning", "id");
    }
}
