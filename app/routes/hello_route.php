<?php
$app = \Slim\Slim::getInstance("default");

$app->map("/hello/show(/:name)", "controller\\Hello:show")
    ->via('GET', 'POST', 'PUT')
    ->name('foo');

$app->map("/hello/test", "controller\\Hello:test")->via("GET");

$app->map("/hello/index", "controller\\Hello:index")
    ->via("GET")
    ->setMiddleware([
    function () {
        echo "12345";
    },
    function () {
        echo "333333333";
    }
]);
