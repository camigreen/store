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

    public $OEMs = array();

    public $parents = array();

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
    public function getOEMs() {

        if(!empty($this->OEMs)) {
            return $this->OEMs;
        }

        if(!$this->id) {
            return $this->OEMs;
        }

        $query = 'SELECT * FROM #__zoo_account_map WHERE parent = '.$this->id;

        $rows = $this->app->database->queryObjectList($query);

        foreach($rows as $row) {
                $this->OEMs[$row->child] = $this->app->account->get($row->child);
            
        }

        return $this->OEMs;

    }
    public function getParentAccounts() {

        if(!empty($this->parents)) {
            return $this->parents;
        }

        if(!$this->id) {
            return $this->parents;
        }

        $query = 'SELECT * FROM #__zoo_account_map WHERE child = '.$this->id;

        $rows = $this->app->database->queryObjectList($query);

        foreach($rows as $row) {
                $this->parents[$row->parent] = $this->app->account->get($row->parent);
            
        }

        return $this->parents;

    }

    public function getAssignedProfiles() {

        if(!empty($this->users)) {
            return $this->users;
        }

        if(!$this->id) {
            return $this->users;
        }

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

    public function removeParentMap($aid) {

        if(!$this->id) {
            return;
        }

        $query = 'DELETE FROM #__zoo_account_map WHERE parent = '.$aid.' AND child = '.$this->id;
        $this->app->database->query($query);
    }

    public function mapProfilesToAccount($map = array()) {

        if(!$this->id) {
            return;
        }
        
        $query = 'DELETE FROM #__zoo_account_user_map WHERE parent = '.$this->id;
        $this->app->database->query($query);

        if(empty($map)) {
            return ;
        }

        foreach($map as $profile) {
            $profile = $this->app->userprofile->get($profile);
            $profile->removeAccountMap();
            $query = 'INSERT INTO #__zoo_account_user_map (parent, child) VALUES ('.$this->id.','.$profile->id.')';
            $this->app->database->query($query);
        }
    }

    public function mapOEMsToAccount($map = array()) {

        if(!$this->id) {
            return;
        }
        
        $query = 'DELETE FROM #__zoo_account_map WHERE parent = '.$this->id;
        $this->app->database->query($query);

        if(empty($map)) {
            return ;
        }

        foreach($map as $subaccount) {
            $subaccount = $this->app->account->get($subaccount);
            $subaccount->removeParentMap($this->id);
            $query = 'INSERT INTO #__zoo_account_map (parent, child) VALUES ('.$this->id.','.$subaccount->id.')';
            $this->app->database->query($query);
        }
    }

    public function mapToParents($map) {

        if(!$this->id) {
            return;
        }
        $query = 'DELETE FROM #__zoo_account_map WHERE child = '.$this->id;
        $this->app->database->query($query);

        if(empty($map)) {
            return ;
        }

        foreach($map as $parent) {
            $parent = $this->app->account->get($parent);
            $query = 'INSERT INTO #__zoo_account_map (parent, child) VALUES ('.$parent->id.','.$this->id.')';
            $this->app->database->query($query);
        }
    }

    public function isTaxable() {
        return (bool) $this->elements->get('pricing.taxable', false);
    }

}