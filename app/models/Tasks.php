<?php

class Tasks extends \Phalcon\Mvc\Model
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
    public $package_id;

    /**
     *
     * @var integer
     */
    public $project_id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var integer
     */
    public $blocked;

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
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $edit;

    /**
     *
     * @var integer
     */
    public $priority_id;

    /**
     *
     * @var string
     */
    public $percent;

    /**
     *
     * @var integer
     */
    public $task_id_parent;

    public function initialize(){
        $this->belongsTo("user_id", "Users", "id");
        $this->belongsTo("priority_id", "Priorities", "id");
        $this->hasMany("id", "UnidTimes", "task_id");
        $this->hasMany("id", "UsersTasks", "task_id");
        $this->belongsTo("project_id", "Projects", "id");
        $this->belongsTo("package_id", "Packages", "id");
        $this->hasMany("id", "Comments", "task_id");
        $this->hasMany("id", "RmRegistriesTasks", "task_id");
        $this->hasMany("id", "Delegates", "task_id");
        $this->hasMany("id", "TasksMessages", "task_id");
        $this->hasMany("id", "TasksRepeats", "task_id");
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'package_id' => 'package_id', 
            'project_id' => 'project_id', 
            'name' => 'name', 
            'description' => 'description', 
            'blocked' => 'blocked', 
            'created' => 'created', 
            'modified' => 'modified', 
            'status' => 'status', 
            'user_id' => 'user_id', 
            'edit' => 'edit', 
            'priority_id' => 'priority_id', 
            'percent' => 'percent', 
            'task_id_parent' => 'task_id_parent'
        );
    }

    public function beforeValidationOnCreate() {
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d H:m:s');
        $this->created = $today;
        $this->modified = $today;
        if(!$this->package_id){
            $this->package_id=0;
        }
        if(!$this->project_id){
            $this->project_id=0;
        }
        if(!$this->blocked){
            $this->blocked=0;
        }
        if(!$this->status){
            $this->status=1;
        }
        if(!$this->percent){
            $this->percent='00.00';
        }
        if(!$this->task_id_parent){
            $this->task_id_parent=0;
        }
        if(!$this->edit){
            $this->edit=0;
        }
        $this->sanitizeAll();
    }

    public function beforeUpdate() {
        // Set the modification date
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d H:m:s');
        $this->modified = $today;
        $this->sanitizeAll();
    }

    public function sanitizeAll(){
        $f                      = new Phalcon\Filter();
        $this->package_id       = $f->sanitize($this->package_id,"int");
        $this->project_id       = $f->sanitize($this->project_id,"int");
        $this->name             = $f->sanitize($this->name,"string");
        $this->description      = $f->sanitize($this->description,"string");
        $this->blocked          = $f->sanitize($this->blocked,"int");
        $this->created          = $f->sanitize($this->created,"string");
        $this->modified         = $f->sanitize($this->modified,"int");
        $this->status           = $f->sanitize($this->status,"int");
        $this->user_id          = $f->sanitize($this->user_id,"int");
        $this->edit             = $f->sanitize($this->edit,"int");
        $this->priority_id      = $f->sanitize($this->priority_id,"int");
        $this->percent          = $f->sanitize($this->percent,"string");
        $this->task_id_parent   = $f->sanitize($this->task_id_parent,"int");
    }
}
