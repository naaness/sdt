<?php

class Postlikes extends \Phalcon\Mvc\Model
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
    public $post_id;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'post_id' => 'post_id', 
            'user_id' => 'user_id'
        );
    }

    public function initialize()
    {
        $this->belongsTo("post_id", "Posts", "id");
        $this->belongsTo("user_id", "Users", "id");
    }

}
