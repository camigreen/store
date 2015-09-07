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
class AccountHelper extends ObjectHelper {
    
    
    public function create($class = null, $args = array()) {
        
        if (!is_null($class) && file_exists($this->app->path->path('classes:/accounts/'.$class.'.php'))) {
            $this->app->loader->register($class, 'classes:/accounts/'.$class.'.php');
        } else {
            $class = 'Account';
        }
        
        $object = new $class($this->app);
        
        return $object;
    }
    
}