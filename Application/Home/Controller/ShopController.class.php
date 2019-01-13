<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class ShopController extends CommonController{

    public function member(){
       $this->display();
    }

    public function saftystep(){
        $this->display();
    }

    public function shoplist(){
        $this->display();
    }

    public function shopapply(){
        if ($_POST){
            print_r($_FILES);die;
        }
        $this->display();
    }
}