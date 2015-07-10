<?php
$app = \Slim\Slim::getInstance("default");

$app->map("/hello/show(/:name)", "controller\\Hello:show")
    ->via('GET', 'POST', 'PUT')
    ->name('foo');