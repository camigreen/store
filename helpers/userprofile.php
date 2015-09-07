<?php defined('_JEXEC') or die('Restricted access');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// $zoo = App::getInstance('zoo');
// $zoo->loader->register('UserAppHelper', 'helpers:user.php');
/**
 * Description of newPHPClass
 *
 * @author Shawn
 */
class UserProfileHelper extends UserAppHelper {

    public $user;
    public $_accounts;

    public function getName() {
        return 'userprofile';
    }

    public function get($id = null) {
        $this->user = parent::get($id);
        $accounts = $this->user->getParam('accounts');
        foreach($accounts as $type => $account_id) {
            $this->_accounts[$account_id] = $this->app->table->account->get($account_id, $type);
            $this->_accounts[$account_id]->initParams();
        }
        return $this;
    }

    public function canCreateOrders($user = null, $asset_id = 0) {
        if(!isset($this->user)) {
            $this->user = $this->get();
        }
        return $this->isAdmin($this->user, $asset_id) || $this->authorise($this->user, 'order.create', $asset_id);

    }
    public function canEditOrders($user = null, $asset_id = 0) {
        if(!isset($this->user)) {
            $this->user = $this->get();
        } 
        return $this->isAdmin($this->user, $asset_id) || $this->authorise($this->user, 'order.edit', $asset_id);

    }
    public function canDeleteOrders($user = null, $asset_id = 0) {
        if(!isset($this->user)) {
            $this->user = $this->get();
        } 
        return $this->isAdmin($this->user, $asset_id) || $this->authorise($this->user, 'order.delete', $asset_id);

    }

}