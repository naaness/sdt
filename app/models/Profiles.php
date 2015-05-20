<?php

class Profiles extends \Phalcon\Mvc\Model
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
    public $last_name;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var string
     */
    public $about_bio;

    /**
     *
     * @var string
     */
    public $about_job;

    /**
     *
     * @var string
     */
    public $phone;

    /**
     *
     * @var string
     */
    public $mobile_phone;

    /**
     *
     * @var string
     */
    public $position;

    /**
     *
     * @var string
     */
    public $company;

    /**
     *
     * @var string
     */
    public $url_photo;

    /**
     *
     * @var string
     */
    public $subject_email;

    /**
     *
     * @var string
     */
    public $navbar_color;

    /**
     *
     * @var string
     */
    public $body_color;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'name' => 'name', 
            'last_name' => 'last_name', 
            'description' => 'description',
            'about_bio' => 'about_bio',
            'about_job' => 'about_job',
            'phone' => 'phone',
            'mobile_phone' => 'mobile_phone',
            'position' => 'position',
            'company' => 'company',
            'url_photo' => 'url_photo',
            'subject_email' => 'subject_email',
            'navbar_color'  =>  'navbar_color',
            'body_color'    =>  'body_color'
        );
    }

    public function initialize()
    {
        $this->belongsTo("user_id", "Users", "id");
    }

    public function sanitizeAll(){
        $sanitize = array(
            'user_id' => 'int',
            'name' => 'string',
            'last_name' => 'string',
            'description' => 'string',
            'about_bio' => 'string',
            'about_job' => 'string',
            'phone' => 'string',
            'mobile_phone' => 'string',
            'position' => 'string',
            'company' => 'string',
            'url_photo' => 'string',
            'subject_email' => 'string',
            'navbar_color'  =>  'string',
            'body_color'    =>  'string'
        );
        $filter = new Phalcon\Filter();
        $this->user_id = $filter->sanitize($this->user_id,$sanitize['user_id']);
        $this->name = $filter->sanitize($this->name,$sanitize['name']);
        $this->last_name = $filter->sanitize($this->last_name,$sanitize['last_name']);
        $this->description = $filter->sanitize($this->description,$sanitize['description']);
        $this->about_bio = $filter->sanitize($this->about_bio,$sanitize['about_bio']);
        $this->about_job = $filter->sanitize($this->about_job,$sanitize['about_job']);
        $this->phone = $filter->sanitize($this->phone,$sanitize['phone']);
        $this->mobile_phone = $filter->sanitize($this->mobile_phone,$sanitize['mobile_phone']);
        $this->position = $filter->sanitize($this->position,$sanitize['position']);
        $this->company = $filter->sanitize($this->company,$sanitize['company']);
        $this->url_photo = $filter->sanitize($this->url_photo,$sanitize['url_photo']);
        $this->subject_email = $filter->sanitize($this->subject_email,$sanitize['subject_email']);
        $this->navbar_color = $filter->sanitize($this->navbar_color,$sanitize['navbar_color']);
        $this->body_color = $filter->sanitize($this->body_color,$sanitize['body_color']);
    }
    public function beforeValidationOnCreate() {
        $this->sanitizeAll();
    }
    public function beforeUpdate(){
        $this->sanitizeAll();
    }
}
