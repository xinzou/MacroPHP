<?php
namespace boot;

use Doctrine\Common\EventManager;
use Respect\Validation\Validator;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Slim\Slim;

class Bootstrap
{
    // @var Application $app
    private static $app = NULL;
    
    // @var EventManager $evm;
    private static $evm;
    
    // @var sessionManager
    private static $sessionManager = NULL;
    
    // @var sessionContainer
    private static $sessionContainer = NULL;

    /**
     * 配置entityManager的事件映射对象，因为addEventListener不能识别config.php配置的字符串，因此设置这个数组
     *
     * @var \Doctrine\ORM\Events $eventTypeMapping
     */
    private static $eventTypeMapping = array(
        "Events::prePersist" => \Doctrine\ORM\Events::prePersist,
        "Events::preFlush" => \Doctrine\ORM\Events::preFlush,
        "Events::preUpdate" => \Doctrine\ORM\Events::preUpdate,
        "Events::preRemove" => \Doctrine\ORM\Events::preRemove
    );

    /**
     * 引导整个应用
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function start()
    {
        $app = self::getApp();
        $app->configureMode(APPLICATION_ENV, function () {
            error_reporting(- 1);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        });
        
        // 初始化视图对象
        $view = $app->view();
        $view->parserOptions = self::getConfig('twig');
        $view->parserExtensions = array(
            new \Slim\Views\TwigExtension()
        );
        // 注册slim.before.router的hook
        self::registerHook("slim.before.router", self::slimBeforeRouter(), 10);
        // 注册slim.before.dispatch的hook
        self::registerHook("slim.after.router", self::slimBeforeDispatch(), 10);
        // 注册slim.before.dispatch的hook
        self::registerHook("slim.stop", self::slimStop(), 10);
        // 处理500错误
        $app->error(function (\Exception $e) use($app) {
            $app->render('error.php');
        });
        // 处理404
        $app->notFound(function () use($app) {
            $app->render('404.html');
        });
        self::setEntityManager();
        self::registerValidateComponent();
        self::sessionStart();
        self::requireRouteFile();
        self::dynamicAddRoter();
        $app->run();
    }

    /**
     * 处理slim.before.router事件的回调函数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function slimBeforeRouter()
    {}

    /**
     * 处理slim.before.dispatch事件的回调函数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function slimBeforeDispatch()
    {}

    /**
     * 处理slim.stop事件的回调函数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function slimStop()
    {
        echo "1111";
    }

    /**
     * 获取整个应用
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function getApp()
    {
        if (NULL == self::$app) {
            self::$app = new \Slim\Slim(self::getConfig('slim'));
        }
        return self::$app;
    }

    /**
     * 配置Cookie中间件
     * 如果zendframework-session设置了使用Cookie(use_cookies : true)
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function cookieStart()
    {
        self::$app->add(new \Slim\Middleware\SessionCookie(self::getConfig('cookies')));
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
        self::sessionManager();
        self::sessionContainer();
    }

    /**
     * 获取sessionManager
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function sessionManager()
    {
        $config = new SessionConfig();
        $config->setOptions(self::getConfig("session")['manager']);
        self::$app->container->singleton('sessionManager', function () use($config) {
            $sessionManager = new SessionManager($config);
            $sessionManager->start();
            return $sessionManager;
        });
    }

    /**
     * 获取SessionContainer
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function sessionContainer()
    {
        self::$app->container->singleton("sessionContainer", function () {
            $sessionManager = self::$app->container->get('sessionManager');
            $container = Container::setDefaultManager($sessionManager);
            $container = new Container(self::getConfig("session")['container']['namespace']);
            return $container;
        });
    }

    /**
     * 引导单元测试
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function startUnit()
    {
        self::getApp();
        self::setEntityManager();
        self::registerValidateComponent();
        return self::$app;
    }

    /**
     * 获取指定的模型实体
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function getModel($model)
    {
        $model_str = md5($model);
        if (! self::$app->container->get($model_str)) {
            self::$app->container->singleton($model_str, function () {});
        }
        return self::$app->container->get($model_str);
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
        $config = require APP_PATH . '/app/config/config.php';
        if (isset($config[$key])) {
            return $config[$key];
        }
        return null;
    }

    /**
     * 根据URI包含路由文件
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private static function requireRouteFile()
    {
        $app = self::$app;
        $path_info = $app->request->getPathInfo();
        $file = "";
        if (strcmp($path_info, "/") == 0) {
            $file = "home";
        } else {
            $file = explode("/", $path_info)[1];
        }
        require APP_PATH . '/app/routes/' . $file . '_route.php';
    }

    /**
     * 注册Hook函数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    protected static function registerHook($name, $callable, $priority)
    {
        self::$app->hook($name, $callable, $priority);
    }

    /**
     * 如果没有手动的配置路由信息
     * 则动态的添加路由，根据请求的URL来动态的添加路由表,
     *
     * @author macro chen <macro_fengye@163.com>
     */
    protected static function dynamicAddRoter()
    {
        $path_info = self::$app->request()->getPathInfo();
        $path_infos = explode("/", trim($path_info));
        $route_name = $path_infos[1] . '.' . $path_infos[2];
        if (! self::$app->router()->getNamedRoute($route_name)) {
            $route = "controller\\" . ucfirst($path_infos[1]) . ":" . $path_infos[2];
            self::$app->map("/" . $path_infos[1] . "/" . $path_infos[2] . "(/:param1)(/:param2)(/:param3)(/:param4)(/:other+)", $route)
                ->via("GET", "POST", "PUT")
                ->name($route_name)
                ->setMiddleware([
                function () {
                    if (! preg_match("/login/", self::$app->request->getResourceUri())) {
                        self::$app->flash('error', 'Login required');
                        self::$app->redirect('/hello/login');
                    }
                },
                function () {}
            ]);
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
        $em = self::$app()->container->get("entityManager");
        if (! $em) {
            self::setEntityManager();
            $em = self::$app->container->get("entityManager");
        }
        return $em;
    }

    /**
     * 创建事件管理器
     *
     * @author macro chen <macro_fengye@163.com>
     */
    protected static function createEventManager()
    {
        if (NULL == self::$evm) {
            self::$evm = new EventManager();
        }
        if (self::getConfig("evm")) {
            $evmConfig = self::getConfig("evm");
            foreach ($evmConfig['listener'] as $key => $listener) {
                self::$evm->addEventListener(array(
                    self::$eventTypeMapping[$key]
                ), new $listener());
            }
            foreach ($evmConfig['subscriber'] as $key => $subscriber) {
                self::$evm->addEventSubscriber(new $subscriber());
            }
        }
        return self::$evm;
    }

    /**
     * 注册验证组件
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function registerValidateComponent()
    {
        self::$app->container->singleton('v', function () {
            return Validator::create();
        });
    }

    /**
     * 获取事件组件
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function getEvm()
    {
        return self::getEntityManager()->getEventManager();
    }

    /**
     * 设置doctrine2的entityManager
     *
     * @author macro chen <macro_fengye@163.com>
     * @param SlimController\Slim $app            
     * @return \Doctrine\ORM\EntityManager
     */
    private static function setEntityManager()
    {
        $config['db'] = self::getConfig('db');
        self::$app->container->singleton("entityManager", function () use($config) {
            return \Doctrine\ORM\EntityManager::create(array(
                'driver' => $config['db'][APPLICATION_ENV]['driver'],
                'host' => $config['db'][APPLICATION_ENV]['host'],
                'port' => $config['db'][APPLICATION_ENV]['port'],
                'user' => $config['db'][APPLICATION_ENV]['user'],
                'password' => $config['db'][APPLICATION_ENV]['password'],
                'dbname' => $config['db'][APPLICATION_ENV]['dbname']
            ), \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array(
                APP_PATH . '/app/data/Entity/'
            ), APPLICATION_ENV == 'development', APP_PATH . '/app/data/Proxies/', new \Doctrine\Common\Cache\ArrayCache(), false),
               /*  \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(array(
                APP_PATH . "/app/data/Yaml/"
            ), APPLICATION_ENV == 'development', APP_PATH . '/app/data/Proxies/', new \Doctrine\Common\Cache\ArrayCache()), */
                self::createEventManager());
        });
    }
}