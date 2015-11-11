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
class OrderDev {

	public $id;
	public $created;
	public $created_by;
	public $modified;
	public $modified_by;
	public $params;
	public $elements;
	public $access = 12;
	public $status;
	public $subtotal;
	public $tax_total;
	public $ship_total;
	public $account;
	public $total;

	public $app;

	public function __construct() {

	}

	public function save() {
		$this->table->save($this);
		$this->app->session->set('order',$this,'checkout');

	}

	public function __toString () {
		$result = $this->app->parameter->create();
		$result->loadObject($this);
		return (string) $result;
	}

	public function getOrderDate() {
		$date = $this->app->date->create($this->created);
		return $date->format('m/d/Y g:i a');
	}

	public function getItemPrice($sku) {
		if(!$item = $this->elements->get('items.'.$sku)) {
			$item = $this->app->cart->create()->get($sku);
		}
		$discount = $this->app->account->get($this->account)->elements->get('pricing.discount', 0);
		return $item->total - ($item->total*$discount);
	}

	public function getSubtotal() {
		if(!$items = $this->elements->get('items.')) {
			$items = $this->app->cart->create()->getAllItems();
		}
		$this->subtotal = 0;
		foreach($items as $item) {
			$this->subtotal += $this->getItemPrice($item->sku);
		}

		return $this->subtotal;
	}

	public function getTaxTotal() {
		if($this->taxTotal += ($item->taxable ? ($item->total*$this->taxRate) : 0);)
	}
	public function getTotal() {
		return $this->getSubtotal() + $this->tax_total + $this->ship_total;
	}

	public function calculateCommissions() {
		$application = $this->app->zoo->getApplication();
		$application->getCategoryTree();
		$items = $this->elements->get('items.');
		$account = $this->app->account->get($this->account);
		$acct_oems = $account->getOEMs();
		foreach($items as $item) {
			$_item = $this->app->table->item->get($item->id);
			$oems = $_item->getRelatedCategoryIds();
			foreach($oems as $oem) {
				if(in_array($oem, $acct_oems)) {
					$_oem = $this->app->account->get($oem);
					$order->elements->set('commissions.accounts.'.$_oem->id, $this->getItemPrice($item->sku)*$_oem->elements->get('commission'));
				}
			}
			
		}
	}

	public function isTaxable() {

        $state = $this->elements->get('billing.state');
        $taxable = false;
        $taxable_states = array('SC');
        if ($state) {
            $taxable = (!in_array($state,$taxable_states) && !$this->elements->get('shipping_method'));
        }

        if($account = $this->app->account->get($this->account)) {
            $taxable = $account->isTaxable();
        }

        return $taxable;
    }

}