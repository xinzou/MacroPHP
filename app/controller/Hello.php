<?php
namespace controller;

use SlimController\SlimController;
use Guzzle\Http\Client;
use boot\Bootstrap;
use Entity\Actor;

class Hello extends SlimController{
    public function indexAction(){
        $this->render('/home/index' , array('somevar'=>date('c')));
    }
    
    public function showAction(){
        $client = new Client();
        $response = $client->get("http://guzzlephp.org");
        //print_r($response->getResponseBody());
        $this->render("/home/hello" , array('name'=>'Macro' , 'title'=>"这是第一个页面哦"));
    }
    
    public function addItemAction(){
        $em = Bootstrap::getEntityManager();
        $conn = $em->getConnection();
        $actor = new Actor();
        $metadata = $em->getClassMetadata(get_class($actor));
        $tableName = $metadata->getQuotedTableName($conn);
        echo $tableName;
        print_r($metadata->getTableName());
        $results = $conn->query("select * from " . $tableName);
        print_r($metadata->getAssociationMappings());
       /*  $actor->setFirstName("zhao");
        $actor->setLastName("haha");
        $em->persist($actor);
        $em->flush($actor );*/
    }
    
    public function adminAction(){
        if($this->app->response->getStatus() == 404){
            check_login();
        }
    }
}