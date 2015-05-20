<?php

class UsersTeams extends \Phalcon\Mvc\Model
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
    public $team_id;

    public function initialize(){
        $this->belongsTo('team_id','Teams','id',array(
                "foreignKey" => array(
                    "message" => "The team_id does not exist on the Teams model"
                ))
        );
        $this->belongsTo('user_id','Users','id',array(
            "foreignKey" => array(
                "message" => "The user_id does not exist on the Users model"
            ))
        );
    }
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'team_id' => 'team_id'
        );
    }

}
