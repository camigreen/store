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

    public $kind;

    public $created;

    public $created_by;

    public $modified;

    public $modified_by;

    public $params;

    public $elements;

    public $access = 1;

    public $state = 1;

    public $app;

    protected $_mappedAccounts;

    protected $_mappedAccountsLoaded = false;

    public function __construct() {

    }

    /**
     * Get the account type
     *
     * @return string       The account type.
     *
     * @since 2.0
     */
    public function getType() {
        $type = $this->kind ? $this->type.'.'.$this->kind : $this->type;
        return JText::_('ACCOUNT_TYPE_'.$type);
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
     * Get a sub-account for the account
     *
     * @param  int $id The id of the subaccount to retrieve.
     *
     * @return mixed  Returns an Account Object if it exists.  If not, returns null
     *
     * @since 1.0
     */
    public function getChild($id) {

        if($this->id) {
            $this->_loadMappedAccounts();
        }
        
        $child = $this->_mappedAccounts->get('children.'.$id);

        return $child;

    }

    /**
     * Set a child account.
     *
     * @param  int $id The id of the child to set.
     *
     * @return object  Return $this for chaining.
     *
     * @since 1.0
     */
    public function setChild($id) {

        if($this->id) {
            $this->_loadMappedAccounts();
        }

        $this->_mappedAccounts->set('children.'.$id, $this->app->account->get($id));

        return $this;

    }

    /**
     * Get all child accounts
     *
     * @return array  Returns an array of Account objects.
     *
     * @since 1.0
     */
    public function getChildren() {

        if($this->id) {
            $this->_loadMappedAccounts();
        }

        return $this->_mappedAccounts->get('children');

    }

    /**
     * Get a parent for the account
     *
     * @param  int $id The id of the parent account to retrieve.
     *
     * @return mixed  Returns an Account Object if it exists.  If not, returns null
     *
     * @since 1.0
     */
    public function getParent($id) {

        if($this->id) {
            $this->_loadMappedAccounts();
        }
        
        $parent = $this->_mappedAccounts->get('parents.'.$id);

        return $parent;

    }

    /**
     * Set a parent account.
     *
     * @param  int $id The id of the parent to set.
     *
     * @return object  Return $this for chaining.
     *
     * @since 1.0
     */
    public function setParent($id) {

        if($this->id) {
            $this->_loadMappedAccounts();
        }

        $this->_mappedAccounts->set('parents.'.$id, $this->app->account->get($id));

        return $this;

    }

    /**
     * Get all parent accounts
     *
     * @return array  Returns an array of Account objects.
     *
     * @since 1.0
     */
    public function getParents() {

        if($this->id) {
            $this->_loadMappedAccounts();
        }

        return $this->_mappedAccounts->get('parents');

    }

    /**
     * Load sub-accounts into the cache.
     *
     * @param  boolean $reload Automatically reload all subaccounts from the database. Default is NULL
     *
     * @return array  Returns an array of Account Objects.
     *
     * @since 1.0
     */
    protected function _loadMappedAccounts($reload = false) {

        // if the subaccounts array is empty load all subaccounts from the database and hold them in cache
        if((!$this->_mappedAccountsLoaded || $reload) && $this->id) {
            $query = 'SELECT * FROM #__zoo_account_map WHERE parent = '.$this->id.' OR child = '.$this->id;

            $rows = $this->app->database->queryObjectList($query);

            $this->_mappedAccounts = $this->app->parameter->create();

            foreach($rows as $row) {
                if($row->parent == $this->id) {
                    $this->_mappedAccounts->set('children.'.$row->child, $this->app->account->get($row->child));
                }
                if($row->child == $this->id) {
                    $this->_mappedAccounts->set('parents.'.$row->parent, $this->app->account->get($row->parent));
                }
            }
        }

        $this->_mappedAccountsLoaded = true;

        return $this;

    }

    /**
     * Map all related accounts to the database
     *
     * @return object $this Account object for chaining.
     *
     * @since 1.0
     */
    protected function _mapRelatedAccounts() {

        // Load all parent and child accounts into the cache.
        $this->_loadMappedAccounts();

        // Remove all mappings where this account is the child from the database.
        $query = 'DELETE FROM #__zoo_account_map WHERE child = '.$this->id;
        $this->app->database->query($query);

        // Remove all mappings where this account is the parent from the database.
        $query = 'DELETE FROM #__zoo_account_map WHERE child = '.$this->id;
        $this->app->database->query($query);

        // Map all of the parent accounts to the database.
        foreach($this->_mappedAccounts->get('parents.') as $parent) {
            $query = 'INSERT INTO #__zoo_account_map (parent, child) VALUES ('.$parent->id.','.$this->id.')';
            $this->app->database->query($query);
        }

        // Map all of the child accounts to the database.
        foreach($this->_mappedAccounts->get('children.') as $child) {
            $query = 'INSERT INTO #__zoo_account_map (parent, child) VALUES ('.$this->id.','.$child->id.')';
            $this->app->database->query($query);
        }

        return $this;
    }

    public function isTaxable() {
        return (bool) $this->elements->get('pricing.taxable', false);
    }

}