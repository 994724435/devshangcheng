<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class UserController extends CommonController{
    public function ordercommit(){
        $orderid =$_GET['orderid'];
        $orderinfo = M('p_orderlog')->where(array('orderid'=>$orderid))->select();
        if(!$orderinfo[0]['id']){
            echo "<script>alert('订单ID不存在');window.location.href = '".__ROOT__."/index.php/Home/User/allorder';</script>";
            exit();
        }

        if($orderinfo[0]['state'] == 5){
            echo "<script>alert('订单已评价');window.location.href = '".__ROOT__."/index.php/Home/User/allorder';</script>";
            exit();
        }

        if($orderinfo[0]['userid'] != session('uid')){
            echo "<script>alert('不是自己的订单');window.location.href = '".__ROOT__."/index.php/Home/User/allorder';</script>";
            exit();
        }
        if($_POST){
            if(empty(trim($_POST['commits']))){
                echo "<script>alert('请输入评论');window.location.href = '".__ROOT__."/index.php/Home/User/ordercommit?orderid=".$_GET['orderid']."';</script>";
                exit();
            }
            $data['orderid'] = $orderid;
            $data['userid'] = $orderinfo[0]['userid'];
            $data['shopid'] = $orderinfo[0]['shopid'];
            $data['productid'] = $orderinfo[0]['productid'];
            $data['type'] = $_POST['radio'];
            $data['commits'] =$_POST['commits'];
            $data['addtime'] =  date('Y-m-d H:i:s');
            $data['updatetime'] =  date('Y-m-d H:i:s');
            M('p_ordercommits')->add($data);
            M('p_orderlog')->where(array('orderid'=>$orderid))->save(array('state'=>5));
            echo "<script>alert('评论成功');window.location.href = '".__ROOT__."/index.php/Home/User/allorder/state/5';</script>";
            exit();
        }
        $this->assign('orderlog',$orderinfo);

        $this->display();
    }

    public function orderdetail(){
        $orderid =$_GET['orderid'];
        $orderinfo = M('p_orderlog')->where(array('orderid'=>$orderid))->select();
        if(!$orderinfo[0]['id']){
            echo "<script>alert('订单ID不存在');window.location.href = '".__ROOT__."/index.php/Home/User/allorder';</script>";
            exit();
        }
        if($orderinfo[0]['userid'] != session('uid')){
            echo "<script>alert('不是自己的订单');window.location.href = '".__ROOT__."/index.php/Home/User/allorder';</script>";
            exit();
        }
        if($_POST['ispost']){
            M('p_orderlog')->where(array('orderid'=>$orderid))->save(array('state'=>4));
            echo "<script>alert('收货成功');window.location.href = '".__ROOT__."/index.php/Home/User/allorder/state/4';</script>";
            exit();
        }
        // addr
        $addr = M('p_addr')->where(array('id'=>$orderinfo[0]['addrid']))->find();
        // commit
        $commits = M('p_ordercommits')->where(array('orderid'=>$orderid))->find();
        $this->assign('addr',$addr);
        $this->assign('orderlog',$orderinfo);
        $this->assign('commits',$commits);
        $this->display();
    }

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

    /*
     * 订单状态
     */
    public function allorder(){
        $state=$_GET['state'];
        $where['userid'] =session('uid');

        if($state){
            if($state ==2 ){
                $where['state'] = array('in','2,3');
            }else{
                $where['state'] = $state;
            }

        }

        $orderlog = M('p_orderlog')->where($where)->order('id desc ')->select();
        $this->assign('state',$state);
        $this->assign('orderlog',$orderlog);

        $this->display();
    }

}