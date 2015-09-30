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
class EmployeeAccount extends Account {

    public $type = 'employee';

    public $_user;

    /**
     * Gets the user object
     *
     * @return object  JUSer  The user object assigned to the profile
     *
     * @since 1.0
     */
    public function getUser() {
    	if (empty($this->_user) && $this->elements->get('user', 0) == 0) {
    		$this->_user = new JUser;
    	} else {
    		$this->_user = $this->app->user->get($this->elements->get('user'));
    	}
        return $this->_user;
    }

     /**
     * Checks if the user is the current user.
     *
     * @return boolean  True if the user is the current user.
     *
     * @since 1.0
     */
    public function isCurrentUser() {
        $cUser = $this->app->session->get('user')->id;
        $user = $this->getUser();
        return $cUser == $user->id ? true : false;
    }

}