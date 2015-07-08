<?php
use boot\Bootstrap;
define("APP_PATH", dirname(__DIR__));
require APP_PATH.'/vendor/autoload.php';
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
// Include our required UDFs.
require APP_PATH.'/app/lib/functions.php';
Bootstrap::start();