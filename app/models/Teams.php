<?php

class Teams extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

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
     * @var integer
     */
    public $leader_id;

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
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'name' => 'name', 
            'description' => 'description', 
            'leader_id' => 'leader_id', 
            'created' => 'created', 
            'modified' => 'modified', 
            'status' => 'status'
        );
    }

    public function initialize()
    {
        $this->hasMany("id", "UsersTeams", "team_id");
        $this->hasMany("id", "Projects", "project_id");
        $this->hasMany("id", "Packages", "package_id");
    }

    public function beforeCreate()
    {
        //Set the creation date
        $this->created = date('Y-m-d H:i:s');
        $this->sanitizeAll();
    }

    public function beforeUpdate()
    {
        // Set the modification date
        $this->modified = date('Y-m-d H:i:s');
        $this->sanitizeAll();
    }

    public function sanitizeAll(){
        $sanitize =  array(
            'name' => 'string',
            'description' => 'string',
            'leader_id' => 'int',
            'created' => 'string',
            'modified' => 'string',
            'status' => 'int'
        );
        $filter = new Phalcon\Filter();
        $this->name = $filter->sanitize($this->name,$sanitize['name']);
        $this->description = $filter->sanitize($this->description,$sanitize['description']);
        $this->leader_id = $filter->sanitize($this->leader_id,$sanitize['leader_id']);
        $this->created = $filter->sanitize($this->created,$sanitize['created']);
        $this->modified = $filter->sanitize($this->modified,$sanitize['modified']);
        $this->status = $filter->sanitize($this->created,$sanitize['status']);
    }

}
