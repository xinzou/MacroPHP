<?php
$app = \boot\Bootstrap::getApp();
$app->addRoutes([
    "/sync/test" => [
        "Synchronization:sync"
    ],

    "/sync/add" => [
        "Synchronization:add",
        function () {
        },
    ],
]);