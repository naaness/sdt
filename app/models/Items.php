<?php

class Items extends \Phalcon\Mvc\Model
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
    public $subcategory_id;

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

    /**
     *
     * @var string
     */
    public $url_photo;

    /**
     *
     * @var double
     */
    public $price;

    public function initialize(){
        $this->belongsTo("subcategory_id", "Subcategories", "id");
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'subcategory_id' => 'subcategory_id', 
            'name' => 'name', 
            'description' => 'description', 
            'created' => 'created', 
            'modified' => 'modified', 
            'status' => 'status', 
            'user_create' => 'user_create', 
            'user_modify' => 'user_modify', 
            'url_photo' => 'url_photo', 
            'price' => 'price'
        );
    }

}
