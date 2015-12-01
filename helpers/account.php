<?php defined('_JEXEC') or die('Restricted access');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author Shawn
 */
class AccountHelper extends AppHelper {

	protected $_accounts = array();

	public function __construct($app) {
		parent::__construct($app);

		$this->app->loader->register('Account', 'classes:account.php');
		$this->table = $this->app->table->account;

        
	}

	public function get($id) {

		if (!isset($this->_accounts[$id])) {
			$account = $this->table->get($id);
			$this->_accounts[$id] = $account;
		}
		
		return $this->_accounts[$id]; 
	}

	public function create($type = 'default') {

		if($type == 'default') {
			$class = 'Account';
		} else {
			list($_type) = explode('.', $type,2);
			$class = $_type."Account";
			$this->app->loader->register($class, 'classes:accounts/'.strtolower($_type).'.php');
		}

		$account = new $class();
		$account->type = $type;
		$account->app = $this->app;

		// trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($account, 'account:init'));

		return $account;

	}

	public function getByTypes() {
		return $this->app->table->account->all();
	}

	public function getStoreAccount() {
		return $this->get(7);
	}

	public function getByUser($user = null) {

		if(!$user || !$user->id) {
			$account = $this->app->object->create('account');
			$this->app->event->dispatcher->notify($this->app->event->create($account, 'account:init'));
			return $account;
		}

		$db = $this->app->database;

		$id = $db->queryResult('SELECT parent FROM #__zoo_account_user_map WHERE child = '.$user->id);

		if(!$id) {
			return null;
		} 

		$account = $this->get($id);

		return $account;
	}

	public function getCurrent() {
		$user = $this->app->user->get();
		$accounts = $this->table->all(array('conditions' => "type LIKE 'user.%'"));
		foreach($accounts as $account) {
			if($account->elements->get('user') == $user->id) {
				return $this->get($account->id);
			}
		}
		return;
	}

	public function getUnassignedOEMs($options = null) {
		$oems = $this->app->table->account->getUnassignedOEMs();
		$assignments = array();
        foreach($oems as $oem) {
            if($oem->parent) {
                $assignments[$oem->parent][$oem->id] = $oem;
            } else {
                $assignments['unassigned'][$oem->id] = $oem;
            }
        }
        return $assignments;

	}

    

    
}