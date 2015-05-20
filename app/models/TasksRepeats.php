<?php

class TasksRepeats extends \Phalcon\Mvc\Model
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
     * @var integer
     */
    public $unid_time_id;

    /**
     *
     * @var integer
     */
    public $options;

    /**
     *
     * @var integer
     */
    public $each_period;

    /**
     *
     * @var integer
     */
    public $day_L;

    /**
     *
     * @var integer
     */
    public $day_M;

    /**
     *
     * @var integer
     */
    public $day_X;

    /**
     *
     * @var integer
     */
    public $day_J;

    /**
     *
     * @var integer
     */
    public $day_V;

    /**
     *
     * @var integer
     */
    public $day_S;

    /**
     *
     * @var integer
     */
    public $day_D;

    /**
     *
     * @var integer
     */
    public $month_week;

    /**
     *
     * @var string
     */
    public $start_day;

    /**
     *
     * @var integer
     */
    public $N_R_T;

    /**
     *
     * @var integer
     */
    public $repeat_interval;

    /**
     *
     * @var string
     */
    public $end_day;

    /**
     *
     * @var integer
     */
    public $day_position;


    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'task_id' => 'task_id', 
            'unid_time_id' => 'unid_time_id', 
            'options' => 'options', 
            'each_period' => 'each_period',
            'day_L' => 'day_L', 
            'day_M' => 'day_M', 
            'day_X' => 'day_X', 
            'day_J' => 'day_J', 
            'day_V' => 'day_V', 
            'day_S' => 'day_S', 
            'day_D' => 'day_D', 
            'month_week' => 'month_week', 
            'start_day' => 'start_day', 
            'N_R_T' => 'N_R_T', 
            'repeat_interval' => 'repeat_interval',
            'end_day' => 'end_day', 
            'day_position' => 'day_position'
        );
    }

    public function initialize(){
        $this->belongsTo("task_id", "Tasks", "id");
        $this->belongsTo("unid_time_id", "UnidTimes", "id");
    }

}
