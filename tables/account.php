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
class AccountTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, '#__zoo_account');
		
		$this->app->loader->register('Account','classes:/accounts/default.php');
	}

	public function get($key, $type = null, $new = false) {
		if (!is_null($type) && file_exists($this->app->path->path('classes:/accounts/'.$type.'.php'))) {
				$this->class = $type.'Account';
            	$this->app->loader->register($this->class, 'classes:/accounts/'.$type.'.php');
            	
        }
		return parent::get($key, $new);
	}

	protected function _initObject($object) {

		parent::_initObject($object);
	
		// add to cache
		$key_name = $this->key;

		if ($object->$key_name && !key_exists($object->$key_name, $this->_objects)) {
			$this->_objects[$object->$key_name] = $object;
		}

		// trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'account:init'));

		return $object;
	}

	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {

		$new = !(bool) $object->id;

		// first save to get id
		if (empty($object->id)) {
			parent::save($object);
		}

		$result = parent::save($object);

		// trigger save event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'account:saved', compact('new')));

		return $result;
	}
}

/*
	Class: ItemTableException
*/
class ItemTableException extends AppException {}