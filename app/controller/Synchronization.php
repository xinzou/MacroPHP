<?php
namespace controller;

use SlimController\SlimController;
use Guzzle\Http\Client;
use Entity\Car;
use Entity\Brand;
use boot\Bootstrap;

class Synchronization extends SlimController
{

    public function syncAction()
    {
        $client = new Client();
        $requests = $client->get("http://local.erp.com/mobile.php/Test/test");
        $response = $client->send($requests);
        $data = $response->json();
        $entityManager = Bootstrap::getApp()->container->get("entityManager");
        $this->render("/home/hello", array(
            'name' => 'Macro'
        ));
    }
    
    public function addAction(){
        $brand = new Brand();
        $brand->setBrandCode("aaaa");
        $entityManager = $this->app->container->get('entityManager');
       $entityManager->persist($brand);;
       $entityManager->flush($brand);;
        print_r($this->app->request->params());
    }
}