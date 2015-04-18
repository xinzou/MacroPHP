<?php
require __DIR__.'/../vendor/autoload.php';
use vendor\Authentication\Storage\EncryptedCookie;
use vendor\Middleware\Authentication;
use vendor\Middleware\HttpAuthentication;
use Slim\Extras\Views\Twig;
use Zend\Authentication\AuthenticationService;
use SlimController\Slim;
use Zend\Authentication\Storage\Session;

define("APP_PATH", dirname(__DIR__));

defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
$config = require __DIR__ . '/../app/config/config.php';

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
