<?php
use boot\Bootstrap;
require_once "vendor/autoload.php";
define('APPLICATION_ENV', 'development');
define("APP_PATH", (__DIR__));
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(Bootstrap::getEntityManager());