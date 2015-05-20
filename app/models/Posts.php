<?php

class Posts extends \Phalcon\Mvc\Model
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
    public $name;

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
    public $post;

    /**
     *
     * @var integer
     */
    public $parent_post;

    /**
     *
     * @var integer
     */
    public $parent_post_near;

    /**
     *
     * @var string
     */
    public $order_r;

    /**
     *
     * @var integer
     */
    public $count_likes;

    /**
     *
     * @var integer
     */
    public $count_dislikes;

    /**
     *
     * @var integer
     */
    public $count_views;

    /**
     *
     * @var integer
     */
    public $count_answers;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'name' => 'name', 
            'created' => 'created', 
            'modified' => 'modified', 
            'status' => 'status', 
            'post' => 'post', 
            'parent_post' => 'parent_post', 
            'parent_post_near' => 'parent_post_near', 
            'order_r' => 'order_r',
            'count_likes' => 'count_likes', 
            'count_dislikes' => 'count_dislikes', 
            'count_views' => 'count_views', 
            'count_answers' => 'count_answers'
        );
    }

    public function initialize()
    {
        $this->belongsTo("user_id", "Users", "id");
        $this->hasMany("id", "Postlikes", "post_id");
        $this->hasMany("id", "Postdislikes", "post_id");
    }

    public function sanitizeAll(){
        $f                      = new Phalcon\Filter();
        $this->user_id          = $f->sanitize($this->user_id,"int");
        $this->name             = $f->sanitize($this->name,"string");
        $this->created          = $f->sanitize($this->created,"string");
        $this->modified         = $f->sanitize($this->modified,"string");
        $this->status           = $f->sanitize($this->status,"int");
        // Post
        $this->parent_post      = $f->sanitize($this->parent_post,"int");
        $this->parent_post_near = $f->sanitize($this->parent_post_near,"int");
        $this->order_r          = $f->sanitize($this->order_r,"string");
        $this->count_likes      = $f->sanitize($this->count_likes,"int");
        $this->count_dislikes   = $f->sanitize($this->count_dislikes,"int");
        $this->count_views      = $f->sanitize($this->count_views,"int");
        $this->count_answers    = $f->sanitize($this->count_answers,"int");
    }

    public function beforeUpdate() {
        $this->sanitizeAll();
    }

    public function beforeValidationOnCreate() {
        $this->sanitizeAll();
    }
}
