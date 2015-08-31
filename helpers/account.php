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
    
    
    public function __construct($app) {
        parent::__construct($app);
        
        $app->loader->register('Account','classes:account.php');
    }
    
    public function get($name) {
        
        $class = $name.'Account';
        
        if (file_exists($this->app->path->path('classes:'.basename($class,'account').'.php'))) {
            $this->app->loader->register($class, 'classes:'.basename($class,'account').'.php');
        } else {
            $class = 'Account';
        }
        
        $object = new $class($this->app);
        
        return $object;
    }
    
}