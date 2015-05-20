<?php

class UnidTimes extends \Phalcon\Mvc\Model
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
    public $start_day;

    /**
     *
     * @var string
     */
    public $back_day;

    /**
     *
     * @var string
     */
    public $next_day;

    /**
     *
     * @var integer
     */
    public $follow_up;

    /**
     *
     * @var integer
     */
    public $task_id_back;

    /**
     *
     * @var integer
     */
    public $task_id_next;

    /**
     *
     * @var integer
     */
    public $unit_time_id_parent;

    /**
     *
     * @var integer
     */
    public $priority_id;

    /**
     *
     * @var string
     */
    public $next_day_r;

    /**
     *
     * @var integer
     */
    public $next_time_r;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'task_id' => 'task_id', 
            'created' => 'created', 
            'modified' => 'modified', 
            'start_day' => 'start_day', 
            'back_day' => 'back_day', 
            'next_day' => 'next_day', 
            'follow_up' => 'follow_up', 
            'task_id_back' => 'task_id_back', 
            'task_id_next' => 'task_id_next', 
            'unit_time_id_parent' => 'unit_time_id_parent', 
            'priority_id' => 'priority_id',
            'next_day_r'=>'next_day_r',
            'next_time_r'=>'next_time_r'
        );
    }

    public function initialize()
    {
        $this->belongsTo("task_id", "Tasks", "id");
        $this->hasOne('id', 'Alerts', 'unid_time_id');
        $this->belongsTo("priority_id", "Priorities", "id");
        $this->hasOne('id', 'TasksRepeats', 'unid_time_id');
    }

    public function beforeValidationOnCreate() {
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d H:m:s');
        $this->created = $today;
        $this->modified = $today;
        if(!$this->back_day){
            $this->back_day = '0000-00-00';
        }
        if(!$this->next_day){
            $this->next_day = '0000-00-00';
        }
        if(!$this->follow_up){
            $this->follow_up = 1;
        }
        if(!$this->task_id_back){
            $this->task_id_back = 0;
        }
        if(!$this->unit_time_id_parent){
            $this->unit_time_id_parent = 0;
        }
        if(!$this->task_id_next){
            $this->task_id_next = 0;
        }
        $this->sanitizeAll();
    }

    public function beforeUpdate() {
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('Y-m-d');
        $this->modified = $today;
        $this->sanitizeAll();
    }

    public function beforeDelete() {
        $alert = Alerts::findByUnidTimeId($this->id);
        $alert->delete();
        return true;
    }
    public function sanitizeAll(){
        $sanitize =   array(
            'task_id' => 'int',
            'created' => 'string',
            'modified' => 'string',
            'start_day' => 'string',
            'back_day' => 'string',
            'next_day' => 'string',
            'follow_up' => 'int',
            'task_id_back' => 'int',
            'task_id_next' => 'int',
            'unit_time_id_parent' => 'int',
            'priority_id' => 'int',
            'next_day_r'=>'string',
            'next_time_r'=>'string'
        );
        $filter = new Phalcon\Filter();
        $this->task_id = $filter->sanitize($this->task_id,$sanitize['task_id']);
        $this->created = $filter->sanitize($this->created,$sanitize['created']);
        $this->modified = $filter->sanitize($this->modified,$sanitize['modified']);
        $this->start_day = $filter->sanitize($this->start_day,$sanitize['start_day']);
        $this->back_day = $filter->sanitize($this->back_day,$sanitize['back_day']);
        $this->next_day = $filter->sanitize($this->next_day,$sanitize['next_day']);
        $this->follow_up = $filter->sanitize($this->follow_up,$sanitize['follow_up']);
        $this->task_id_back = $filter->sanitize($this->task_id_back,$sanitize['task_id_back']);
        $this->task_id_next = $filter->sanitize($this->task_id_next,$sanitize['task_id_next']);
        $this->unit_time_id_parent = $filter->sanitize($this->unit_time_id_parent,$sanitize['unit_time_id_parent']);
        $this->priority_id = $filter->sanitize($this->priority_id,$sanitize['priority_id']);
        $this->next_day_r = $filter->sanitize($this->next_day_r,$sanitize['next_day_r']);
        $this->next_time_r = $filter->sanitize($this->next_time_r,$sanitize['next_time_r']);
    }

}
