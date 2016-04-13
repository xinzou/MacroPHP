<?php
/**
 * 所有控制器必须集成该类
 *
 * @author macro chen <macro_fengye@163.com>
 */
namespace Controller;

use Boot\Bootstrap;

class Controller
{
    /**
     * 整个应用
     *
     * @author macro chen <macro_fengye@163.com>
     * @var Slim APP $app
     */
    protected $app;

    /**
     * 获取应用的SessionManager
     *
     * @author macro chen <macro_fengye@163.com>
     * @var SessionManager $sessionManager
     */
    protected $sessionManager;

    /**
     * 获取应用的SessionContainer
     *
     * @author macro chen <macro_fengye@163.com>
     * @var SessionContainer $sessionContainer
     */
    protected $sessionContainer;

    /**
     * 数据库链接类型
     * @author macro chen <macro_fengye@163.com>
     * @var SessionContainer $sessionContainer
     */
    const ENTITY = "entityManager";
    const DRIVER = "driverManager";

    /**
     * 控制器构造函数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public function __construct()
    {
        $this->app = Bootstrap::getApp();
        $this->sessionManager = Bootstrap::getPimple('sessionManager');
        $this->sessionContainer = Bootstrap::getPimple('sessionContainer');
        $this->initSession();
    }

    /**
     * 在开启SessionCookie中间件的情况下，需要调用此函数，以初始化Cookie
     *
     * @author macro chen <macro_fengye@163.com>
     */
    protected function initSession()
    {
        $this->sessionContainer->_MACROPHP = "macro_php";
    }

    /**
     * 获取指定组件名字的对象
     * @param $componentName
     * @return mixed
     */
    protected function getPimple($componentName)
    {
        return Bootstrap::getPimple($componentName);
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
    protected function getDbInstance($type, $dbName)
    {
        return Bootstrap::getDbInstance($type, $dbName);
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
    protected function getDbInstanceEvm($type, $dbName)
    {
        return Bootstrap::getDbInstanceEvm($type, $dbName);
    }

    /**
     * 模板渲染
     * @author macro chen <macro_fengye@163.com>
     * @param $response 响应的对象
     * @param $template 模板文件
     * @param $data 传递到模板的数据
     */
    protected function render($response, $template, $data)
    {
        return $this->app->getContainer()->get('view')->render($response, $template, $data);
    }
}

?>