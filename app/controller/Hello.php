<?php
namespace controller;

use SlimController\SlimController;
use Guzzle\Http\Client;

class Hello extends SlimController{
    public function indexAction(){
        $this->render('/home/index' , array('somevar'=>date('c')));
    }
    
    public function helloAction(){
        $client = new Client();
        $response = $client->get("http://guzzlephp.org");
        print_r($response->getResponseBody());
        $this->render("/home/hello" , array('name'=>'Macro'));
    }
}