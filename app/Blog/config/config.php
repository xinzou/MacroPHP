<?php
// 整个引用的配置
$config = array(
    // 自定义的配置(额外的配置)
    'customer' => array(
        'use_seesioncookie_middleware' => true,
        'show_use_memory' => true,
    ),

    //Pimple 容器的配置
    'pimpleConfig'=>array(

    ),

    //缓存的配置
    'cache' => array(
        'memcache' => array(
            "host" => "127.0.0.1",
            "port" => 11211
        ),
        "redis" => array(
            "host" => "127.0.0.1",
            "port" => 6379
        ),
        "memcached" => array(),
    ),

    // 应用的配置
    'slim' => array(
        'controller.param_prefix' => 'prefix',
        'mode' => APPLICATION_ENV,
        'templates.path' => APP_PATH . '/templates',
        'log.level' => Slim\Log::ERROR,
        'log.enabled' => true,
        'view' => new \Slim\Views\Twig(),
        'controller.class_prefix' => '\\controller',
        'controller.method_suffix' => 'Action',
        'controller.template_suffix' => 'twig',
        "routes.case_sensitive" => "true",
        'debug' => true,
        "cookies.httponly" => true,
        "slim.errors" => APP_PATH . "/log/error.log",
        'log.writer' => new \Slim\LogWriter(@fopen(APP_PATH . "/log/error.log", "w")),
    ),

    // 配置模板实例
    'twig' => array(
        'charset' => 'utf-8',
        'cache' => APP_PATH . '/templates/cache',
        'auto_reload' => true,
        'strict_variables' => false,
        'autoescape' => true,
    ),

    // 配置事件监听器与事件订阅者
    'evm' => array(
        "listener" => array(
            "Events::prePersist" => 'Blog\listener\MyEventListener',
        ),
        'subscriber' => array(
            "" => "Blog\subscriber\MyEventSubscriber",
        ),
    ),

    // Cookie的配置
    'cookies' => array(
        'expires' => '60 minutes',
        'path' => '/',
        'domain' => null,
        // 'secure' => true,
        'httponly' => true,
        'name' => 'macro_php',
        'secret' => 'changethiskeytosomethingelseasap',
        'cipher' => MCRYPT_RIJNDAEL_256,
        'cipher_mode' => MCRYPT_MODE_CBC,
    ),

    // Session的配置
    'session' => array(
        'manager' => array(
            'remember_me_seconds' => 1200,
            'name' => 'macro_php',
            // 'phpSaveHandler' => 'redis',
            // 'savePath' => 'tcp://127.0.0.1:6379?weight=1&timeout=1',
            'use_cookies' => true,
        ),
        'container' => array(
            'namespace' => 'macro_php',
        ),
    ),

    // 数据库配置
    'db' => array(
        // 开发模式
        'development' => array(
            "db1" => array(
                'driver' => 'pdo_mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'user' => 'root',
                'password' => 'root',
                'dbname' => 'sakila',
                "charset"=>"UTF8",
                'sharding' => array(
                    'federationName' => 'my_database',
                    'distributionKey' => 'customer_id',
                )
            ),
            "db2" => array(
                'driver' => 'pdo_mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'user' => 'root',
                'password' => 'root',
                'dbname' => 'sakila',
                "charset"=>"UTF8",
                'sharding' => array(
                    'federationName' => 'my_database',
                    'distributionKey' => 'customer_id',
                )
            ),
            "db3" => array(
                'driver' => 'pdo_mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'user' => 'root',
                'password' => 'root',
                'dbname' => 'sakila',
                "charset"=>"UTF8",
                'sharding' => array(
                    'federationName' => 'my_database',
                    'distributionKey' => 'customer_id',
                )
            )),
        // 生产模式
        'production' => array(
            "db1" => array(
                'driver' => 'pdo_mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'username',
                'password' => 'password',
                'dbname' => 'production_dbname',
                "charset"=>"UTF8"
            ),
            "db2" => array(
                'driver' => 'pdo_mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'username',
                'password' => 'password',
                'dbname' => 'production_dbname',
                "charset"=>"UTF8"
            )
        ),
    ),

    // 登录的URL
    'login.url' => '/login',

    // 后台管理URL
    'secured.urls' => array(
        array(
            'path' => '/admin',
        ),
        array(
            'path' => '/admin/.+',
        ),
    ),
);

return $config;
