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

    public $elements;

    public $access = 1;

    public $state = 1;

    public $app;

    public $subaccounts;

    public $users;

    public function __construct() {

        $app = App::getInstance('zoo');

        $this->params = $app->parameter->create($this->params);
        $this->elements = $app->parameter->create($this->elements);

    }

    /**
     * Get the account type
     *
     * @return string       The account type.
     *
     * @since 2.0
     */
    public function getType() {
        return JText::_('ACCOUNT_TYPE_'.$this->type);
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
    public function getParams() {
        $params = array();
        foreach($this->params as $k => $v) {
            $value = explode('.',$k, 5);
            if (!in_array($value[0], $params)) {
                $params[] = $value[0];
            }
        }
        return $params;
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
     * Set the status for the account object
     *
     * @param  string $state The parameter to retrieve
     * 
     * @param  boolean $save Automatically save to the database? default = false
     *
     * @return Account $this for chaining support.
     *
     * @since 1.0
     */
    public function setState($state, $save = false) {
        if ($this->state != $state) {

            // set state
            $old_state   = $this->state;
            $this->state = $state;

            // autosave comment ?
            if ($save) {
                $this->app->table->account->save($this);
            }

            // fire event
            $this->app->event->dispatcher->notify($this->app->event->create($this, 'account:stateChanged', compact('old_state')));
        }

        return $this;
    }

        /**
     * Get the state account object
     *
     * @return string  The human readable value of the account state.
     *
     * @since 1.0
     */
    public function getState() {
        return JText::_($this->app->status->get('account', $this->state));
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
    public function getSubAccounts() {

        $query = 'SELECT * FROM #__zoo_account_map WHERE parent = '.$this->id;

        $rows = $this->app->database->queryObjectList($query);

        foreach($rows as $row) {
                $this->subaccounts[$row->child] = $this->app->account->get($row->child);
            
        }

        return $this->subaccounts;

    }

    public function getUsers() {

        $query = 'SELECT child FROM #__zoo_account_user_map WHERE parent = '.$this->id;

        $rows = $this->app->database->queryResultArray($query);

        foreach($rows as $row) {
                $this->users[$row] = $this->app->userprofile->get($row);
            
        }

        return $this->users;

    }

    public function getUser($id) {
        return $this->_users[$id];
    }

    public function mapProfilesToAccount($map = array()) {
        
        $query = 'DELETE FROM #__zoo_account_user_map WHERE parent = '.$this->id;
        $this->app->database->query($query);

        if(empty($map)) {
            return ;
        }

        foreach($map as $profile) {
            $query = 'INSERT INTO #__zoo_account_user_map (parent, child) VALUES ('.$this->id.','.$profile.')';
            $this->app->database->query($query);
        }
    }

}