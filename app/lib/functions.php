<?php
use Slim\Slim;

/**
 * @desc 常用函数的文件
 * @author macro chen  <macro_fengye@163.com>
 * @date 2015/07/07
 */

/**
 * 动态的添加应该包含的路由表
 * @param \Slim\Slim $app
 * @return string
 * {{{
 */
function requireRouteFile($app)
{
    $path_info = $app->request->getPathInfo();
    $file = explode("/", $path_info)[1];
    if (empty($file) && strcmp($path_info, "/") === 0) {
        $file = "home";
    }
    return $file;
}

//}}}

//以下为常用中间件{{{
function mw1()
{
    echo "This is middleware!<br/>";
}

function mw2()
{
    echo "This is middleware!<br/>";
}

//关于管理员的中间件
function check_login()
{
    echo "您还没登录，不是操作其他的数据...";
    return false;
}
//}}}

/**
 * 字节转换
 * @param $size
 * @return string
 */
function convert($size){
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
