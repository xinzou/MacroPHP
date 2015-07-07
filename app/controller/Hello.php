<?php
namespace controller;

use SlimController\SlimController;
use Guzzle\Http\Client;

class Hello extends SlimController{
    public function indexAction(){
        $this->render('/home/index' , array('somevar'=>date('c')));
    }
    
    public function showAction(){
        $client = new Client();
        $response = $client->get("http://guzzlephp.org");
        print_r($response->getResponseBody());
        $this->render("/home/hello" , array('name'=>'Macro'));
    }
    
    public function adminAction(){
        print_r(get_class_methods($this->app->response));
        if($this->app->response->getStatus() == 404){
            check_login();
        }
    }
}