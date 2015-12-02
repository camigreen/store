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

	protected $_user;

	public function __construct() {

	}

	public function save() {
		$this->table->save($this);
		$this->app->session->set('order',$this,'checkout');

	}

	public function __toString () {
		$result = $this->app->parameter->create();
		$result->loadObject($this);
		$result->set('account', $this->account->id);
		return (string) $result;
	}

	public function getOrderDate() {
		$tzoffset   = $this->app->date->getOffset();
		$date = $this->app->date->create($this->created, $tzoffset);
		return $date->format('m/d/Y g:i a');
	}

	public function getItemPrice($sku) {
		if(!$item = $this->elements->get('items.'.$sku)) {
			$item = $this->app->cart->create()->get($sku);
			$item->getTotal();
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
			$this->subtotal += $item->getTotal('discount');
		}
		return $this->subtotal;
	}

	public function isProcessed() {
		return $this->id ? true : false;
	}

	public function getUser() {
		if($this->created_by) {
			$this->_user = $this->app->account->get($this->created_by);
		}
		if(empty($this->_user)) {
			$this->_user = $this->app->customer->getAccount();
			$this->created_by = $this->_user->id;
		}
		
		return $this->_user;
	}

	public function getAccount() {
		$this->account = $this->getUser()->getParentAccount();
		return $this->account;
	}

	public function getTaxTotal() {

		$taxtotal = 0;
		$taxrate = 0.07;

		$account = $this->getAccount();
		if($account->elements->get('pricing.tax_exempt', true)) {
			$this->tax_total = 0;
			return $this->tax_total;
		}

		if(!$items = $this->elements->get('items.')) {
			$items = $this->app->cart->create()->getAllItems();
		}

		foreach($items as $item) {
			$taxtotal += ($item->taxable ? ($this->getItemPrice($item->sku)*$taxrate) : 0);
		}
		
		$this->tax_total = $taxtotal;
		return $this->tax_total;
	}
	public function calculateTotals() {

		if(!$this->isProcessed()) {
			$this->getSubtotal();
			$this->getTaxTotal();
		}

		$this->total = $this->subtotal + $this->tax_total + $this->ship_total;
		$totals['subtotal'] = $this->subtotal;
		$totals['taxtotal'] = $this->tax_total;
		$totals['shiptotal'] = $this->ship_total;
		$totals['total'] = $this->total;

		return $totals;
	}
	public function calculateCommissions() {
		$application = $this->app->zoo->getApplication();
		$application->getCategoryTree();
		$items = $this->elements->get('items.');
		$account = $this->getAccount();
		$oems = $account->getAllOEMs();
		foreach($items as $item) {
			$_item = $this->app->table->item->get($item->id);
			$item_cat = $_item->getPrimaryCategory();
			foreach($oems as $oem) {
				if($item_cat->id == $oem->elements->get('category')) {
					$this->elements->set('commissions.accounts.'.$oem->id, $this->getItemPrice($item->sku)*$oem->elements->get('commission'));
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