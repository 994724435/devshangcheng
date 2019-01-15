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

                $data['num'] =$arr_num[$k];
                $data['addtime'] =date('Y-m-d H:i:s',time());
                if (!$res_tem['id']){
                    $cart->add($data);
                }else{
                    $cart->where(array('id'=>$res_tem['id']))->save($data);
                }
            }
        }
        $where=array('p_cart.uid'=>session('uid'),'p_cart.type'=>1);
        $result = $cart->field('p_cart.id,p_cart.productid,p_product.name,p_cart.num,p_product.pic,p_product.price')->join('p_product ON p_cart.productid=p_product.id')->where($where)->select();
        $this->assign('res',$result);
        $this->display();
    }
    public function deletecart(){
        $orderlog =M('p_cart');
        $result  = $orderlog->where(array('id'=>I('id')))->select();
        if(!$result[0]){
            echo "<script>alert('货品不存在');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Product/shopcart';";
            echo "</script>";
            exit;
        }
        $orderlog->where(array('id'=>I('id')))->delete();
        echo "<script>alert('删除成功');";
        echo "window.location.href='".__ROOT__."/index.php/Home/Product/shopcart';";
        echo "</script>";
        exit;
    }

    public function category(){
        $type_list= M('p_type')->where(array('pid'=>0))->order('sort asc')->select();
        $cdata=array();
        $cdata['pid']= array('NEQ','0');
        if(I('id')){
          $cdata['pid']=I('id');
        }
        $child_list = M('p_type')->where($cdata)->order('sort asc')->select();
        $this->assign('type_list',$type_list);
        $this->assign('child_list',$child_list);
        $this->display();
    }

    public function specialprice(){
        $this->display();
    }


    public function lists(){
        $product =M('p_product');
        if (I('ftype')) {
            
        }
        if (I('ctype')) {
           $where['ctype'] = I('ctype');
        }

        $result  = $product->where($where)->select();
        $this->assign('res',$result);
        $this->display();
    }
}