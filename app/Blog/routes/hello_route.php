<?php
$app = \Boot\Bootstrap::getPimple("app");

$app->map("/hello/show(/:name)", APP_NAME."\\controller\\Hello:show")
    ->via('GET', 'POST', 'PUT')
    ->name('show')->setMiddleware([function () {
        echo __FILE__;
    }]);

$app->map("/hello/test", APP_NAME."\\controller\\Hello:test")->via("GET")->name("test");

$app->map("/hello/index", APP_NAME."\\controller\\Hello:index")
    ->via("GET")
    ->setMiddleware([
    function () {
        echo "12345";
    },
    function () {
        echo "333333333";
    }
])->name("index");

$app->map("/hello/rbac", APP_NAME."\\controller\\Hello:rbac")
    ->via("POST")
    ->name("rbac"); 
