<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class ShopController extends CommonController{
    // 下单详情
    public function orderdetail(){
        $orderid =$_GET['orderid'];
        $orderinfo = M('p_orderlog')->where(array('orderid'=>$orderid))->select();
        if(!$orderinfo[0]['id']){
            echo "<script>alert('订单ID不存在');window.location.href = '".__ROOT__."/index.php/Home/Shop/shoporder';</script>";
            exit();
        }

        $shopinfo = M('p_shop')->field('id')->where(array('userid'=>session('uid')))->find();
        if($orderinfo[0]['shopid'] != $shopinfo['id']){
            echo "<script>alert('非本店铺商品');window.location.href = '".__ROOT__."/index.php/Home/Shop/shoporder';</script>";
            exit();
        }
        if($_POST['expressorderid'] && $_POST['express']){
            $expressinfo = M('p_express')->where(array('code'=>$_POST['express']))->find();
            $data['expressname'] =$expressinfo['name'];
            $data['express'] =$_POST['express'];
            $data['state'] =3;
            $data['expressorderid'] =$_POST['expressorderid'];
            M('p_orderlog')->where(array('orderid'=>$orderid))->save($data);
            echo "<script>alert('发货成功');window.location.href = '".__ROOT__."/index.php/Home/Shop/shoporder/state/3';</script>";
            exit();
        }
        // commit
        $commits = M('p_ordercommits')->where(array('orderid'=>$orderid))->find();
        // addr
        $addr = M('p_addr')->where(array('id'=>$orderinfo[0]['addrid']))->find();
        //快递
        $kuaidi = M('p_express')->select();
        $this->assign('addr',$addr);
        $this->assign('kuaidi',$kuaidi);
        $this->assign('orderlog',$orderinfo);
        $this->assign('commits',$commits);
        $this->display();
    }

    /**
     * 店铺订单
     */
    public function shoporder(){
        $shopinfo = M('p_shop')->field('id')->where(array('userid'=>session('uid')))->find();

        $state=$_GET['state'];
        $where['shopid'] =$shopinfo['id'];

        if($state){
            $where['state'] = $state;
        }

        $orderlog = M('p_orderlog')->where($where)->order('id desc ')->select();
        $this->assign('state',$state);
        $this->assign('orderlog',$orderlog);

        $this->display();
    }

    public function shop(){
        $user_accout_info = M('s_account')->where(array('userId'=>session('uid')))->find();

        // 在售商品
        $shop =M('p_shop')->where(array('userid'=>session('uid')))->find();
        $sale_num =M('p_product')->where(array('shopid'=>$shop['id'],'state'=>1))->count();

        // 订单数量
        $order_num = M('p_orderlog')->where(array('shopid'=>$shop['id']))->count();
        $this->assign('order_num',$order_num);
        $this->assign('sale_num',$sale_num);
        $this->assign('user_accout_info',$user_accout_info);
        $this->display();
        }
    public function shoplist(){
        $where['status'] = 1;
        $res_all = M('p_shop')->where($where)->order('id desc')->select();
        $this->assign('res_all',$res_all);
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
                $data['pic']=$info['logo']['url'];
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

    public function shopvisit(){
        $shop_info  =M('p_shop')->where(array('id'=>I('shopid')))->find();
        // 在售商品
        $sale_list =M('p_product')->where(array('shopid'=>I('shopid'),'state'=>1))->select();

        // 订单数量
        $order_num = M('p_orderlog')->where(array('shopid'=>I('shopid')))->count();

        //成交好评
        $good_commits_num =  M('p_ordercommits')->where(array('shopid'=>I('shopid'),'type'=>1))->count();
        $this->assign('order_num',$order_num);
        $this->assign('sale_num',count($sale_list));
        $this->assign('good_commits_num',$good_commits_num);
        $this->assign('sale_list',$sale_list);
        $this->assign('shop_info',$shop_info);
        $this->display();
    }
    //店铺商品
    public function productlist(){
        $this->display();
    }
}