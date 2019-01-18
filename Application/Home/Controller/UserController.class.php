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

    public function password(){
        if($_POST){
            if(empty(trim($_POST['pwd'])) ||  empty(trim($_POST['pwd2']))){
                echo "<script>alert('密码不能为空');</script>";
            }else{
              if(trim($_POST['pwd']) != trim($_POST['pwd2']) ){
                  echo "<script>alert('密码不相等');</script>";
              }
            }
            $muser = M('p_user');
            $userinfo = $muser->where(array('uid'=>session('uid')))->find();
            if($userinfo['id']){
                $muser->where(array('id'=>$userinfo['id']))->save(array('password'=>md5(trim($_POST['pwd']))  ));
            }else{
                $data['password'] = md5(trim($_POST['pwd']));
                $data['uid'] = session('uid');
                $data['addtime'] =  date('Y-m-d H:i:s');
                $data['updatetime'] =  date('Y-m-d H:i:s');
                $muser->add($data);
            }

            if($_GET['id']){
                echo "<script>alert('密码设置成功');window.location.href = '".__ROOT__."/index.php/Home/Product/order/id/".$_GET['id']."/current_num/".$_GET['current_num']."/addr_id/ ';</script>";
                exit();
            }else{
                echo "<script>alert('密码设置成功');window.location.href = '".__ROOT__."/index.php/Home/User/member';</script>";
                exit();
            }
        }
        $this->display();
    }

    public function allorder(){
        $this->display();
    }

}