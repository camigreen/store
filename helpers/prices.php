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

    public function test() {
        include $this->app->path->path('prices:prices.php');
        $output = implode(', ', array_map(function ($v, $k) { return $k . '=' . $v; }, $prices['ubsk'], array_keys($prices['ubsk'])));
    }

    
    public function getRetail($group, $default = null, $formatCurrency = false) {
        $markup = $this->app->account->getCurrent()->elements->get('pricing.dealer_markup', 0);
    	include $this->app->path->path('prices:prices.php');
        $prices = $item;
        $prices = $this->app->parameter->create($prices);
        $search = $group;
        $search .= !empty($options) ? '.'.implode('.', $options) : '';
        if(!$result = $prices->get($search)) {
            $result = $default;
        }
        $result += $result*$markup;
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

    public function looper ($key = null, $array = array()) {

    }
}

