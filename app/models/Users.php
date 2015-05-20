<?php

use Phalcon\Mvc\Model\Validator\Email as Email;
use Phalcon\Mvc\Model\Validator\Uniqueness;
class Users extends \Phalcon\Mvc\Model
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
    public $username;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $role;

    /**
     *
     * @var integer
     */
    public $status;

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
    public $step_of_day;

    /**
     *
     * @var string
     */
    public $date_step_day;

    /**
     *
     * @var integer
     */
    public $sync_calendar;

    /**
     *
     * @var string
     */
    public $rm_token;

    /**
     *
     * @var string
     */
    public $ch_token;

    /**
     *
     * @var string
     */
    public $htd_token;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'username' => 'username', 
            'password' => 'password', 
            'email' => 'email', 
            'role' => 'role', 
            'status' => 'status', 
            'created' => 'created', 
            'modified' => 'modified', 
            'step_of_day' => 'step_of_day',
            'date_step_day' =>  'date_step_day',
            'sync_calendar' => 'sync_calendar', 
            'rm_token' => 'rm_token', 
            'ch_token' => 'ch_token', 
            'htd_token' => 'htd_token'
        );
    }

    /*
    * @desc - añadimos los campos que no queremos insertar
    */
    public function initialize() {
        // $this->skipAttributesOnCreate(array('created_at','active'));
        // $this->hasMany($this,'id','Categories','user_create', null);
        // $this->hasMany($this,'id','Categories','user_modify', null);

//        $this->hasMany('id','subcategories','user_create');
//        $this->hasMany('id','subcategories','user_modify');
//        $this->hasMany('id','items','user_create');
//        $this->hasMany('id','items','user_modify');
        $this->hasOne('id', 'Profiles', 'user_id');

        $this->hasMany("id", "UsersTeams", "user_id");
        $this->hasMany("id", "UsersProjects", "user_id");
        $this->hasMany("id", "UsersPackages", "user_id");
        $this->hasMany("id", "UsersTasks", "user_id");
        $this->hasMany("id", "Posts", "user_id");
        $this->hasOne('id', 'StepDays', 'user_id');
        $this->hasMany("id", "Alerts", "user_id");
        $this->hasMany("id", "Projects", "user_id");
        $this->hasMany("id", "Packages", "user_id");
        $this->hasMany("id", "Notifications", "user_id");
        $this->hasMany("id", "Notifications", "change_user_id");
        $this->hasMany("id", "Comments", "user_id");
        $this->hasOne('id', 'RememberTokens', 'user_id');
        $this->hasMany("id", "SuccessLogin", "user_id");
        $this->hasMany("id", "TasksMessages", "user_id");
        $this->hasMany("id", "Postlikes", "user_id");
        $this->hasMany("id", "Postdislikes", "user_id");
        $this->hasOne('id', 'UsersChecklist', 'user_id');

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
            'field'     =>      'username',
            'message'   =>      'El Nombre de Usuario ya existe'
        )));
        $this->validate(new Uniqueness(array(
            'field'     =>      'email',
            'message'   =>      'El Correo ya existe'
        )));
        if ($this->validationHasFailed() == true) {
            return false;
        }
        return true;
    }

    public function validationUsername()
    {
        $this->validate(new Uniqueness(array(
            'field'     =>      'username',
            'message'   =>      'El Nombre de Usuario ya existe'
        )));
        if ($this->validationHasFailed() == true) {
            return false;
        }
        return true;
    }

    public function validationEmail()
    {
        $this->validate(new Uniqueness(array(
            'field'     =>      'email',
            'message'   =>      'El Correo ya existe'
        )));
        if ($this->validationHasFailed() == true) {
            return false;
        }
        return true;
    }

    /*
    * @desc - después de pasar la validación encriptamos el password para guardar en la bd
    */
    public function afterValidationOnCreate()
    {
        // $security = new Phalcon\Security();
        // $this->password = $security->hash($this->password);
        $this->password = sha1(md5($this->password));
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
        $sanitize =  array(
            'username' => 'string',
            'password' => 'string',
            'email' => 'email',
            'role' => 'string',
            'status' => 'int',
            'created' => 'string',
            'modified' => 'string',
            'step_of_day' => 'int',
            'date_step_day' =>  'string',
            'sync_calendar' => 'int',
            'rm_token' => 'string',
            'ch_token' => 'string',
            'htd_token' => 'string'
        );
        $filter = new Phalcon\Filter();
        $this->password = $filter->sanitize($this->password,$sanitize['password']);
        $this->email = $filter->sanitize($this->email,$sanitize['email']);
        $this->role = $filter->sanitize($this->role,$sanitize['role']);
        $this->status = $filter->sanitize($this->status,$sanitize['status']);
        $this->created = $filter->sanitize($this->created,$sanitize['created']);
        $this->modified = $filter->sanitize($this->modified,$sanitize['modified']);
        $this->step_of_day = $filter->sanitize($this->step_of_day,$sanitize['step_of_day']);
        $this->date_step_day = $filter->sanitize($this->date_step_day,$sanitize['date_step_day']);
        $this->sync_calendar = $filter->sanitize($this->sync_calendar,$sanitize['sync_calendar']);
        $this->rm_token = $filter->sanitize($this->rm_token,$sanitize['rm_token']);
        $this->ch_token = $filter->sanitize($this->ch_token,$sanitize['ch_token']);
        $this->htd_token = $filter->sanitize($this->htd_token,$sanitize['htd_token']);
    }

}
