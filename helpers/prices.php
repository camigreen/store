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

    
    public function getRetail($group, $options = array(), $default = null, $formatCurrency = false) {
    	include $this->app->path->path('prices:prices.php');
        $prices = $item;
        $prices = $this->app->parameter->create($prices);
        $search = $group;
        $search .= !empty($options) ? '.'.implode('.', $options) : '';
        if(!$result = $prices->get($search)) {
            $result = $default;
        }
        if($formatCurrency) {
            $result = $this->app->number->currency($result ,array('currency' => 'USD'));
        }
        return $result;
	
    }

    public function getShipping($group, $options = array(), $default = array()) {
        include $this->app->path->path('prices:prices.php');
        $prices = $shipping;
        $prices = $this->app->parameter->create($prices);
        $search = $group;
        $search .= !empty($options) ? '.'.implode('.', $options).'.' : '.';
        if(!$result = $prices->get($search)) {
            $result = $default;
        }
        $result = $this->app->parameter->create($result);
        return $result;
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

