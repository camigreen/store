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

        
	}

	public function get($id = null) {
		if(!$id) {
			$account = $this->app->object->create('account');
			// trigger init event
			$this->app->event->dispatcher->notify($this->app->event->create($account, 'account:init'));
			return $account;
		}

		if (!isset($this->_accounts[$id])) {
			$table = $this->app->table->account;
			$account = $table->get($id);
			$this->_accounts[$id] = $account;
		}
		
		return $this->_accounts[$id]; 
	}

	public function getByTypes() {
		return $this->app->table->account->all();
	}

	public function getByUser($user = null) {

		if(!$user) {
			$user = $this->app->userprofile->get();
		}

		$db = $this->app->database;

		$id = $db->queryResult('SELECT parent FROM #__zoo_account_user_map WHERE child = '.$user->id);

		if(!$id && !array_key_exists($id, $this->_accounts)) {
			return null;
		} 

		$account = $this->get($id);

		return $account;
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

    public function mapProfilesToAccount($aid, $pids = array()) {
    		$account = $this->get($aid);

    		$account_profiles = $account->getAssignedProfiles();

    		foreach($account_profiles as $key => $value) {
    			if(!in_array($pids)) {
    				$pids[] = $key;
    			}
    		}

    		$account->mapProfilesToAccount($pids);

    }

    

    
}