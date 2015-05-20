<?php

class RmSizes extends \Phalcon\Mvc\Model
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
    public $size;

    public function initialize(){
        $this->hasMany('id','RmLabels','rm_size_id');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'size' => 'size'
        );
    }

}
