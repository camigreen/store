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

    public function __construct() {

        $app = App::getInstance('zoo');

        $this->params = $app->parameter->create($this->params);

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
    public function canEdit($user = null) {
        var_dump($this->app->zoo->getApplication()->assetRules);
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
    public function getSubAccounts($type = 'default') {
        $class = $type == 'default' ? 'Account' : $type.'Account';
        if($this->app->path->path('classes:accounts/'.$type.'.php')) {
            $this->app->loader->register($class, 'classes:accounts/'.$type.'.php');
        } else {
            $class = 'Account';
            $this->app->loader->register($class, 'classes:accounts/default.php');
        }
        
        $result = $this->app->database->query('SELECT b.* FROM #__zoo_account_link AS a LEFT JOIN (#__zoo_account AS b) ON (a.child = b.id) WHERE a.parent = '.$this->id.' AND b.type = "'.$type.'"');
        $objects = array();
        while ($object = $this->app->database->fetchObject($result, $class)) { 
            $objects[$object->id] = $object;
        }
        return $objects;
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
    public function saveSubAccounts() {
        if(!$this->app->database->query('DELETE FROM #__zoo_account_link WHERE parent = "'.$this->id.'"')) {
            $this->app->error->raiseError(500, JText::_('There was an error saving the related sub-accounts'));
            return;
        }
        $subs = $this->params->get('sub-accounts.');
        $obj = $this->app->data->create();
        $obj->parent = $this->id;
        foreach ($subs as $type => $accounts) {
            foreach($accounts as $account) {
                $obj->child = $account;
                $this->app->database->insertObject('#__zoo_account_link', $obj);
            }
 
        }
        
        return false;
    }

}
