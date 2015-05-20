<?php

class UsersProjects extends \Phalcon\Mvc\Model
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
    public $project_id;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'project_id' => 'project_id'
        );
    }

    public function initialize(){
        $this->belongsTo('project_id','Projects','id',array(
                "foreignKey" => array(
                    "message" => "The project_id does not exist on the Projects model"
                ))
        );
        $this->belongsTo('user_id','Users','id',array(
                "foreignKey" => array(
                    "message" => "The user_id does not exist on the Users model"
                ))
        );
    }

}
