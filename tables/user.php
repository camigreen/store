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
class UserTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, '#__users');
	}

	/**
	 * Performs a query to the database and returns the representing list of objects
	 *
	 * This method will run the query and then build the list of objects that represent
	 * the records of the table. It will also init the objects with the basic properties
	 * like the reference to the global App object
	 *
	 * @param string $query The query to perform
	 *
	 * @return array The list of objects representing the records
	 */
	protected function _queryObjectList($query) {

		// query database
		$result = $this->database->query($query);
		// fetch objects and execute init callback
		$objects = array();
		while ($object = $this->database->fetchArray($result)) {
			$user = User::getInstance()
			$user->bind($object);
			$objects[$user->{$this->key}] = $this->_initObject($user);
		}

		$this->database->freeResult($result);
		return $objects;
	}
}

/*
	Class: ItemTableException
*/
class UserTableException extends AppException {}