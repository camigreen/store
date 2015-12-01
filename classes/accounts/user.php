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

    protected $_user;

    public function __construct() {
        parent::__construct();
    }

    public function loadUser() {
        if(!$this->elements->get('user')) {
            $this->_user = new JUser();
        }

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

    public function getParentAccount() {
        $parents = array_values($this->getParents());
        list($account) = $parents;
        return $account;
    }



}