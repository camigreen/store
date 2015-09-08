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
class UsersTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, '#__users');
	}
}

/*
	Class: ItemTableException
*/
class UsersTableException extends AppException {}