<?php
$app = \boot\Bootstrap::getApp();
$app->addRoutes(array(
    '/' => "Home:index",
    "/hello/:name" => "Home:hello"
));