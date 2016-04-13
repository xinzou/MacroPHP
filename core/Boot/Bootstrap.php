<?php
namespace Boot;

use Respect\Validation\Validator;
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
     * 引导应用
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function start()
    {
        self::$app = new \Slim\App(self::getConfig('slim'));
        self::initContainer();
        self::dealRoute();
        // register_shutdown_function('fatal_handler');
        self::$app->run();
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
        $container['view'] = function ($container) {
            $view = new Twig(APP_PATH . 'templates', self::getConfig('twig'));
            $view->addExtension(new TwigExtension($container['router'], $container['request']->getUri()));
            return $view;
        };
        /*Validate Object*/
        $container["v"] = function ($container) {
            return Validator::create();
        };
        /*Doctrine2 Memcache Driver*/
        $container["memcacheCacheDriver"] = function ($container) {
            $memcacheConfig = self::getConfig('cache')['memcache'];
            $memcache = new \Memcache();
            $memcache->connect($memcacheConfig['host'], $memcacheConfig['port']);
            $memcacheCacheDriver = new MemcacheCache();
            $memcacheCacheDriver->setMemcache($memcache);
            return $memcacheCacheDriver;
        };
        /*Doctrine2 Redis Driver*/
        $container["redisCacheDriver"] = function ($container) {
            $redisConfig = self::getConfig("cache")['redis'];
            $redis = new \Redis();
            $redis->connect($redisConfig['host'], $redisConfig['port']);
            $redisCacheDriver = new RedisCache();
            //设置缓存的命名空间
            $redisCacheDriver->setNamespace("redisCache");
            $redisCacheDriver->setRedis($redis);
            return $redisCacheDriver;
        };
        /*ZendFrameWork Redis Object*/
        $container["redisCache"] = function ($container) {
            $redis = new \Zend\Cache\Storage\Adapter\Redis(array(
                'server' => self::getConfig("cache")['redis'],
            ));
            //设置缓存的命名空间
            $redis->getOptions()->setNamespace("macrophp");
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
            $sessionManager = self::getPimple("sessionManager");
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
            $config = $dbConfig[$dbName];
            $useSimpleAnnotationReader = $config['useSimpleAnnotationReader'];
            unset($config['useSimpleAnnotationReader']);
            if (!$useSimpleAnnotationReader) {
                $configuration = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(
                    DATA_PATH . '/data/Entity/',
                ), APPLICATION_ENV == 'development', DATA_PATH . '/data/Proxies/', self::$pimpleContainer["memcacheCacheDriver"], $useSimpleAnnotationReader);
                /*  $configuration = \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(array(
                    APP_PATH . "/data/Yaml/"
                    ), APPLICATION_ENV == 'development', APP_PATH . '/data/Proxies/', self::$pimpleContainer["memcacheCacheDriver"]), */
            } else {
                $isDevMode = false;
                $configuration = Setup::createConfiguration($isDevMode);
                $cacheDriver = new AnnotationDriver(new AnnotationReader(), APP_PATH . "/data/Entity");
                AnnotationRegistry::registerLoader("class_exists");
                $configuration->setMetadataDriverImpl($cacheDriver);
            }
            if ($type == "entityManager") {
                $db = \Doctrine\ORM\EntityManager::create($config
                    , $configuration, self::getPimple("eventManager"));
            } else if ($type == "driverManager") {
                $db = DriverManager::getConnection($config
                    , $configuration, self::getPimple("eventManager"));
            }
        }
        if (!self::getPimple("dataBase" . $type . $dbName)) {
            $container = self::$app->getContainer();
            $container["dataBase" . $type . $dbName] = $db;
        }
        return $db;
    }

    /**
     * 获取指定键的配置文件
     *
     * @author macro chen <macro_fengye@163.com>
     * @param string $key
     * @return []
     */
    private static function getConfig($key)
    {
        /*App Config*/
        $config = require APP_PATH . '/config/config.php';
        if (isset($config[$key])) {
            return $config[$key];
        }
        return null;
    }

    /**
     * 获取APP
     * @author macro chen <macro_fengye@163.com>
     * @return Slim\App
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
        $pathInfo = self::$app->getContainer()->get('request')->getUri()->getPath();
        $pathArr = explode("/", $pathInfo);
        $controller = (isset($pathArr[1]) && !empty($pathArr[1])) ? $pathArr[1] : "home";
        $action = (isset($pathArr[2]) && !empty($pathArr[2])) ? $pathArr[2] : "index";
        $route_name = $controller . '.' . $action;
        $isDynamicAddRoute = true;
        if (file_exists(APP_PATH . '/routes/' . $pathArr[1] . '_route.php')) {
            require_once APP_PATH . '/routes/' . $pathArr[1] . '_route.php';
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
            if (!method_exists(APP_NAME . "\\controller\\" . ucfirst($controller), $action)) {
                return;
            }
            $route = APP_NAME . "\\controller\\" . ucfirst($controller) . ":" . $action;
            self::$app->map(["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS"], $pathInfo, $route)->setName($route_name)->add('checkLogin');
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
                self::getPimple('eventManager')->addEventListener(array(
                    self::$eventTypeMapping[$key],
                ), new $listener());
            }
        }
        if (isset($evmConfig['subscriber'])) {
            foreach ($evmConfig['subscriber'] as $key => $subscriber) {
                self::getPimple('eventManager')->addEventSubscriber(new $subscriber());
            }
            return self::getPimple('eventManager');
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
        if (self::getPimple("dataBase" . $type . $dbName)) {
            $db = self::getPimple("dataBase" . $type . $dbName);
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
        if (self::getPimple("dataBase" . $type . $dbName)) {
            $db = self::getPimple("dataBase" . $type . $dbName);
        } else {
            $db = self::databaseConnection($type, $dbName);
        }
        return $db;
    }

    /**
     * 获取指定组件名字的对象
     *
     * @param $componentName
     * @return mixed
     */
    public static function getPimple($componentName)
    {
        if (self::$app->getContainer()->offsetExists($componentName)) {
            return self::$app->getContainer()->get($componentName);
        }
        return null;
    }
}