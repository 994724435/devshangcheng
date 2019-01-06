<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class ProductController extends CommonController{

    // 详情
    public function detail(){
        $product =M('p_product');
        $result  = $product->where(array('id'=>$_GET['id']))->select();
        $banner_list  = M('p_product_banner')->where(array('proid'=>$_GET['id']))->select();
        if($_POST){
            $data['num'] =$_POST['number'];
            $data['prices'] =$result[0]['price'];
            $data['userid'] =session('uid');
            $data['productid'] =$_GET['id'];
            $data['productname'] =$result[0]['name'];
            $data['productmoney'] =$result[0]['daycome'];
            $data['states']       =0;
            $data['addtime']       =date('Y-m-d H:i:s',time());
            $data['orderid'] = date('YmdHis',time()).rand(1000,9999);
//            if(empty(session('uid')||empty($result[0]['name']))){
//                print_r("此产品不存在");die;
//            }
            $orderlog =M('p_orderlog');
            $res_order = $orderlog->add($data);
            if($res_order){
                echo "<script>";
                echo "window.location.href='".__ROOT__."/index.php/Home/Product/orderDetail/orderid/".$data['orderid']."';";
                echo "</script>";
                exit;
            }else{
                echo "<script>alert('下单失败');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Product/product';";
                echo "</script>";
                exit;
            }
        }
        $this->assign('res',$result[0]);
        $this->assign('banner',$banner_list);

        $this->display();
    }

    public function shopcart(){
        $this->display();
    }
    public function deleteorder(){
        $orderlog =M('p_orderlog');
        $result  = $orderlog->where(array('orderid'=>$_GET['orderid']))->select();
        if(!$result[0]||$result[0]['states']!=0){
            echo "<script>alert('订单不存在');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/financial';";
            echo "</script>";
            exit;
        }
        $orderlog->where(array('orderid'=>$_GET['orderid']))->delete();
        echo "<script>alert('删除成功');";
        echo "window.location.href='".__ROOT__."/index.php/Home/Index/financial';";
        echo "</script>";
        exit;
    }

}