<?php
/**
 * 所有控制都必须集成该类
 * 
 * @author macro chen <macro_fengye@163.com>
 */
namespace controller;

use boot\Bootstrap;

class Controller
{

    /**
     * 每个控制器可以自定义$hooks
     *
     * @author macro chen <macro_fengye@163.com>
     * @var array $hooks
     */
    protected $hooks;

    /**
     * 整个应用
     *
     * @author macro chen <macro_fengye@163.com>
     * @var Slim APP $app
     */
    protected $app;

    /**
     * 整个应用的事件管理器
     *
     * @author amcro chen <macro_fengye@163.com>
     * @var EventManager
     */
    protected $event;

    /**
     * 引用的请求参数
     *
     * @author amcro chen <macro_fengye@163.com>
     * @var array $params
     */
    protected $params;

    /**
     * 集成SlimController,并j添加自定义的逻辑
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public function __construct()
    {
        $this->app = Bootstrap::getApp();
        $this->analyzeRequestParams();
        $this->registerHooks();
    }

    /**
     * 注册每个控制自己配置的Hooks
     *
     * @author macro chen <macro_fengye@163.com>
     */
    protected function registerHooks()
    {}

    /**
     * 应用自定义的 Hooks
     *
     * @author macro chen <macro_fengye@163.com>
     * @param string $name            
     * @param mixed $data            
     */
    protected function applyHook($name, $data)
    {
        $this->app->applyHook($name, $data);
    }

    /**
     * 获取EventsManager
     *
     * @author macro chen <macro_fengye@163.com>
     */
    protected function getEventManager()
    {
        $this->event = Bootstrap::getEntityManager()->getEventManager();
        return $this->event;
    }

    /**
     * 分析请求的参数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private function analyzeRequestParams()
    {
        $method = $this->app->request()->getMethod();
        $method = strtolower($method);
        $method == "get" ? $this->params = $this->app->router()
            ->getCurrentRoute()
            ->getParams() : $this->params = $this->app->request()->$method();
        return $this->params;
    }

    /**
     * 根据key获取指定的参数
     *
     * @author amcro chen <macro_fengye@163.com>
     * @return mixed
     */
    protected function param($key)
    {
        return (isset($this->params[$key]) ? $this->params[$key] : "");
    }
}

?>