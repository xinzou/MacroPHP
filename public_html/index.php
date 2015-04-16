<?php
ini_set("display_errors"  , 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';
use vendor\Authentication\Storage\EncryptedCookie;
use vendor\Middleware\Authentication;
use vendor\Middleware\HttpAuthentication;
use Slim\Extras\Views\Twig;
use Zend\Authentication\AuthenticationService;
use SlimController\Slim;

define("APP_PATH", dirname(__DIR__));

defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
$config = require __DIR__ . '/../app/config/config.php';
//Session::start($config['session']);

// Prepare app.
//$app = new Slim\Slim($config['slim']);
$app = new Slim($config['slim']);
$app->configureMode(APPLICATION_ENV, function () {
	error_reporting(-1);
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
		array(__DIR__ . '/../app/data/Entity'),
		APPLICATION_ENV == 'development',
		__DIR__ . '/../app/data/Proxies',
		new \Doctrine\Common\Cache\ArrayCache
	)*/
	\Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(array(__DIR__."/../app/data/yaml"), 
		APPLICATION_ENV == 'development',
		__DIR__ . '/../app/data/Proxies',
		new \Doctrine\Common\Cache\ArrayCache)
);

// Include our required UDFs.
require '../app/lib/functions.php';
// Add any middleware.
//$app->request->getPathInfo()
if($file = requireRouteFile($app)){
    echo  '../app/routes/'.$file.'_route.php';;
    require '../app/routes/'.$file.'_route.php';
}

//require '../app/routes/session.php';
//require '../app/routes/member_route.php';
//require '../app/routes/admin.php';

$app->run();
