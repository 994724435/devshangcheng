<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class ProductController extends CommonController{

    // 详情
    public function detail(){
        $product =M('p_product');
        $result  = $product->where(array('id'=>I(id)))->select();
        $banner_list  = M('p_product_banner')->where(array('proid'=>I(id)))->select();
        $this->assign('res',$result[0]);
        $this->assign('banner',$banner_list);

        $this->display();
    }

    public function shopcart(){
        $id =I('id');
        $current_num=I('current_num');
        $cart =M('p_cart');
        if ($id){
            $arr_id =explode(',',$id);
            $arr_num =explode(',',$current_num);
            foreach ($arr_id as $k=>$v){
                $data= array();
                $data['productid'] =$v;
                $data['type'] =1;
                $data['uid'] =session('uid');
                $res_tem =$cart->where($data)->find();
                if (!$res_tem['id']){
                    $data['num'] =$arr_num[$k];
                    $data['addtime'] =date('Y-m-d H:i:s',time());
                    $cart->add($data);
                }
            }
        }
        $where=array('p_cart.uid'=>session('uid'),'p_cart.type'=>1);
        $result = $cart->field('p_cart.id,p_cart.productid,p_product.name,p_cart.num,p_product.pic,p_product.price')->join('p_product ON p_cart.productid=p_product.id')->where($where)->select();
        $this->assign('res',$result);
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