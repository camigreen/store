<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author Shawn Gibbons
 */
class Account {

    public $id;

    public $name;

    public $number;

    public $type;

    public $created;

    public $created_by;

    public $modified;

    public $modified_by;

    public $params;

    public function __construct() {

        // get app instance
        $app = App::getInstance('zoo');

        // decorate data as object
        $this->params = $app->parameter->create($this->params);
    }
}
