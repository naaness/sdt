<?php

class Delegates extends \Phalcon\Mvc\Model
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
    public $first_user;

    /**
     *
     * @var integer
     */
    public $second_user;

    /**
     *
     * @var integer
     */
    public $task_id;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'first_user' => 'first_user', 
            'second_user' => 'second_user', 
            'task_id' => 'task_id'
        );
    }

    public function initialize(){
        $this->belongsTo("task_id", "Tasks", "id");
    }

}
