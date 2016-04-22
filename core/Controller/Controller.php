<?php
/**
 * 所有控制器必须集成该类
 *
 * @author macro chen <macro_fengye@163.com>
 */
namespace Controller;

use Boot\Bootstrap;
use Doctrine\DBAL\Sharding\PoolingShardManager;

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
     * 缓存的类型
     */
    const REDIS = "redis";
    const MEMCACHE = "memcache";

    /**
     * 控制器构造函数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public function __construct()
    {
        $this->app = Bootstrap::getApp();
        $this->sessionManager = Bootstrap::getContainer('sessionManager');
        $this->sessionContainer = Bootstrap::getContainer('sessionContainer');
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
    protected function getContainer($componentName)
    {
        return Bootstrap::getContainer($componentName);
    }

    /**
     * 获取数据库的实例
     * @author macro chen <macro_fengye@163.com>
     * @param $type
     * $type == entityManager的实例可以支持事务
     * $type == driverManager支持分库分表
     * @param string $dbName 数据库配置的键名
     * @return \Doctrine\Common\EventManager
     */
    protected function getDbInstance($type, $dbName)
    {
        return Bootstrap::getDbInstance($type, $dbName);
    }

    /**
     * 缓存对象的实例
     * @author macro chen <macro_fengye@163.com>
     * @param $type 缓存的类型
     * @param string $server_name 服务器的名字
     * @param bool $lookup 是否继续寻找其他的服务器是否可以链接
     * @return mixed
     */
    protected function getCacheInstance($type, $server_name, $lookup = true)
    {
        return Bootstrap::getCacheInstance($type, $server_name, $lookup);
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

    /**
     * 获取应用的配置
     * @author macro chen <macro_fengye@163.com>
     * @param $key 响应的对象
     * @return \Zend\Config\Config
     */
    protected function getConfig($key)
    {
        return Bootstrap::getConfig($key);
    }

    /**
     * @param string $type 数据库实体类型
     * @param string $server_name 服务器的名字
     * @param integer $shard_id 分库的ID
     * @return \Doctrine\DBAL\Sharding\PoolingShardManager $shardManager
     */
    protected function getShards($type, $server_name, $shard_id)
    {
        $em = $this->getDbInstance($type, $server_name);
        $qb = $em->createQueryBuilder();
        $conn = $qb->getConnection();
        $shardManager = new PoolingShardManager($conn);
        $shardManager->selectGlobal();
        $shardManager->selectShard(1);
        return $shardManager;
    }
}

?>