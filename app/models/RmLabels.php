<?php

class RmLabels extends \Phalcon\Mvc\Model
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
    public $color;

    /**
     *
     * @var string
     */
    public $b_color;

    /**
     *
     * @var string
     */
    public $b_color_checked;

    /**
     *
     * @var integer
     */
    public $rm_font_id;

    /**
     *
     * @var integer
     */
    public $rm_size_id;

    public function initialize(){
        $this->belongsTo("user_id", "Users", "id");
        $this->hasMany('id','RmRegistries','rm_label_id');
        $this->belongsTo("rm_font_id", "RmFonts", "id");
        $this->belongsTo("rm_size_id", "RmSizes", "id");
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'name' => 'name', 
            'color' => 'color', 
            'b_color' => 'b_color', 
            'b_color_checked' => 'b_color_checked', 
            'rm_font_id' => 'rm_font_id', 
            'rm_size_id' => 'rm_size_id'
        );
    }

    public function beforeValidationOnCreate() {
        $this->sanitizeAll();
    }

    public function beforeUpdate() {
        $this->sanitizeAll();
    }

    public function sanitizeAll(){
        $f                      = new Phalcon\Filter();
        $this->user_id          = $f->sanitize($this->user_id,"int");
        $this->name             = $f->sanitize($this->name,"string");
        $this->color            = $f->sanitize($this->color,"string");
        $this->b_color          = $f->sanitize($this->b_color,"string");
        $this->b_color_checked  = $f->sanitize($this->b_color_checked,"string");
        $this->rm_font_id       = $f->sanitize($this->rm_font_id,"int");
        $this->rm_size_id       = $f->sanitize($this->rm_size_id,"int");
    }

}
