<?php

class TasksMessages extends \Phalcon\Mvc\Model
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
    public $task_id;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $message;

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
            'task_id'=>'task_id',
            'user_id' => 'user_id', 
            'message' => 'message', 
            'date' => 'date'
        );
    }

    public function initialize(){
        $this->belongsTo("task_id", "Tasks", "id");
        $this->belongsTo("user_id", "Users", "id");
    }

    public function beforeValidationOnCreate()
    {
        $hoy = new \DateTime('America/Mexico_City');
        $this->date = $hoy->format('Y-m-d H:m:s');
    }

}
