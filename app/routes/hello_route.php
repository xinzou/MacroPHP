<?php 
$app = \Slim\Slim::getInstance("default");

$app->addRoutes(array(
    
    '/hello/index' => array("Hello:index",function(){
        echo "aggagag";
    }),
    
    "/hello/show/:name"=> array('GET' => array("Hello:show",function () {
        echo "show action first middleware<br/>";
    } , function(){
         echo "show action second middleware<br/>";
         return ;
    },function(){
        echo "show action third middleware<br/>";
    }
    )),
    
    "/hello/addItem"=>array("Hello:addItem" , function(){
        echo "addItem....";
    }),
    
    '/hello/admin/:name' => array(
        "GET"=>array(
            "Hello:admin",
            function() use ($app){
                return false;
            },
        ),
    ),
    
),array('mw1' , 'mw2',function() use ($app){
        echo "This is middleware 3...<br/>";
}));