<?php

class Comments extends \Phalcon\Mvc\Model
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
    public $task_id;

    /**
     *
     * @var string
     */
    public $date;

    /**
     *
     * @var string
     */
    public $comment;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'task_id' => 'task_id',
            'date' => 'date', 
            'comment' => 'comment'
        );
    }

    public function initialize()
    {
        $this->belongsTo("user_id", "Users", "id");
        $this->belongsTo("task_id", "Tasks", "id");
    }
    public function beforeValidationOnCreate() {
        $filter = new Phalcon\Filter();
        if($this->user_id){
            $this->user_id = $filter->sanitize($this->user_id,"int");
        }
        if($this->task_id){
            $this->task_id = $filter->sanitize($this->task_id,"int");
        }
        if($this->comment){
            $this->comment = $filter->sanitize($this->comment,"string");
        }
    }
    public function beforeUpdate(){
        $filter = new Phalcon\Filter();
        if($this->user_id){
            $this->user_id = $filter->sanitize($this->user_id,"int");
        }
        if($this->task_id){
            $this->task_id = $filter->sanitize($this->task_id,"int");
        }
        if($this->comment){
            $this->comment = $filter->sanitize($this->comment,"string");
        }
    }
}
