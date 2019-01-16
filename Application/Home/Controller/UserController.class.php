<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class UserController extends CommonController{

    public function member(){
       $userinfo = M('s_user')->where(array('id'=>session('uid')))->find(); 
       $shopcart =M('p_cart')->where(array('uid'=>session('uid')))->count();
 
       $useraccount = M('s_account')->where(array('userId'=>session('uid')))->find(); 
       $this->assign('userinfo',$userinfo);
       $this->assign('shopcart',$shopcart);
	   $this->assign('useraccount',$useraccount);

       $this->display();
    }

    public function saftystep(){
        $this->display();
    }

    public function allorder(){
        $this->display();
    }

}