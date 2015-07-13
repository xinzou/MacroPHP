<?php
// 整个引用的配置
$config = array(
    // 自定义的配置(额外的配置)
    'customer' => array(
        'use_seesioncookie_middleware' => true
    ),
    
    // 应用的配置
    'slim' => array(
        'controller.param_prefix' => 'prefix',
        'mode' => APPLICATION_ENV,
        'templates.path' => APP_PATH . '/app/templates',
        'log.level' => Slim\Log::ERROR,
        'log.enabled' => true,
        'view' => new \Slim\Views\Twig(),
        'controller.class_prefix' => '\\controller',
        'controller.method_suffix' => 'Action',
        'controller.template_suffix' => 'twig',
        "routes.case_sensitive" => "true",
        'debug' => true,
        "cookies.httponly" => true,
        "slim.errors" => APP_PATH . "/app/log/error.log",
        'log.writer' => new \Slim\LogWriter(@fopen(APP_PATH . "/app/log/error.log", "w"))
    ),
    
    // 配置模板实例
    'twig' => array(
        'charset' => 'utf-8',
        'cache' => APP_PATH . '/app/templates/cache',
        'auto_reload' => true,
        'strict_variables' => false,
        'autoescape' => true
    ),
    
    // 配置事件监听器与事件订阅者
    'evm' => array(
        "listener" => array(
            "Events::prePersist" => 'listener\MyEventListener'
        ),
        'subscriber' => array(
            "" => "subscriber\\MyEventSubscriber"
        )
    ),
    
    // Cookie的配置
    'cookies' => array(
        'expires' => '60 minutes',
        'path' => '/',
        'domain' => null,
        // 'secure' => true,
        'httponly' => true,
        'name' => 'php_assemble',
        'secret' => 'changethiskeytosomethingelseasap',
        'cipher' => MCRYPT_RIJNDAEL_256,
        'cipher_mode' => MCRYPT_MODE_CBC
    ),
    
    // Session的配置
    'session' => array(
        'manager' => array(
            'remember_me_seconds' => 1200,
            'name' => 'php_assemble',
            // 'phpSaveHandler' => 'redis',
            // 'savePath' => 'tcp://127.0.0.1:6379?weight=1&timeout=1',
            'use_cookies' => true
        ),
        'container' => array(
            'namespace' => 'php_assemble'
        )
    ),
    
    // 数据库配置
    'db' => array(
        // 开发模式
        'development' => array(
            'driver' => 'pdo_mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'user' => 'root',
            'password' => 'root',
            'dbname' => 'sakila'
        ),
        // 生产模式
        'production' => array(
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'username',
            'password' => 'password',
            'dbname' => 'production_dbname'
        )
    ),
    
    // 登录的URL
    'login.url' => '/login',
    
    // 后台管理URL
    'secured.urls' => array(
        array(
            'path' => '/admin'
        ),
        array(
            'path' => '/admin/.+'
        )
    )
);

return $config;
