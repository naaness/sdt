<?php

class UsersTasks extends \Phalcon\Mvc\Model
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
     * @var integer
     */
    public $status;

    /**
     *
     * @var integer
     */
    public $new_message;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'task_id' => 'task_id', 
            'status' => 'status',
            'new_message' => 'new_message'
        );
    }

    public function initialize(){
        $this->belongsTo('task_id','Tasks','id',array(
                "foreignKey" => array(
                    "message" => "The task_id does not exist on the Tasks model"
                ))
        );
        $this->belongsTo('user_id','Users','id',array(
                "foreignKey" => array(
                    "message" => "The user_id does not exist on the Users model"
                ))
        );
    }

    public function beforeValidationOnCreate() {
        if(!$this->new_message){
            $this->new_message=0;
        }
    }
}
