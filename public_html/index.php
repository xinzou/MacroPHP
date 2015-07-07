<?php
define("APP_PATH", dirname(__DIR__));
require APP_PATH.'/vendor/autoload.php';
use SlimController\Slim;

defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
$config = require APP_PATH. '/app/config/config.php';
//Session::start($config['session']);

// Prepare app.
//$app = new Slim\Slim($config['slim']);
$app = new Slim($config['slim']);
$app->configureMode(APPLICATION_ENV, function () {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
});
// Prepare view
$view = $app->view();
$view->parserOptions = $config['twig'];
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

// Set up the Doctrine Entity Manager.
$entityManager = \Doctrine\ORM\EntityManager::create(
	array(
		'driver'   => $config['db'][APPLICATION_ENV]['driver'],
		'host'	   => $config['db'][APPLICATION_ENV]['host'],
		'port'	   => $config['db'][APPLICATION_ENV]['port'],
		'user'     => $config['db'][APPLICATION_ENV]['user'],
		'password' => $config['db'][APPLICATION_ENV]['password'],
		'dbname'   => $config['db'][APPLICATION_ENV]['dbname']
	),
	/*\Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
		array(APP_PATH . '/app/data/Entity'),
		APPLICATION_ENV == 'development',
		APP_PATH . '/app/data/Proxies',
		new \Doctrine\Common\Cache\ArrayCache
	)*/
	\Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(array(__DIR__."/../app/data/yaml"), 
		APPLICATION_ENV == 'development',
		APP_PATH . '/app/data/Proxies',
		new \Doctrine\Common\Cache\ArrayCache)
);

// Include our required UDFs.
require APP_PATH.'/app/lib/functions.php';
// Add any middleware.
//$app->request->getPathInfo()
$app->setName("default");
if($file = requireRouteFile($app)){
    require APP_PATH.'/app/routes/'.$file.'_route.php';
}

//require '../app/routes/session.php';
//require '../app/routes/member_route.php';
//require '../app/routes/admin.php';


$app->run();
