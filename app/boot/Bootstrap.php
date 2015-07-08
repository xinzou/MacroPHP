<?php
namespace boot;

use SlimController\Slim;
use SlimController\SlimController;

class Bootstrap
{

    private static $app = NULL;

    /**
     * 开始整个应用
     *
     * @author macro chen
     *        
     */
    public static function start()
    {
        $app = static::getApp();
        $app->configureMode(APPLICATION_ENV, function () {
            error_reporting(- 1);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
        });
        
        // Prepare view
        $view = $app->view();
        $view->parserOptions = static::getConfig('twig');
        $view->parserExtensions = array(
            new \Slim\Views\TwigExtension()
        );
        static::requireRouteFile();
        $app->run();
    }

    /**
     * 获取整个应用
     *
     * @author macro chen
     *        
     */
    public static function getApp()
    {
        if (NULL == static::$app) {
            static::$app = new Slim(static::getConfig('slim'));
        }
        static::setEntityManager(static::$app);
        return static::$app;
    }

    /**
     * 获取指定的模型实体
     * 
     * @author macro chen
     */
    public static function getModel($model)
    {
        $model_str = md5($model);
        if (! $app->container->get($model_str)) {
            $app->container->singleton($model_str, function () {
                
            });
        }
        return $app->container->get(md5($model));
    }

    /**
     * 获取指定键的配置文件
     *
     * @author macro chen
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
     * @author macro chen
     *        
     */
    private static function requireRouteFile()
    {
        $app = static::getApp();
        $path_info = $app->request->getPathInfo();
        if (strlen($path_info) == 1) {
            require APP_PATH . '/app/routes/home_route.php';
        }
        require APP_PATH. '/app/routes/' . explode("/", $path_info)[1] . '_route.php';
    }

    /**
     * 设置doctrine2的entityManager
     *
     * @author macro chen
     * @param SlimController\Slim $app            
     * @return \Doctrine\ORM\EntityManager
     *
     */
    private static function setEntityManager($app)
    {
        $config['db'] = static::getConfig('db');
        $app->container->singleton("entityManager", function () use($config) {
            return \Doctrine\ORM\EntityManager::create(array(
                'driver' => $config['db'][APPLICATION_ENV]['driver'],
                'host' => $config['db'][APPLICATION_ENV]['host'],
                'port' => $config['db'][APPLICATION_ENV]['port'],
                'user' => $config['db'][APPLICATION_ENV]['user'],
                'password' => $config['db'][APPLICATION_ENV]['password'],
                'dbname' => $config['db'][APPLICATION_ENV]['dbname']
            ),
                /*\Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
                 array(APP_PATH. '/app/data/Entity'),
                    APPLICATION_ENV == 'development',
                    APP_PATH . '/app/data/Proxies',
                    new \Doctrine\Common\Cache\ArrayCache
                )*/
                \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(array(
                APP_PATH . "/app/data/yaml/"
            ), APPLICATION_ENV == 'development', APP_PATH . '/app/data/Proxies', new \Doctrine\Common\Cache\ArrayCache()));
        });
    }
}