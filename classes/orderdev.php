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
	public $total;

	public $app;

	public function __construct() {


	}

	public function save() {
		$table = $this->app->table->orderdev;

		return $table->save($this);

	}

	public function __toString () {
		$result = $this->app->parameter->create();
		$result->loadObject($this);
		return (string) $result;
	}

}