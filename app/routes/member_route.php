<?php
$app->addRoutes(array(
    '/' => "Home:index",
    "/hello/:name" => "Home:hello"
));