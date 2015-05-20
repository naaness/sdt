<?php

class RmRegistriesTasks extends \Phalcon\Mvc\Model
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
    public $rm_registry_id;

    /**
     *
     * @var integer
     */
    public $task_id;

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
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'rm_registry_id' => 'rm_registry_id', 
            'task_id' => 'task_id', 
            'created' => 'created', 
            'modified' => 'modified', 
            'day' => 'day'
        );
    }

    public function initialize(){
        $this->belongsTo("task_id", "Tasks", "id");
        $this->belongsTo('rm_registry_id','RmRegistries','id');
    }

}
