<?php

namespace Admin\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class ShopController extends CommonController {

    public function pass(){
        if(I('id')){
            $product =M('p_shop');
            $data['status'] =1;
            $product->where(array('id'=>I('id')))->save($data);
        }

        echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Shop/shoplist';</script>";
        exit();
    }

    public function shoplist(){
        $product =M('p_shop');

        $result = $product->alias('a')->join(' LEFT JOIN s_user b on a.userid=b.id')->order('a.id desc')->field('a.*,b.userAccount,b.realName')->select();
        $this->assign('res',$result);
        $this->display();
    }

    public function generate_code($length = 4) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $password;
    }

    public function editshop(){
         $product =M('p_shop');
        if($_POST){
            $pic='';
            // if($_FILES['thumb']['name']){   // 上传文件
            //     $thumb = imgFile();
            //     $info = $thumb['info'];
            //     if(!$info) {// 上传错误提示错误信息

            //     }else{// 上传成功
            //         $path = $info['thumb']['savepath'];
            //         $p = ltrim($path,'.');
            //         $img = $info['thumb']['savename'];
            //         $pic=$p.$img;
            //         $pic=__ROOT__.$pic;
            //     }
            // }
            $data['shopname'] =$_POST['shopname'];
            $data['ontime'] =$_POST['ontime'];
            $data['tel'] =$_POST['tel'];
            if($pic){
                $data['pic'] =$pic;
            }
            $data['addr'] =$_POST['addr'];
            $data['status'] =$_POST['status'];
            $data['intro'] =$_POST['intro'];

            $data['addtime'] =date('Y-m-d H:i:s',time());
            $result = $product->where(array('id'=>$_GET['id']))->save($data);
            if($result){
                echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Shop/shoplist';</script>";
            }else{
                echo "<script>alert('修改失败');window.location.href = '".__ROOT__."/index.php/Admin/Shop/shoplist';</script>";
            }

        }
        $result = $product->where(array('id'=>$_GET['id']))->select();
        $this->assign('res',$result[0]);
        $this->display();
    }

    public function deleteshop(){
        $product =M('p_shop');
        $result = $product->where(array('id'=>$_GET['id']))->select();
        if($result[0]){
            $state =$result[0]['status'];
        }else{
            echo "<script>alert('店铺不存在');window.location.href = '".__ROOT__."/index.php/Admin/Shop/shoplist';</script>";
        }
        if($state==1){
            $state=2;
        }else{
            $state=1;
        }
        $res= $product->where(array('id'=>$_GET['id']))->save(array('status'=>$state));
        if($res){
            echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Shop/shoplist';</script>";
        }else{
            echo "<script>alert('修改失败');window.location.href = '".__ROOT__."/index.php/Admin/Shop/shoplist';</script>";
        }
    }

    public function select(){
        $orderlog = M('orderlog');
        if($_GET['state']){
//            $map['name']=array('like','%'.$_GET['name'].'%');
            $map['state'] =$_GET['state'];
        }
        if($_GET['uid']){
            $map['userid'] =$_GET['uid'];
        }
        if($_GET['orderid']){
            $map['orderid'] =$_GET['orderid'];
        }
        $map['type'] =10;
        $users= $orderlog->where($map)->order('logid DESC')->select();

        $this->assign('users',$users);
        $this->display();
    }

}