<?php
namespace Boot;

use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Config\Config;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Session\Storage\SessionArrayStorage;

class Bootstrap
{
    private static $app = NULL;

    /**
     * 配置entityManager的事件映射对象，因为addEventListener不能识别config.php配置的字符串，因此设置此映射数组
     *
     * @var \Doctrine\ORM\Events $eventTypeMapping
     */
    private static $eventTypeMapping = array(
        "Events::prePersist" => \Doctrine\ORM\Events::prePersist,
        "Events::preFlush" => \Doctrine\ORM\Events::preFlush,
        "Events::preUpdate" => \Doctrine\ORM\Events::preUpdate,
        "Events::preRemove" => \Doctrine\ORM\Events::preRemove,
    );

    /**
     * 缓存的类型
     */
    const REDIS = "redis";
    const MEMCACHE = "memcache";

    /**
     * 引导应用
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function start()
    {
        try {
            $slim_config = self::getConfig('slim') ? self::getConfig('slim')->toArray() : [];
            self::$app = new \Slim\App($slim_config);
            self::initContainer();
            self::dealRoute();
            //register_shutdown_function('fatal_handler');
            self::$app->run();
        } catch (\Exception $e) {

        }
        echo convert(memory_get_usage(true));
        echo convert(memory_get_peak_usage(true));
    }

    /**
     * 引导控制台的引用，包括单元测试及其他的控制台程序(定时任务等...)
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function startConsole()
    {
        $slim_config = self::getConfig('slim') ? self::getConfig('slim')->toArray() : [];
        self::$app = new \Slim\App($slim_config);
        self::initContainer();
    }

    /**
     * 初始化依赖管理器
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function initContainer()
    {
        $container = self::$app->getContainer();
        $container['errorHandler'] = function ($c) {
            return function ($request, $response, $exception) use ($c) {
                /*return $c['response']->withStatus(500)
                    ->withHeader('Content-Type', 'text/html')
                    ->write('Something went wrong!');*/
                // return self::$app->getContainer()->get('view')->render($response, '/error.twig', []);
                print_r((string)$exception);
            };
        };
        $container['notFoundHandler'] = function ($c) {
            return function ($request, $response) use ($c) {
                /*return $c['response']
                    ->withStatus(404)
                    ->withHeader('Content-Type', 'text/html')
                    ->write('Page not found');*/
                return self::$app->getContainer()->get('view')->render($response, '/404.twig', []);
            };
        };
        $container['notAllowedHandler'] = function ($c) {
            return function ($request, $response, $methods) use ($c) {
                return $c['response']
                    ->withStatus(405)
                    ->withHeader('Allow', implode(', ', $methods))
                    ->withHeader('Content-type', 'text/html')
                    ->write('Method must be one of: ' . implode(', ', $methods));
            };
        };
        $container['view'] = function ($container) {
            $twig_config = self::getConfig('twig') ? self::getConfig('twig')->toArray() : [];
            $view = new Twig(APP_PATH . 'templates', self::getConfig('twig')->toArray());
            $view->addExtension(new TwigExtension($container['router'], $container['request']->getUri()));
            return $view;
        };
        /*Doctrine2 Memcache Driver*/
        $container["memcacheCacheDriver"] = function ($container) {
            $memcache = self::getCacheInstance(self::MEMCACHE, 'server1');
            $memcacheCacheDriver = new MemcacheCache();
            $memcacheCacheDriver->setNamespace("memcacheCacheDriver_namespace");
            $memcacheCacheDriver->setMemcache($memcache);
            return $memcacheCacheDriver;
        };
        /*Doctrine2 Redis Driver*/
        $container["redisCacheDriver"] = function ($container) {
            $redisCacheDriver = new RedisCache();
            $redis = self::getCacheInstance(self::REDIS, 'server1');
            //设置缓存的命名空间
            $redisCacheDriver->setNamespace('redisCacheDriver_namespace');
            $redisCacheDriver->setRedis($redis);
            return $redisCacheDriver;
        };
        /*ZendFrameWork Redis Object*/
        $container["redisCache"] = function ($container) {
            $redisConfig = self::getConfig("cache");
            $redis = NULL;
            if ($redisConfig->redis) {
                $redis = new \Zend\Cache\Storage\Adapter\Redis();
                //设置缓存的命名空间
                $redis->getOptions()->getResourceManager()->setResource('default', self::getCacheInstance(self::REDIS, 'server1'));
                $redis->getOptions()->setNamespace('redisCache_namespace');
            }
            return $redis;
        };
        /*ZendFrameWork FileSystemCache*/
        $container["fileSystemCache"] = function ($container) {
            $fileSystem = new Filesystem(array(
                "cache_dir" => APP_PATH . "/cache"
            ));
            return $fileSystem;
        };
        /*SessionManager Object*/
        $container['sessionManager'] = function ($container) {
            $config = new SessionConfig();
            $config->setOptions(self::getConfig("session")['manager']);
            $sessionManager = new SessionManager($config);
            $sessionManager->setStorage(new SessionArrayStorage());
            $sessionManager->start();
            return $sessionManager;
        };
        /*SessionManager Container Object*/
        $container["sessionContainer"] = function ($container) {
            $sessionManager = self::getContainer("sessionManager");
            Container::setDefaultManager($sessionManager);
            $container = new Container(self::getConfig("session")['container']['namespace']);
            return $container;
        };

        /*Event Manager Object*/
        $container["eventManager"] = function ($container) {
            return new EventManager();
        };
        /*Zend ServiceManager*/
        $container['serviceManager'] = function ($container) {
            $serviceManager = new ServiceManager();
            return $serviceManager;
        };
    }

    /**
     * 根据不同的数据库链接类型，实例化不同的数据库链接对象
     * @param $type
     * $type == entityManager的实例可以支持事务
     * $type == driverManager支持分库分表
     * @param $dbName string
     * @throws \Doctrine\ORM\ORMException
     * @return array
     */
    private static function databaseConnection($type, $dbName)
    {
        $dbConfig = self::getConfig('db')[APPLICATION_ENV];
        $db = NULL;
        if (isset($dbConfig[$dbName]) && $dbConfig[$dbName]) {
            $config = $dbConfig->$dbName ? $dbConfig->$dbName->toArray() : [];
            $useSimpleAnnotationReader = $config['useSimpleAnnotationReader'];
            unset($config['useSimpleAnnotationReader']);
            if ($useSimpleAnnotationReader) {
                $configuration = Setup::createConfiguration(APPLICATION_ENV == 'development');
                $annotationDriver = new AnnotationDriver(new AnnotationReader(), DATA_PATH . "/models/Entity");
                AnnotationRegistry::registerLoader("class_exists");
                $configuration->setMetadataDriverImpl($annotationDriver);
            } else {
                $configuration = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(
                    DATA_PATH . '/models/Entity/',
                ), APPLICATION_ENV == 'development', DATA_PATH . '/models/Proxies/', self::getContainer("memcacheCacheDriver"), $useSimpleAnnotationReader);
                /*  $configuration = \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(array(
                    APP_PATH . "/data/Yaml/"
                    ), APPLICATION_ENV == 'development', APP_PATH . '/data/Proxies/', self::$pimpleContainer["memcacheCacheDriver"]), */
            }
            //设置缓存组件
            $configuration->setQueryCacheImpl(self::getContainer('redisCacheDriver'));
            $configuration->setResultCacheImpl(self::getContainer('redisCacheDriver'));
            if ($type == "entityManager") {
                $db = \Doctrine\ORM\EntityManager::create($config
                    , $configuration, self::getContainer("eventManager"));
            } else if ($type == "driverManager") {
                $db = DriverManager::getConnection($config
                    , $configuration, self::getContainer("eventManager"));
            }
        }
        if (!self::getContainer("dataBase" . $type . $dbName)) {
            $container = self::$app->getContainer();
            $container["dataBase" . $type . $dbName] = $db;
        }
        return $db;
    }

    /**
     * 获取指定键的配置文件
     *
     * @author macro chen <macro_fengye@163.com>
     * @params string $first_key
     * @params string $second_key
     * @return mixed
     */
    public static function getConfig($key)
    {
        /*App Config*/
        $config_file = require APP_PATH . '/config/config.php';
        $config = new Config($config_file);
        if (!$config->$key) {
            echo "{$key}不存在！";
            return NULL;
        }
        return $config->$key;
    }

    /**
     * 获取APP
     * @author macro chen <macro_fengye@163.com>
     * @return \Slim\App
     */
    public static function getApp()
    {
        return self::$app;
    }

    /**
     * 处理动态路由
     */
    private static function dealRoute()
    {
        $path_info = self::$app->getContainer()->get('request')->getUri()->getPath();
        $path_arr = explode("/", ltrim($path_info, '/'));
        $controller = (isset($path_arr[0]) && !empty($path_arr[0])) ? $path_arr[0] : "home";
        $action = (isset($path_arr[1]) && !empty($path_arr[1])) ? $path_arr[1] : "index";
        $route_name = $controller . '.' . $action;
        self::getContainer('sessionContainer')->current_path_arr = $path_arr;
        $isDynamicAddRoute = true;
        if (!method_exists(APP_NAME . "\\controller\\" . ucfirst($controller), $action)) {
            return;
        }
        if (file_exists(APP_PATH . '/routes/' . $path_arr[0] . '_route.php')) {
            require_once APP_PATH . '/routes/' . $path_arr[0] . '_route.php';
            try {
                if (self::$app->getContainer()->get('router')->getNamedRoute($route_name)) {
                    $isDynamicAddRoute = false;
                } else {
                    $isDynamicAddRoute = true;
                }
            } catch (\RuntimeException $e) {
                $isDynamicAddRoute = true;
            };
        }
        if ($isDynamicAddRoute) {
            $route = APP_NAME . "\\controller\\" . ucfirst($controller) . ":" . $action;
            $pattern = "/" . $controller . '/' . $action . '[/{param:.*}]';
            self::$app->map(["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS"], $pattern, $route)->setName($route_name);
            if (isset(self::getConfig('customer')['is_check_login']) && self::getConfig('customer')['is_check_login']) {
                self::$app->add('checkLogin');
            }
            if (isset(self::getConfig('customer')['is_check_permission']) && self::getConfig('customer')['is_check_permission']) {
                self::$app->add('checkPermission');
            }
        }
    }

    /**
     * 添加系统配置的事件（监听器，订阅器）
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function addSystemEvent()
    {
        if (self::getConfig("evm")) {
            self::addEvent(self::getConfig("evm"));
        }
    }

    /**
     * 添加自定义的事件（监听器，订阅器）
     *
     * @param array $evm
     * @author macro chen <macro_fengye@163.com>
     * @return mixed
     */

    private static function addCustomEvent(array $evm = array())
    {
        if ($evm) {
            return self::addEvent(self::getConfig("evm"));
        }
        return NULL;
    }

    /**
     * 添加事件到事件管理器
     *
     * @param $evm array 需要添加的事件
     * @author macro chen <macro_fengye@163.com>
     * @return  mixed
     */
    private static function addEvent(array $evm = array())
    {
        if (isset($evmConfig['listener'])) {
            foreach ($evmConfig['listener'] as $key => $listener) {
                self::getContainer('eventManager')->addEventListener(array(
                    self::$eventTypeMapping[$key],
                ), new $listener());
            }
        }
        if (isset($evmConfig['subscriber'])) {
            foreach ($evmConfig['subscriber'] as $key => $subscriber) {
                self::getContainer('eventManager')->addEventSubscriber(new $subscriber());
            }
            return self::getContainer('eventManager');
        }
    }

    /**
     * 获取指定数据库实例的事件组件
     * @author macro chen <macro_fengye@163.com>
     * @param $type
     * $type == entityManager的实例可以支持事务
     * $type == driverManager支持分库分表
     * @param string $dbName
     * @param string $dbName
     * @return \Doctrine\Common\EventManager
     */
    public static function getDbInstanceEvm($type, $dbName)
    {
        if (self::getContainer("dataBase" . $type . $dbName)) {
            $db = self::getContainer("dataBase" . $type . $dbName);
        } else {
            $db = self::databaseConnection($type, $dbName);
        }
        return $db->getEventManager();
    }

    /**
     * 获取数据库的实例
     * @author macro chen <macro_fengye@163.com>
     * @param $type
     * $type == entityManager的实例可以支持事务
     * $type == driverManager支持分库分表
     * @param string $dbName
     * @return \Doctrine\Common\EventManager
     */
    public static function getDbInstance($type, $dbName)
    {
        if (self::getContainer("dataBase" . $type . $dbName)) {
            $db = self::getContainer("dataBase" . $type . $dbName);
        } else {
            $db = self::databaseConnection($type, $dbName);
        }
        return $db;
    }

    /**
     * 获取缓存的实例
     * @author macro chen <macro_fengye@163.com>
     * @param $type 缓存的类型
     * @param string $server_name 服务器的名字
     * @param bool $lookup 是否继续寻找其他的服务器是否可以链接
     * @return mixed
     */
    public static function getCacheInstance($type, $server_name, $lookup = true)
    {
        $config = self::getConfig('cache');
        if ($config) {
            if ($type == self::REDIS) {
                $redis = new \Redis();
                $is_conn = $redis->connect($config->$type->$server_name->host, $config->$type->$server_name->port, $config->$type->$server_name->timeout);
                if (!$is_conn && $lookup) {
                    foreach ($config->$type as $key => $value) {
                        if ($key != $server_name) {
                            $is_conn = $redis->connect($value->host, $value->port, $value->timeout);
                            if ($is_conn) {
                                break;
                            }
                        }
                    }
                }
                return $is_conn ? $redis : NULL;
            } elseif ($type == self::MEMCACHE && $lookup) {
                $memcache = new \Memcache();
                $is_conn = $memcache->connect($config->$type->$server_name->host, $config->$type->$server_name->port, $config->$type->$server_name->timeout);
                if (!$is_conn) {
                    foreach ($config->$type as $key => $value) {
                        if ($key != $server_name) {
                            $is_conn = $memcache->connect($value->host, $value->port, $value->timeout);
                            if ($is_conn) {
                                break;
                            }
                        }
                    }
                }
                return $is_conn ? $memcache : NULL;
            }
        }
    }

    /**
     * 获取指定组件名字的对象
     *
     * @param $componentName
     * @return mixed
     */
    public static function getContainer($componentName)
    {
        if (self::getApp()->getContainer()->offsetExists($componentName)) {
            return self::getApp()->getContainer()->get($componentName);
        }
        return null;
    }
}