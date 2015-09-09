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

	protected $_accounts;

	public function __construct($app) {
		parent::__construct($app);

		$this->app->loader->register('Account', 'classes:/accounts/default.php');

        
	}

	public function get($id, $type = null) {
		if (!isset($this->_accounts[$id])) {
			$table = $this->app->table->account;
			$account = $table->get($id, $type);
			$this->_accounts[$id] = $account;
		}
		
		return $this->_accounts[$id]; 
	}
    
    
    public function create($class = null, $args = array()) {
        
        if (!is_null($class) && file_exists($this->app->path->path('classes:/accounts/'.$class.'.php'))) {
            $this->app->loader->register($class, 'classes:/accounts/'.$class.'.php');
        } else {
            $class = 'Account';
        }
        
        $object = new $class();

        if (property_exists($object, 'app')) {
        	$object->app = $this->app;
        }

        $object->initParams();
        
        return $object;
    }
    
}