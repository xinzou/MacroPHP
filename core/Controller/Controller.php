<?php
/**
 * 所有控制器必须集成该类
 *
 * @author macro chen <macro_fengye@163.com>
 */
namespace Controller;

use Boot\Base;
use Boot\Bootstrap;

class Controller extends Base
{
    /**
     * 控制器构造函数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * 初始化函数
     *
     * @author macro chen <macro_fengye@163.com>
     */
    protected function init()
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