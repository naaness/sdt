<?php
use Phalcon\Mvc\Model\Validator\Uniqueness;
class Packages extends \Phalcon\Mvc\Model
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
    public $team_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $code;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var integer
     */
    public $user_id;

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
     * @var integer
     */
    public $status;

    public function validation()
    {
        $this->validate(new Uniqueness(array(
            'field'     =>      'code',
            'message'   =>      'El codigo ya existe'
        )));
        if ($this->validationHasFailed() == true) {
            return false;
        }
        return true;
    }

    public function validationCode()
    {
        $this->validate(new Uniqueness(array(
            'field'     =>      'code',
            'message'   =>      'El codigo ya existe'
        )));
        if ($this->validationHasFailed() == true) {
            return false;
        }
        return true;
    }

    public function initialize()
    {
        $this->hasMany("id", "UsersPackages", "package_id");
        $this->hasMany("id", "Tasks", "package_id");
        $this->belongsTo("user_id", "Users", "id");
        $this->belongsTo("team_id", "Teams", "id");
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'team_id' => 'team_id', 
            'name' => 'name', 
            'code' => 'code', 
            'description' => 'description', 
            'user_id' => 'user_id', 
            'created' => 'created', 
            'modified' => 'modified', 
            'status' => 'status'
        );
    }

    public function beforeCreate()
    {
        //Set the creation date
        $this->created = date('Y-m-d H:i:s');
        $this->modified = date('Y-m-d H:i:s');
    }

    public function beforeUpdate()
    {
        // Set the modification date
        $this->modified = date('Y-m-d H:i:s');
    }

}
