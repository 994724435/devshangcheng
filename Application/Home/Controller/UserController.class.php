<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class UserController extends CommonController{

    public function member(){
       $this->display();
    }

    public function saftystep(){
        $this->display();
    }


}