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

    public $type = 'default';

    public $created;

    public $created_by;

    public $modified;

    public $modified_by;

    public $params;

    public $access = 1;

    public $state = 1;

    public $app;

    public $config;

    public function __construct() {

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
     * Check if the given usen can access this item
     *
     * @param  JUser $user The user to check
     *
     * @return boolean       If the user can access the item
     *
     * @since 2.0
     */
    public function getType() {
        return $this->type;
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
        if (is_array($this->params->get($name))) {
            return $this->app->parameter->create($this->params->get($name));
        }
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

    /**
     * Get the sub-account for the account
     *
     * @param  int $id The id of the subaccount to retrieve. Default is NULL
     *
     * @return mixed  Account Object or array of Account Objects
     *
     * @since 1.0
     */
    public function getSubAccount($id) {
        $subs = $this->params->get('subaccounts');
        if (!array_key_exists($id, $subs)) {
            return $this->app->error->raiseError(403, JText::_('Unable to access this sub-account'));
        }
        if (!array_key_exists($id, $this->subaccounts)) {
            $table = $this->app->table->account;
            $this->subaccounts[$id] = $table->get($id);
        }
        return $this->subaccounts[$id];
    }

    public function initParams() {

        require_once($this->app->path->path('classes:/accounts/config.php'));
        if (is_string($this->params) || is_null($this->params)) {
            // decorate data as this
            $this->params = $this->app->parameter->create($this->params);
        }
        $this->config = $config;
        foreach ($this->config['types'][$this->type] as $key => $value) {
            if(!$this->params->get($key)) {
                $this->params->set($key, $value);
            }
        }
    }
}
