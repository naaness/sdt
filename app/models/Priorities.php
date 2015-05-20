<?php

class Priorities extends \Phalcon\Mvc\Model
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
    public $name;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'name' => 'name'
        );
    }

    public function initialize()
    {
        $this->hasMany("id", "Tasks", "priority_id");
        $this->hasMany("id", "UnidTimes", "priority_id");
    }

}
