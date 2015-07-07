<?php
$config = array(
	'session' => array(
		'name' => 'UNTITLEDAPP'
	),
    'slim' => array(
        'mode' => APPLICATION_ENV,
        'templates.path' => APP_PATH . '/app/templates',
        'log.level' => Slim\Log::ERROR,
        'log.enabled' => true,
        'view' => new \Slim\Views\Twig(),
        'controller.class_prefix'    => '\\controller',
        'controller.method_suffix'   => 'Action',
        'controller.template_suffix' => 'twig',
        'debug' => true,
        "cookies.httponly"=>true,
        "slim.errors"=>APP_PATH."/app/log/error.log"
    ),

    'twig' => array(
        'charset' => 'utf-8',
        'cache' => APP_PATH . '/app/templates/cache',
        'auto_reload' => true,
        'strict_variables' => false,
        'autoescape' => true
    ),

    'cookies' => array(
        'expires' => '60 minutes',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'name' => 'untitledapp_session',
        'secret' => 'changethiskeytosomethingelseasap',
        'cipher' => MCRYPT_RIJNDAEL_256,
        'cipher_mode' => MCRYPT_MODE_CBC
    ),
    'db' => array(
    	'development' => array(
			'driver' => 'pdo_mysql',
			'host' => '127.0.0.1',
			'port' => '3306',
			'user' => 'root',
			'password' => 'root',
			'dbname' => 'sakila'
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
