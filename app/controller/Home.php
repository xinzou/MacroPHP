<?php
namespace controller;

class Home extends Controller
{

    public function index()
    {
        $this->sessionContainer->user = array("username"=>20 , "age"=>30);
        $this->render('/home/index.twig', array(
            'somevar' => date('c'),
        ));
    }

    public function hello()
    {
        $this->render("/home/hello.twig", array(
            'name' => 'Macro',
        ));
    }
}