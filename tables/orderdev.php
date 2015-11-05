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
class OrderDevTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, '#__zoo_orderdev');
	}


	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {
		$tzoffset = $this->app->date->getOffset();
		$object->created = $this->app->date->create($object->created, $tzoffset)->toSQL();
		$object->modified = $this->app->date->create($object->modified, $tzoffset)->toSQL();
		$result = parent::save($object);
		return $result;
	}
}

/*
	Class: ItemTableException
*/
class OrderDevTableException extends AppException {}