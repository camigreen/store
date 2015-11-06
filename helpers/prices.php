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
class PricesHelper extends AppHelper {
    
    
    public function create() {
    	include $this->app->path->path('prices:retail.php');
        $_prices = $prices;
        $prices = $this->app->parameter->create($_prices);
        var_dump($prices);
	
    }

    public function looper ($array = array(), $key = null) {
    	foreach($array as $k => $value) {
    		$key = $key ? $key.'.'.$k : $k;
    		if(is_array($value)) {
    			$final = $this->looper($value, $key);
    			continue;
    		} else {
    			$final[] = $key;
    			$key = null;
    		}
    		
    		
    	}
    	return $final;

    }
}

