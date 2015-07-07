<?php 
$app->addRoutes(array(
    
    '/hello/index' => array("Hello:index",function(){
        echo "aggagag";
    }),
    
    "/hello/show/:name"=> array('GET' => array("Hello:show",function () {
        echo "12345";
    } , function(){
        echo "45656656";return ;
    })),
    
    '/hello/admin/:name' => array(
        "GET"=>array(
            "Hello:admin",
            function() use ($app){
               $app->status(404);
                return false;
            },
        ),
    ),
    
),array('mw1' , 'mw2'));