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

    public $created;

    public $created_by;

    public $modified;

    public $modified_by;

    public $params;

    public $elements;

    public $access = 1;

    public $app;

    public $_user;

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
     * Gets the user object
     *
     * @return object  JUSer  The user object assigned to the profile
     *
     * @since 1.0
     */
    public function isCurrentUser() {
        $cUser = $this->app->session->get('user')->id;
        return $cUser == $this->id ? true : false;
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
            $this->_account = $this->app->account->get($this->elements->get('account'));
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

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     * @param int $created_by
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canEdit($user = null, $asset_id = 0, $created_by = 0) {
        if (is_null($user)) {
            $user = $this->getUser();
        }
        return $this->isAdmin($user, $asset_id) || $this->authorise($user, 'core.edit', $asset_id) || ($created_by === $user->id && $user->authorise('core.edit.own', $asset_id));
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canEditState($user = null, $asset_id = 0) {
        return $this->isAdmin($user, $asset_id) || $this->authorise($user, 'core.edit.state', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canCreate($user = null, $asset_id = 0) {
        return $this->isAdmin($user, $asset_id) || $this->authorise($user, 'core.create', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canDelete($user = null, $asset_id = 0) {
        return $this->isAdmin($user, $asset_id) || $this->authorise($user, 'core.delete', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canManage($user = null, $asset_id = 0) {
        return $this->isAdmin($user, $asset_id) || $this->authorise($user, 'core.manage', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function isAdmin($user = null, $asset_id = 0) {
        return $this->authorise($user, 'core.admin', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param string $action
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    protected function authorise($user, $action, $asset_id) {
        if (!$asset_id) {
            $asset_id = 'com_zoo';
        }
        if (is_null($user)) {
            $user = $this->get();
        }

        return (bool) $user->authorise($action, $asset_id);
    }
}
