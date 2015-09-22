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
class UserProfile {

    public $id;

    public $name;

    public $username;

    public $email;

    public $password;

    public $block;

    public $sendEmail;

    public $created;

    public $created_by;

    public $modified;

    public $modified_by;

    public $params;

    public $elements;

    public $access = 1;

    public $app;

    public  $user;

    public function __construct() {

        // get app instance
        $app = App::getInstance('zoo');

        // decorate data as object
        $this->params = $app->parameter->create($this->params);
        $this->elements = $app->parameter->create($this->elements);

    }

    /**
     * Check if the given usen can access this item
     *
     * @param  JUser $user The user to check
     *
     * @return boolean       If the user can access the item
     *
     * @since 2.0
     */
    public function canAccess($user = null) {
        return $this->app->userprofile->canAccess($user, $this->access);
    }

    /**
     * Gets the user object
     *
     * @return object  JUSer  The user object assigned to the profile
     *
     * @since 1.0
     */
    public function getUser() {
        if (empty($this->_user)) {
            $this->_user = $this->app->user->get($this->id);
        }
        return $this->_user;
    }
    /**
     * Gets the account object
     *
     * @return object  Account  The account object assigned to this user profile
     *
     * @since 1.0
     */
    public function getAccount() {
        if (empty($this->_account)) {
            $this->_account = $this->app->account->get($this->account_id, $this->account_type);
        }
        return $this->_account;
    }

    /**
     * Get the given parameter for the account object
     *
     * @param  string $name The parameter to retrieve
     *
     * @return mixed  The value of the parameter. Returns null if parameter does not exist
     *
     * @since 1.0
     */
    public function getParam($name) {
        return $this->params->get($name);
    }

    /**
     * Set the given parameter for the account object
     *
     * @param  string $name The parameter to set
     *
     * @param  mixed  $value The value of the parameter
     *
     * @return mixed  The value of the parameter.
     *
     * @since 1.0
     */
    public function setParam($name, $value) {
        return $this->params->set($name, $value);
    }
}
