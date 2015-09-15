<?php 

// Set Statuses for Accounts

$config['status'][1] = JText::_('ACCOUNT_STATUS_ACTIVE');
$config['status'][2] = JText::_('ACCOUNT_STATUS_INACTIVE');
$config['status'][3] = JText::_('ACCOUNT_STATUS_SUSPENDED');
$config['status'][4] = JText::_('ACCOUNT_STATUS_TRASHED');

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