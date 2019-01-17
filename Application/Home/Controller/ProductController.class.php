<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class ProductController extends CommonController{

    /**
     * 下单确认页面
     */
    public function order(){
        if($_GET['addr_id']){
            $addr_res = M('p_addr')->where(array('uid'=>session('uid'),'id'=>$_GET['addr_id']))->find();
            $addr_id = $_GET['addr_id'];
        }
        if(empty($addr_res)){
            $addr_res = M('p_addr')->where(array('uid'=>session('uid')))->order('isdefault DESC')->find();
            $addr_id = $addr_res['id'];
        }

        $id =$_GET['id'];
        $current_num= $_GET['current_num'];
        if ($id) {
            $arr_id = explode(',', $id);
            $arr_num = explode(',', $current_num);
            $list_info =array();
            $total_price = 0;
            foreach($arr_id as $k=>$v){
              $temp_product =  M('p_product')->where(array('id'=>$v))->find();
                $temp_product['current_num'] =$arr_num[$k];
                $list_info[] =$temp_product;
                $total_price =bcadd( $total_price , bcmul($arr_num[$k],$temp_product['price']));
            }

        }

        if($_POST){
            $orderid =date('YmdHis').rand(1000,9999);
            if(empty($addr_res)){
                echo "<script>alert('收货地址必填');window.location.href = '".__ROOT__."/index.php/Home/Product/order/id/".$_GET['id']."/current_num/".$_GET['current_num']."/addr_id/ ';</script>";
                exit();
            }

            foreach($list_info as $key=>$v){
                $data['userid'] = session('uid');
                $data['shopid'] = $v['shopid'];
                $data['productid'] =  $v['id'];
                $data['productname'] = $v['name'];
                $data['productmoney'] = $v['price'];
                $data['producturl'] = $v['pic'];
                $data['state'] =0;
                $data['orderid'] = $orderid;
                $data['producturl'] = $v['pic'];
                $data['num'] = $v['current_num'];
                $data['totals'] = $total_price;
                $data['option'] = $_POST['commits'];
                $data['addtime'] = date('Y-m-d H:i:s');
                $data['addrid'] = $addr_id;
                M('p_orderlog')->add($data);
            }

            
        }
        $this->assign('total_price',$total_price);
        $this->assign('addr_res',$addr_res);
        $this->assign('list_info',$list_info);
        $this->display();
    }
    /*
     * 收货地址
     */
    public function address(){
        if($_POST){
            if(!$_POST['name']){
                echo "<script>alert('收件人必填');";
                echo "</script>";
                $this->display();
                exit;
            }
            if(!$_POST['tel']){
                echo "<script>alert('电话必填');";
                echo "</script>";
                $this->display();
                exit;
            }
            if(!$_POST['province']){
                echo "<script>alert('省份必填');";
                echo "</script>";
                $this->display();
                exit;
            }
            if(!$_POST['city']){
                echo "<script>alert('城市必填');";
                echo "</script>";
                $this->display();
                exit;
            }
            if(!$_POST['county']){
                echo "<script>alert('区县必填');";
                echo "</script>";
                $this->display();
                exit;
            }
            if(!$_POST['addr']){
                echo "<script>alert('详细地址必填');";
                echo "</script>";
                $this->display();
                exit;
            }

            $isdefault_res = M('p_addr')->where(array('uid'=>session('uid'),'isdefault'=>1))->find();
            $isdefault= 1;
            if($isdefault_res['id']){
                $isdefault =0;
            }
            $data['name'] =I('name');
            $data['tel'] =I('tel');
            $data['province'] =I('province');
            $data['city'] =I('city');
            $data['county'] =I('county');
            $data['addr'] =I('addr');
            $data['uid'] =session('uid');
            $data['state'] =1;
            $data['addtime'] =date('Y-m-d H:i:s');
            $data['isdefault'] =$isdefault;
            $addr_id= M('p_addr')->add($data);
            if($_GET['id']){
                $this->redirect("/index.php/Home/Product/order/id/".$_GET['id']."/current_num/".$_GET['current_num']."/addr_id/".$addr_id);
            }

        }
        $this->display();
    }

    public function tureorder(){
        $this->display();
    }

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