<?php
$config = array(
	'session' => array(
		'name' => 'UNTITLEDAPP'
	),
    'slim' => array(
        'mode' => APPLICATION_ENV,
        'templates.path' => __DIR__ . '/../templates',
        'log.level' => Slim\Log::DEBUG,
        'log.enabled' => true,
        'view' => new \Slim\Views\Twig()
    ),

    'twig' => array(
        'charset' => 'utf-8',
        'cache' => realpath(__DIR__ . '/../templates/cache'),
        'auto_reload' => true,
        'strict_variables' => false,
        'autoescape' => true
    ),

    'cookies' => array(
        'expires' => '60 minutes',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'httponly' => false,
        'name' => 'untitledapp_session',
        'secret' => 'changethiskeytosomethingelseasap',
        'cipher' => MCRYPT_RIJNDAEL_256,
        'cipher_mode' => MCRYPT_MODE_CBC
    ),
    'db' => array(
    	'development' => array(
			'driver' => 'pdo_mysql',
			'host' => 'localhost',
			'port' => '3306',
			'user' => 'root',
			'password' => 'root',
			'dbname' => 'test'
		),
	'production' => array(
		'driver' => 'pdo_mysql',
		'host' => 'localhost',
		'port' => '3306',
		'user' => 'username',
		'password' => 'password',
		'dbname' => 'production_dbname'
	)
	),
    'login.url' => '/login',
    'secured.urls' => array(
        array('path' => '/admin'),
        array('path' => '/admin/.+')
    ),
);
return $config;
