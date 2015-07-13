<?php
namespace controller;

class Home extends Controller
{

    public function index()
    {
        $this->render('/home/index.twig', array(
            'somevar' => date('c')
        ));
    }

    public function hello()
    {
        $this->render("/home/hello.twig", array(
            'name' => 'Macro'
        ));
    }
}