<?php
use Phalcon\Mvc\Model\Validator\Uniqueness;
class Projects extends \Phalcon\Mvc\Model
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

    /**
     *
     * @var string
     */
    public $description;

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
            'user_id' => 'user_id', 
            'created' => 'created', 
            'modified' => 'modified', 
            'status' => 'status', 
            'description' => 'description'
        );
    }

    /*
    * @desc - antes de pasar la validación
    */
    public function beforeValidationOnCreate()
    {

    }

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
        $this->hasMany("id", "UsersProjects", "project_id");
        $this->hasMany("id", "Tasks", "project_id");
        $this->belongsTo("user_id", "Users", "id");
        $this->belongsTo("team_id", "Teams", "id");
        $this->sanitizeAll();
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
        $this->sanitizeAll();
    }

    public function sanitizeAll(){
        $f                      = new Phalcon\Filter();
        $this->team_id          = $f->sanitize($this->team_id,"int");
        $this->name             = $f->sanitize($this->name,"string");
        $this->code             = $f->sanitize($this->code,"string");
        $this->user_id          = $f->sanitize($this->user_id,"int");
        $this->created          = $f->sanitize($this->created,"string");
        $this->modified         = $f->sanitize($this->modified,"string");
        $this->status           = $f->sanitize($this->status,"int");
        $this->description      = $f->sanitize($this->description,"string");
    }

}
