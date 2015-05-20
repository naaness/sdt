<?php

class StepDays extends \Phalcon\Mvc\Model
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
     * @var integer
     */
    public $cb_1;

    /**
     *
     * @var integer
     */
    public $cb_2;

    /**
     *
     * @var integer
     */
    public $cb_3;

    /**
     *
     * @var integer
     */
    public $cb_4;

    /**
     *
     * @var integer
     */
    public $cb_5;

    /**
     *
     * @var integer
     */
    public $cb_6;

    /**
     *
     * @var integer
     */
    public $cb_7;

    /**
     *
     * @var integer
     */
    public $cb_8;

    /**
     *
     * @var integer
     */
    public $cb_9;

    /**
     *
     * @var integer
     */
    public $cb_10;

    /**
     *
     * @var integer
     */
    public $cb_11;

    /**
     *
     * @var integer
     */
    public $cb_12;

    /**
     *
     * @var string
     */
    public $text_12;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'date' => 'date', 
            'cb_1' => 'cb_1', 
            'cb_2' => 'cb_2', 
            'cb_3' => 'cb_3', 
            'cb_4' => 'cb_4', 
            'cb_5' => 'cb_5', 
            'cb_6' => 'cb_6', 
            'cb_7' => 'cb_7', 
            'cb_8' => 'cb_8', 
            'cb_9' => 'cb_9', 
            'cb_10' => 'cb_10', 
            'cb_11' => 'cb_11', 
            'cb_12' => 'cb_12', 
            'text_12' => 'text_12'
        );
    }

    public function initialize()
    {
        $this->belongsTo("user_id", "Users", "id");
    }

}
