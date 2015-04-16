<?php
/**
 * 动态的添加应该包含的路由表
 * @param SlimController\Slim $app
 * @return string
 */
function requireRouteFile($app){
    $path_info = $app->request->getPathInfo();
    return explode("/", $path_info)[1];
}

function mw1() {
    echo "This is middleware!";
}
function mw2() {
    echo "This is middleware!";
}
