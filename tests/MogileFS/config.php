<?php
$adapters = array(
	'tracker' => array(
		'domain' => 's1domain',
		'tracker' => array('tcp://10.0.0.16:7001'),
		'noverify' => false,
		'pathcount' => 999,
		'request_timeout' => 1000,
	),
	'memcached' => array(
		'servers' => array(array('localhost', 11211, 1)),
		'expiration' => 0,
	),
	'mysql' => array(
		'domain' => 's1domain',
		'pdo_options' => 'host=localhost;port=3306;dbname=mogilefs',
		'username' => 'mogile',
		'password' => 'THISISASECRET',
	),
);

return $adapters;
