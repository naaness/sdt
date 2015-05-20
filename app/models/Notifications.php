<?php

class Notifications extends \Phalcon\Mvc\Model
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
    public $change_user_id;

    /**
     *
     * @var string
     */
    public $type;

    /**
     *
     * @var integer
     */
    public $change_id;

    /**
     *
     * @var string
     */
    public $date;

    /**
     *
     * @var integer
     */
    public $was_seen;

    /**
     *
     * @var integer
     */
    public $send_email;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'change_user_id' => 'change_user_id', 
            'type' => 'type',
            'change_id' => 'change_id',
            'date' => 'date', 
            'was_seen' => 'was_seen', 
            'send_email' => 'send_email'
        );
    }

    public function initialize()  {
        $this->belongsTo("user_id", "Users", "id");
        $this->belongsTo("change_user_id", "Users", "id");
    }

    public function beforeValidationOnCreate() {
        if(!$this->was_seen){
            $this->was_seen = 0;
        }
        if(!$this->send_email){
            $this->send_email = 0;
        }
    }

}
