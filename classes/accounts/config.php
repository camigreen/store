<?php 

// Set Statuses for Accounts

define('ACCOUNT_STATUS_ACTIVE', 'Active');
define('ACCOUNT_STATUS_INACTIVE', 'Inactive');
define('ACCOUNT_STATUS_SUSPENDED', 'Suspended');
define('ACCOUNT_STATUS_TRASHED', 'Trashed');

$config['status'][1] = ACCOUNT_STATUS_ACTIVE;
$config['status'][2] = ACCOUNT_STATUS_INACTIVE;
$config['status'][3] = ACCOUNT_STATUS_SUSPENDED;
$config['status'][4] = ACCOUNT_STATUS_TRASHED;

$config['types']['default'] = array();
$config['types']['dealer'] = array(
	'poc' => array(
		'name' => '',
		'phone' => '',
		'email' => '',
		'fax' => ''
	),
	'terms' => '',
	'discount' => '',
	'billing' => array(),
	'shipping' => array()
);
$config['types']['employee'] = array();
$config['types']['customer'] = array();
$config['types']['oem'] = array(
	'poc' => ''
);
?>