<?php
namespace controller;

use SlimController\SlimController;

class Home extends SlimController{
    public function indexAction(){
        $this->render('/home/index' , array('somevar'=>date('c')));
    }
}