<?php
namespace Boot;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Respect\Validation\Validator;
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;

class Bootstrap
{
    /**
     * 依赖注入的容器
     *
     * @var \Pimple\Container $pimpleContainer
     */
    private static $pimpleContainer = NULL;

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
     * 初始化依赖管理器
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function initPimple()
    {
        $pimpleConfig = self::getConfig("pimpleConfig");
        self::$pimpleContainer = new \Pimple\Container();
        /*App*/
        /*self::$pimpleContainer["app"] = function ($pimpleConfig) {
            return new \Slim\Slim(self::getConfig('slim'));
        };*/
        /*Validate Object*/
        self::$pimpleContainer["v"] = function ($pimpleConfig) {
            return Validator::create();
        };
        /*Doctrine2 Memcache Driver*/
        self::$pimpleContainer["memcacheCacheDriver"] = function ($pimpleConfig) {
            $memcacheConfig = self::getConfig('cache')['memcache'];
            $memcache = new \Memcache();
            $memcache->connect($memcacheConfig['host'], $memcacheConfig['port']);
            $memcacheCacheDriver = new MemcacheCache();
            $memcacheCacheDriver->setMemcache($memcache);
            return $memcacheCacheDriver;
        };
        /*Doctrine2 Redis Driver*/
        self::$pimpleContainer["redisCacheDriver"] = function ($pimpleConfig) {
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
        self::$pimpleContainer["redisCache"] = function ($pimpleConfig) {
            $redis = new \Zend\Cache\Storage\Adapter\Redis(array(
                'server' => self::getConfig("cache")['redis'],
            ));
            //设置缓存的命名空间
            $redis->getOptions()->setNamespace("macrophp");
            return $redis;
        };
        /*ZendFrameWork FileSystemCache*/
        self::$pimpleContainer["fileSystemCache"] = function ($pimpleConfig) {
            $fileSystem = new Filesystem(array(
                "cache_dir" => APP_PATH . "/cache"
            ));
            return $fileSystem;
        };
        /*SessionManager Object*/
        self::$pimpleContainer['sessionManager'] = function ($pimpleConfig) {
            $config = new SessionConfig();
            $config->setOptions(self::getConfig("session")['manager']);
            $sessionManager = new SessionManager($config);
            $sessionManager->start();
            return $sessionManager;
        };
        /*SessionManager Container Object*/
        self::$pimpleContainer["sessionContainer"] = function ($pimpleConfig) {
            $sessionManager = self::getPimple("sessionManager");
            Container::setDefaultManager($sessionManager);
            $container = new Container(self::getConfig("session")['container']['namespace']);
            return $container;
        };

        /*App Config*/
        self::$pimpleContainer["APP_CONFIG"] = function ($pimpleConfig) {
            $config = require APP_PATH . '/config/config.php';
            return $config;
        };
        /*Event Manager Object*/
        self::$pimpleContainer["eventManager"] = function ($pimpleConfig) {
            return new EventManager();
        };
        /*Zend ServiceManager*/
        self::$pimpleContainer['serviceManager'] = function ($pimpleConfig) {
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
                    APP_PATH . '/data/Entity/',
                ), APPLICATION_ENV == 'development', APP_PATH . '/data/Proxies/', self::$pimpleContainer["memcacheCacheDriver"], $useSimpleAnnotationReader);
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
            self::$pimpleContainer["dataBase" . $type . $dbName] = $db;
        }
        return $db;
    }

    /**
     * 引导整个应用
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function start()
    {
        self::initPimple();
        self::$pimpleContainer["app"] = function ($c) {
            return new \Slim\Slim(self::getConfig('slim'));
        };
        $app = self::getPimple("app");
        $app->configureMode(APPLICATION_ENV, function () {
            error_reporting(-1);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        });
        // 初始化视图对象
        $view = $app->view();
        $view->parserOptions = self::getConfig('twig');
        $view->parserExtensions = array(
            new \Slim\Views\TwigExtension(),
        );
        // 注册slim.before的hook
        self::registerHook("slim.before", self::addSystemEvent(), 1);
        // 注册slim.before.dispatch的hook
        self::registerHook("slim.before.dispatch", self::dealRouter(), 1);
        // 注册slim.before.router的hook
        self::registerHook("slim.before.router", self::slimBeforeRouter(), 10);
        // 注册slim.before.dispatch的hook
        self::registerHook("slim.after.router", self::slimBeforeDispatch(), 10);
        // 注册slim.before.dispatch的hook
        self::registerHook("slim.stop", self::slimStop(), 10);
        // 处理500错误
        $app->error(function (\Exception $e) use ($app) {
            $app->render('error.html');
        });
        // 处理404
        $app->notFound(function () use ($app) {
            $pathInfo = self::getPimple("app")->request->getPathInfo();
            $pathArr = explode("/", trim($pathInfo));
            $controller = isset($pathArr[1]) ? $pathArr[1] : "home";
            $action = (isset($pathArr[2]) && !empty($pathArr[2])) ? $pathArr[2] : "index";
            $app->render('404.html', array("controller" => APP_NAME . "\\controller\\" . ucfirst($controller), "action" => $action));
            //避免路由两次
        });
        $app->run();
        if (self::getConfig('customer')['show_use_memory']) {
            echo convert(memory_get_usage(true));
            echo convert(memory_get_peak_usage(true));
        }
    }

    /**
     * 引导控制台的引用，包括单元测试及其他的控制台程序(定时任务等...)
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function startConsole()
    {
        self::initPimple();
    }

    /**
     * 处理slim.before.router事件的回调函数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function slimBeforeRouter()
    {
    }

    /**
     * 处理slim.before.dispatch事件的回调函数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function slimBeforeDispatch()
    {
    }

    /**
     * 处理slim.stop事件的回调函数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function slimStop()
    {
        echo "slim.stop";
    }

    /**
     * 配置Cookie中间件
     * 如果zendframework-session设置了使用Cookie(use_cookies : true)
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function cookieStart()
    {
        self::getPimple("app")->add(new \Slim\Middleware\SessionCookie(self::getConfig('cookies')));
    }

    /**
     * 配置并启动session管理器
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function sessionStart()
    {
        if (self::getConfig('session')['manager']['use_cookies'] && self::getConfig("customer")['use_seesioncookie_middleware']) {
            self::cookieStart();
        }
    }

    /**
     * 获取指定的模型实体(还未实现)
     * @param $model string
     * @author macro chen <macro_fengye@163.com>
     */
    public static function getModel($model)
    {
        $model_str = md5($model);
        if (!self::getPimple("app")->container->get($model_str)) {
            self::getPimple("app")->container->singleton($model_str, function () {
            });
        }
        return self::getPimple("app")->container->get($model_str);
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
        if (isset(self::$pimpleContainer["APP_CONFIG"][$key])) {
            return self::$pimpleContainer["APP_CONFIG"][$key];
        }
        return null;
    }

    /**
     * 注册Hook函数
     * @param $name string
     * @param $callable callback
     * @param $priority int
     * @author macro chen <macro_fengye@163.com>
     */
    protected static function registerHook($name, $callable, $priority)
    {
        self::getPimple("app")->hook($name, $callable, $priority);
    }

    /**
     * 路由配置
     * 则动态的添加路由，根据请求的URL来动态的添加路由表,
     *
     * @author macro chen <macro_fengye@163.com>
     */
    protected static function dealRouter()
    {
        $pathInfo = self::getPimple("app")->request->getPathInfo();
        $pathArr = explode("/", trim($pathInfo));
        $controller = isset($pathArr[1]) ? $pathArr[1] : "home";
        $action = (isset($pathArr[2]) && !empty($pathArr[2])) ? $pathArr[2] : "index";
        $route_name = $controller . '.' . $action;
        if (strcmp($pathInfo, "/") == 0) {
            $route_file = "home";
        } else {
            $route_file = $pathArr[1];
        }
        $isDynamicAddRoute = true;
        if (file_exists(APP_PATH . '/routes/' . $route_file . '_route.php')) {
            require_once APP_PATH . '/routes/' . $route_file . '_route.php';
            $namedRoutes = self::getPimple("app")->container->get("router")->getNamedRoutes();
            while ($namedRoutes->valid()) {
                $routeName = $namedRoutes->current()->getName();
                if (!strcmp(strtolower($action), $routeName)) {
                    $isDynamicAddRoute = false;
                    break;
                }
                $namedRoutes->next();
            }
        }
        if ($isDynamicAddRoute) {
            if (!self::getPimple("app")->container->get("router")->getNamedRoute($route_name)) {
                if (!method_exists(APP_NAME . "\\controller\\" . ucfirst($controller), $action)) {
                    return;
                }
                if (!self::getPimple("app")->container->get("router")->getNamedRoute($route_name)) {
                    $route = APP_NAME . "\\controller\\" . ucfirst($controller) . ":" . $action;
                    if (!isset($pathArr[2]) || empty($pathArr[2])) {
                        $url = isset($pathArr[2]) ? "/" . $pathArr[1] . "/" : "/" . $pathArr[1];
                    } else {
                        $url = "/" . $pathArr[1] . "/" . $pathArr[2] . (strrchr($pathInfo, "/") == "/" ? "/" : "");
                    }
                    self::getPimple("app")->map($url . "(/)(:param1)(/)(:param2)(/)(:param3)(/)(:param4)(/)(:other+)(/)", $route)
                        ->via("GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS")
                        ->name($route_name)
                        ->setMiddleware([
                            function () {
                                echo __FILE__;
                                /*if (!preg_match("/login/", self::getPimple("app")->request->getResourceUri())) {
                                    self::getPimple("app")->flash('error', 'Login required');
                                    self::getPimple("app")->redirect('/hello/login');
                                }*/
                            },
                            function () {
                                /*self::getPimple("app")->notFound(function(){
                                    self::getPimple("app")->render("404.html");
                                });*/
                            },
                        ]);
                }
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
        if (self::$pimpleContainer->offsetExists($componentName)) {
            return self::$pimpleContainer[$componentName];
        }
        return null;
    }
}