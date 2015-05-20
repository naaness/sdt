<?php

class SuccessLogins extends \Phalcon\Mvc\Model
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
    public $ip_address;

    /**
     *
     * @var string
     */
    public $user_agent;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'ip_address' => 'ip_address', 
            'user_agent' => 'user_agent'
        );
    }

    public function initialize()
    {
        $this->belongsTo('user_id', 'Users', 'id');
    }

}
