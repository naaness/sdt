<?php

class UsersPackages extends \Phalcon\Mvc\Model
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
    public $package_id;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'package_id' => 'package_id', 
            'status' => 'status'
        );
    }

    public function initialize(){
        $this->belongsTo('package_id','Packages','id',array(
                "foreignKey" => array(
                    "message" => "The package_id does not exist on the Packages model"
                ))
        );
        $this->belongsTo('user_id','Users','id',array(
                "foreignKey" => array(
                    "message" => "The user_id does not exist on the Users model"
                ))
        );
    }

}
