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
class UserAccount extends Account {

    public $name;

    public $type = 'user';

    public $kind = 'dealership';

    protected $_user;

    public function __construct() {
        parent::__construct();
    }

    public function loadUser() {
        if(empty($this->_user)) {
            $this->_user = $this->app->user->get($this->elements->get('user'));
        }
        $this->name = $this->_user->name;
        return $this;
    }

    public function getUser() {
        $this->loadUser();
        return $this->_user;
    }

    // public function __get($name) {
    //     if(property_exists($this->_user, $name)){
    //         return $this->_user->$name;
    //     }
    //     return $this->$name;
    // }

    // public function __set($name, $value) {
    //     if(property_exists($this, $name)) {
    //         $this->$name = $value;
    //     }
    // }

}