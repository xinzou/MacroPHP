<?php
namespace boot;

use SlimController\Slim;
use SlimController\SlimController;


class Bootstrap
{
    //@var Application $app
    private static $app = NULL;
    
    /**
     * 引导整个应用
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public static function start()
    {
        $app = self::getApp();
        self::setEntityManager();
        $app->configureMode(APPLICATION_ENV, function () {
            error_reporting(- 1);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        });
        
        // Prepare view
        $view = $app->view();
        $view->parserOptions = self::getConfig('twig');
        $view->parserExtensions = array(
            new \Slim\Views\TwigExtension()
        );
        //处理500错误
        $app->error(function (\Exception $e) use ($app) {
            $app->render('error.php');
        });
        //处理404
        $app->notFound(function () use ($app) {
            $app->render('404.html');
        });
        self::requireRouteFile();
        $app->run();
    }

    /**
     * 获取整个应用
     *
     * @author macro chen    <macro_fengye@163.com>
     */
    public static function getApp()
    {
        if (NULL == self::$app) {
            self::$app = new Slim(self::getConfig('slim'));
        }
        return self::$app;
    }
    
    /**
     * 引导单元测试
     * 
     * @author macro chen <macro_fengye@163.com>
     */
    public static function startUnit(){
        self::getApp();
        self::setEntityManager();
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
        if (! self :: $app->container->get($model_str)) {
            self :: $app->container->singleton($model_str, function () {
                
            });
        }
        return self :: $app->container->get($model_str);
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
        return $config[$key];
    }

    /**
     * 根据URI包含路由文件
     *
     * @author macro chen   <macro_fengye@163.com>
     */
    private static function requireRouteFile()
    {
        $app = self::getApp();
        $path_info = $app->request->getPathInfo();
        $file = "";
        if (strcmp($path_info, "/")==0) {
            $file = "home";
        }else{
            $file =  explode("/", $path_info)[1];
        }
        require APP_PATH. '/app/routes/' . $file . '_route.php';
    }
    
    /**
     * 获取doctrine2的entityManager
     * 
     * @author macro chen <macro_fengye@163.com>
     * @return \Doctrine\ORM\EntityManager
     */
    
    public static function getEntityManager(){
        $em = static::getApp()->container->get("entityManager");
        if(!$em){
            self::setEntityManager();
            $em = static::getApp()->container->get("entityManager");
        }
        return $em;
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
        self :: $app->container->singleton("entityManager", function () use($config) {
            return \Doctrine\ORM\EntityManager::create(array(
                'driver' => $config['db'][APPLICATION_ENV]['driver'],
                'host' => $config['db'][APPLICATION_ENV]['host'],
                'port' => $config['db'][APPLICATION_ENV]['port'],
                'user' => $config['db'][APPLICATION_ENV]['user'],
                'password' => $config['db'][APPLICATION_ENV]['password'],
                'dbname' => $config['db'][APPLICATION_ENV]['dbname']
            ),
                /* \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
                 array(APP_PATH. '/app/data/Entity/'),
                    APPLICATION_ENV == 'development',
                    APP_PATH . '/app/data/Proxies/',
                    new \Doctrine\Common\Cache\ArrayCache
                ) */
                \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(array(
                APP_PATH . "/app/data/Yaml/"
            ), APPLICATION_ENV == 'development', APP_PATH . '/app/data/Proxies/', new \Doctrine\Common\Cache\ArrayCache()));
        });
    }
}