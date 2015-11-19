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

    protected $items;
    protected $shipping;

    public function __construct($app) {
        parent::__construct($app);
        include $this->app->path->path('prices:prices.php');
        $this->items = $this->app->parameter->create($item);
        $this->shipping = $this->app->parameter->create($shipping);
    }

    public function test() {
        include $this->app->path->path('prices:prices.php');
        $output = implode(', ', array_map(function ($v, $k) { return $k . '=' . $v; }, $prices['ubsk'], array_keys($prices['ubsk'])));
    }

    
    public function get($group, $type = 'discount', $default = null, $formatCurrency = false, $currency = 'USD') {   
        $search = $group;
        $search .= !empty($options) ? '.'.implode('.', $options) : '';
        $retail = $this->items->get($search, $default);
        $account = $this->app->account->getCurrent();
        switch ($type) {
            case 'retail':
                $result = $retail;
                break;
            case 'discount':
                $discount = (float) $account->elements->get('pricing.discount', 0);
                $retail -= $retail*$discount;
                $result = $retail;
                break;
            case 'markup':
                $markup = $account->elements->get('pricing.markup', 0);
                $retail += $retail*$markup;
                $result = $retail;
                break;
        }

        if($formatCurrency) {
            return $this->formatCurrency($result, $currency);
        } 

        return $result;
	
    }

    public function getShipping($group, $type = 'weight', $default = null, $formatCurrency = false, $currency = 'USD') {
        $search = $group.'.'.$type;
        $result = $this->shipping->get($search);

        if($formatCurrency) {
            return $this->formatCurrency($result, $currency);
        }

        return $result;
    }

    public function formatCurrency ($value, $currency = 'USD') {
        $value = (float) $value;
        return $this->app->number->currency($value ,array('currency' => $currency));
    }
}

