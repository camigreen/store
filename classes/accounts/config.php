<?php 

$params['default'] = array(
);
$params['dealer'] = array(
	'poc' => array(
		'name' => '',
		'phone' => '',
		'email' => '',
		'fax' => ''
	),
	'terms' => '',
	'discount' => '',
	'oem' => array(),
	'billing' => array(),
	'shipping' => array()
);
$params['employee'] = array();
$params['customer'] = array();
$params['oem'] = array(
	'poc' => ''
);
$params['dealer']['poc'] = $this->app->data->create($params['dealer']['poc']);
?>