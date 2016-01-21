<?php
namespace boot;

use Doctrine\Common\EventManager;
use Respect\Validation\Validator;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container;
use Zend\Session\SessionManager;

class Bootstrap
{
    //@var PimpleContainer
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
     * @author macro chen <macro_fengye@163.com>
     */
    private static function initPimple()
    {
        self::$pimpleContainer = new \Pimple\Container();
        self::$pimpleContainer["app"] = function ($c) {
            return new \Slim\Slim(self::getConfig('slim'));
        };
        self::$pimpleContainer["v"] = function ($c) {
            return Validator::create();
        };
        self::$pimpleContainer['sessionManager'] = function ($c) {
            $config = new SessionConfig();
            $config->setOptions(self::getConfig("session")['manager']);
            $sessionManager = new SessionManager($config);
            $sessionManager->start();
            return $sessionManager;
        };
        self::$pimpleContainer["sessionContainer"] = function ($c) {
            $sessionManager = self::getPimple("sessionManager");
            Container::setDefaultManager($sessionManager);
            $container = new Container(self::getConfig("session")['container']['namespace']);
            return $container;
        };
        self::$pimpleContainer["entityManager"] = function ($c) {
            $config['db'] = self::getConfig('db');
            return \Doctrine\ORM\EntityManager::create(array(
                'driver' => $config['db'][APPLICATION_ENV]['driver'],
                'host' => $config['db'][APPLICATION_ENV]['host'],
                'port' => $config['db'][APPLICATION_ENV]['port'],
                'user' => $config['db'][APPLICATION_ENV]['user'],
                'password' => $config['db'][APPLICATION_ENV]['password'],
                'dbname' => $config['db'][APPLICATION_ENV]['dbname'],
            ), \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(
                APP_PATH . '/app/data/Entity/',
            ), APPLICATION_ENV == 'development', APP_PATH . '/app/data/Proxies/', new \Doctrine\Common\Cache\ArrayCache(), false),
                /*  \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(array(
                APP_PATH . "/app/data/Yaml/"
                ), APPLICATION_ENV == 'development', APP_PATH . '/app/data/Proxies/', new \Doctrine\Common\Cache\ArrayCache()), */
                /*self::createEventManager()*/
                self::getPimple("eventManager"));
        };
        self::$pimpleContainer["APP_CONFIG"] = function ($c) {
            $config = require APP_PATH . '/app/config/config.php';
            return $config;
        };
        self::$pimpleContainer["eventManager"] = function ($c) {
            return new EventManager();
        };
    }

    /**
     * 引导整个应用
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function start()
    {
        self::initPimple();
        $app = self::getApp();
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
            $app->render('error.php');
        });
        // 处理404
        $app->notFound(function () use ($app) {
            $app->render('404.html');
            //避免路由两次
        });
        $app->run();
    }

    /**
     * 引导单元测试
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function startUnit()
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
     * 获取整个应用
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function getApp()
    {
        return self::$pimpleContainer["app"];
    }

    /**
     * 配置Cookie中间件
     * 如果zendframework-session设置了使用Cookie(use_cookies : true)
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function cookieStart()
    {
        self::getApp()->add(new \Slim\Middleware\SessionCookie(self::getConfig('cookies')));
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
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function getModel($model)
    {
        $model_str = md5($model);
        if (!self::getApp()->container->get($model_str)) {
            self::getApp()->container->singleton($model_str, function () {
            });
        }
        return self::getApp()->container->get($model_str);
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
     *
     * @author macro chen <macro_fengye@163.com>
     */
    protected static function registerHook($name, $callable, $priority)
    {
        self::getApp()->hook($name, $callable, $priority);
    }

    /**
     * 路由配置
     * 则动态的添加路由，根据请求的URL来动态的添加路由表,
     *
     * @author macro chen <macro_fengye@163.com>
     */
    protected static function dealRouter()
    {
        $path_info = self::getApp()->request->getPathInfo();
        $path_infos = explode("/", trim($path_info));
        $path_infos[1] = empty($path_infos[1]) ? 'home' : $path_infos[1];
        $path_infos[2] = empty($path_infos[2]) ? 'index' : $path_infos[2];
        $route_name = $path_infos[1] . '.' . $path_infos[2];
        if (strcmp($path_info, "/") == 0) {
            $route_file = "home";
        } else {
            $route_file = $path_infos[1];
        }
        $isDynamicAddRoute = false;
        if (file_exists(APP_PATH . '/app/routes/' . $route_file . '_route.php')) {
            require_once APP_PATH . '/app/routes/' . $route_file . '_route.php';
            if (!(self::getApp()->container->get("router")->getNamedRoute($route_name))) {
                $isDynamicAddRoute = true;
            }
        }
        if ($isDynamicAddRoute) {
            if (!self::getApp()->container->get("router")->getNamedRoute($route_name)) {
                $route = "controller\\" . ucfirst($path_infos[1]) . ":" . $path_infos[2];
                self::getApp()->map("/" . $path_infos[1] . "/" . $path_infos[2] . "(/:param1)(/:param2)(/:param3)(/:param4)(/:other+)", $route)
                    ->via("GET", "POST", "PUT")
                    ->name($route_name)
                    ->setMiddleware([
                        function () {
                            echo __FILE__;
                            /*if (!preg_match("/login/", self::getApp()->request->getResourceUri())) {
                                self::getApp()->flash('error', 'Login required');
                                self::getApp()->redirect('/hello/login');
                            }*/
                        },
                        function () {
                            /*self::getApp()->notFound(function(){
                                self::getApp()->render("404.html");
                            });*/
                        },
                    ]);
            }
        }
    }

    /**
     * 获取doctrine2的entityManager
     *
     * @author macro chen <macro_fengye@163.com>
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEntityManager()
    {
        $em = self::$pimpleContainer["entityManager"];
        return $em;
    }

    /**
     * @添加系统配置的事件（监听器，订阅器）
     * @author macro chen <macro_fengye@163.com>
     */
    private static function addSystemEvent()
    {
        if (self::getConfig("evm")) {
            self::addEvent(self::getConfig("evm"));
        }
    }

    /**
     * @添加自定义的事件（监听器，订阅器）
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
     * @param $evm 需要添加的事件
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
     * 获取事件组件
     *
     * @author macro chen <macro_fengye@163.com>
     * @return \Doctrine\Common\EventManager
     */
    public static function getEvm()
    {
        return self::getEntityManager()->getEventManager();
    }

    /**
     * 获取指定组件名字的对象
     * @param $conponet_name
     * @return mixed
     */
    public static function getPimple($conponet_name)
    {
        return self::$pimpleContainer[$conponet_name];
    }
}