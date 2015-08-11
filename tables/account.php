<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

/*
	Class: ItemTable
		The table class for items.
*/
class OrderTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, '#_zoo_order');
	}

	protected function _initObject($object) {

		parent::_initObject($object);

		// workaround for php bug, which calls constructor before filling values
		if (is_string($object->params) || is_null($object->params)) {
			// decorate data as object
			$object->params = $this->app->parameter->create($object->params);
		}

		// add to cache
		$key_name = $this->key;

		if ($object->$key_name && !key_exists($object->$key_name, $this->_objects)) {
			$this->_objects[$object->$key_name] = $object;
		}

		// trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'item:init'));

		return $object;
	}

	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {

		if (!($application = $object->getApplication())) {
			throw new ItemTableException('Invalid application id');
		}

		$new = !(bool) $object->id;

		// first save to get id
		if (empty($object->id)) {
			parent::save($object);
		}

		$result = parent::save($object);

		return $result;
	}
}

/*
	Class: ItemTableException
*/
class ItemTableException extends AppException {}