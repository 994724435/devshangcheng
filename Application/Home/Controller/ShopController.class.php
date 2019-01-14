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
        $shop =M('p_shop');
        $shop_app_info= $shop->where(array('userid'=>session('uid')))->find();
        $this->assign('shop_app_info',$shop_app_info);
        if ($_POST){
            if(!I('shopname')){
                echo "<script>alert('请填写店铺名称');</script>";
                $this->display();exit();
            }

            if(!I('addr')){
                echo "<script>alert('请填写店铺地址');</script>";
                $this->display();exit();
            }

            if(!I('tel')){
                echo "<script>alert('请填写店铺电话');</script>";
                $this->display();exit();
            }

            if(!I('ontime')){
                echo "<script>alert('请填写营业时间');</script>";
                $this->display();exit();
            }

            if(!I('intro')){
                echo "<script>alert('请填写店铺简介');</script>";
                $this->display();exit();
            }

            $data['userid']=session('uid');
            $data['shopname']=I('shopname');

            $data['online']=1;
            $data['ontime']=I('ontime');
            $data['tel']=I('tel');
            $data['addr']=I('addr');

            $data['status']=0;
            $data['intro']=I('intro');
            if($_FILES['zhizhao']['name'] && $_FILES['logo']['name']){   // 上传文件
                $setting = C('UPLOAD_FILE_QINIU');
                $Upload = new \Think\Upload($setting);
                $info = $Upload->upload($_FILES);
                $data['zhizhao']=$info['zhizhao']['url'];
                $data['logo']=$info['logo']['url'];
            }else{
                if(!$shop_app_info['id']){
                    echo "<script>alert('请上传照片');</script>";
                    $this->display();exit();
                }

            }

            if($shop_app_info['id']){
                $shop->where(array('id'=>$shop_app_info['id']))->save($data);
                echo "<script>alert('修改成功');";
            }else{
                $shop->add($data);
                echo "<script>alert('上传成功');";
            }

            echo "window.location.href='".__ROOT__."/index.php/Home/Shop/shopapply';";
            echo "</script>";
            exit;
        }


        $this->display();
    }
}