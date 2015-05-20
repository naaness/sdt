<?php

class Subcategories extends \Phalcon\Mvc\Model
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
    public $category_id;

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
    public $user_create;

    /**
     *
     * @var integer
     */
    public $user_modify;

    public function initialize(){
        $this->belongsTo("category_id", "Categories", "id");
        $this->hasMany('id','Items','subcategory_id');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'category_id' => 'category_id', 
            'name' => 'name', 
            'description' => 'description', 
            'created' => 'created', 
            'modified' => 'modified', 
            'status' => 'status', 
            'user_create' => 'user_create', 
            'user_modify' => 'user_modify'
        );
    }

}
