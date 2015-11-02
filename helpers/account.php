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

	public function get($id) {
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
    
    
    public function create($class = null, $args = array()) {
    	list($parent, $child) = array_pad(explode('.',$class, 2), 2, null);

    	$class = $child == null ? $parent.'Account' : $child.'Account';
        
        if (!is_null($class) && file_exists($this->app->path->path('classes:/accounts/'.basename($class, 'Account').'.php'))) {
            $this->app->loader->register($class, 'classes:/accounts/'.basename($class, 'Account').'.php');
        } else {
            $class = 'Account';
        }
        
        $object = new $class();

        if (property_exists($object, 'app')) {
        	$object->app = $this->app;
        }

        // trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'account:init'));
        
        return $object;
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