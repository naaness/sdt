<?php

class UsersChecklists extends \Phalcon\Mvc\Model
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
    public $date;

    /**
     *
     * @var string
     */
    public $range;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'date' => 'date', 
            'range' => 'range'
        );
    }
    public function initialize() {
        $this->belongsTo('user_id','Users','id',array(
                "foreignKey" => array(
                    "message" => "The user_id does not exist on the Users model"
                ))
        );
    }

}
