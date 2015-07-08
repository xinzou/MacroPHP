<?php
define("APP_PATH", dirname(__DIR__));
require APP_PATH.'/vendor/autoload.php';
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
$config = require APP_PATH . '/app/config/config.php';
