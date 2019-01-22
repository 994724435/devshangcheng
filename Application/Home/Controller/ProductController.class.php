<?php

namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class ProductController extends CommonController{

    /**
     * 下单密码支付
     */
    public function paypassword(){
        if(!$_GET['orderid']){
            echo "<script>alert('订单ID异常');window.location.href = '".__ROOT__."/index.php/Home/User/member';</script>";
            exit();
        }else{
            $istrueorder= M('p_orderlog')->where(array('userid'=>session('uid'),'orderid'=>$_GET['orderid']))->find();
            if(!$istrueorder['id']){
                echo "<script>alert('异常订单');window.location.href = '".__ROOT__."/index.php/Home/User/member';</script>";
                exit();
            }

            if($istrueorder['state'] != 1){
                echo "<script>alert('订单状态异常');window.location.href = '".__ROOT__."/index.php/Home/User/member';</script>";
                exit();
            }
           $account_info =  M('s_account')->where(array('userId'=>session('uid')))->find();

           if($account_info['totalprice'] < $istrueorder['totals']){
               echo "<script>alert('账户余额不足');window.location.href = '".__ROOT__."/index.php/Home/User/member';</script>";
               exit();
           }

            $dict = M('s_dict')->where(array('code'=>'DICT_SHOP_INTG'))->find();
            if($dict['realvalue'] > $account_info['integral']){
                echo "<script>alert('账户积分不足');window.location.href = '".__ROOT__."/index.php/Home/User/member';</script>";
                exit();
            }
        }

        if($_POST){
          $userinfo =  M('p_user')->where(array('uid'=>session('uid')))->find();
          if($userinfo['password'] != md5(trim($_POST['password']))  ){
              echo "<script>alert('密码错误');window.location.href = '".__ROOT__."/index.php/Home/Product/paypassword/orderid/".$_GET['orderid']."';</script>";
              exit();
          }


            $res_order = M('p_orderlog')->where(array('orderid'=>$_GET['orderid']))->save(array('state'=>2));
            if($res_order){
                $datalog['recordBody'] ='下单购买扣除余额';
                $datalog['recordPrice'] = intval($istrueorder['totals']);
                $recordNowPrice = bcsub($account_info['totalprice'],$istrueorder['totals']);
                $datalog['recordNowPrice'] = $recordNowPrice;
                $datalog['recordStatus'] = 0;
                $datalog['recordType'] =0;
                $datalog['recordMold'] =6;
                $datalog['recordToObject'] =$istrueorder['orderid'];
                $datalog['recordToUserId'] =session('uid');
                $datalog['recordToAccountId'] =  $istrueorder['shopid'];
                $datalog['createDate'] =date('Y-m-d H:i:s');
                $is_log = M('p_incomelog')->add($datalog);
                if($is_log){
                    $account['totalPrice'] =$recordNowPrice;
                    $account['canPrice'] =$recordNowPrice;
                    M('s_account')->where(array('userId'=>session('uid')))->save($account);
                }

                // 处理积分

                if($dict['realvalue']){
                    $datalog['recordBody'] ='下单购买扣除积分';
                    $datalog['recordPrice'] =$dict['realvalue'];
                    $datalog['recordType'] =1;
                    $datalog['createDate'] =date('Y-m-d H:i:s');
                    $recordNowintegral = bcsub($account_info['integral'],$dict['realvalue']);
                    $datalog['recordNowPrice'] =$recordNowintegral;
                    M('p_incomelog')->add($datalog);
                    M('s_account')->where(array('userId'=>session('uid')))->save(array('integral'=>$recordNowintegral));
                }
            }

            echo "<script>alert('支付成功');window.location.href = '".__ROOT__."/index.php/Home/User/allorder';</script>";
            exit();

        }

        $this->display();
    }

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
            // 当前登录人的店铺
            $user_shop_info= M('p_shop')->where(array('userid'=>session('uid')))->find();
            foreach($arr_id as $k=>$v){
                $temp_product =  M('p_product')->where(array('id'=>$v))->find();
                if($temp_product['shopid'] == $user_shop_info['id']){
                    echo "<script>alert('自己不能购买自己商品');window.location.href = '".__ROOT__."/index.php/Home/Index/index';</script>";
                    exit();
                }
            }

            // 是否有待支付订单
            $isnopay= M('p_orderlog')->where(array('userid'=>session('uid'),'state'=>0))->find();
            if($isnopay['id']){
                echo "<script>alert('存在待支付订单');window.location.href = '".__ROOT__."/index.php/Home/User/allorder';</script>";
                exit();
            }

            //是否有支付密码
            $ispaypass= M('p_user')->where(array('uid'=>session('uid')))->find();
            if( empty($ispaypass['id'])){
                echo "<script>alert('请设置支付密码');window.location.href = '".__ROOT__."/index.php/Home/User/password/id/".$_GET['id']."/current_num/".$_GET['current_num']."/addr_id/ ';</script>";
                exit();
            }
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
                $data['state'] =1;
                $data['orderid'] = $orderid;
                $data['producturl'] = $v['pic'];
                $data['num'] = $v['current_num'];
                $data['totals'] = $total_price;
                $data['option'] = $_POST['commits'];
                $data['addtime'] = date('Y-m-d H:i:s');
                $data['addrid'] = $addr_id;
                M('p_orderlog')->add($data);
            }

            echo "<script>window.location.href = '".__ROOT__."/index.php/Home/Product/paypassword/orderid/".$orderid."';</script>";
            exit();
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

        $allprice =0;
        foreach ($result as $v){
            $allprice=$allprice+ $v['num']*$v['price'];
        }

        if($_POST){
            $canid ='';
            $cancurrent_num ='';
            foreach ($_POST as $k=>$value){
                if($value){
                    $canid=$canid.','.$k;
                    $cancurrent_num=$cancurrent_num.','.$value;
                }
            }
            $canid = substr($canid,1,strlen($canid));
            $cancurrent_num = substr($cancurrent_num,1,strlen($cancurrent_num));
            if($canid){
                echo "<script>";
                echo "window.location.href='".__ROOT__."/index.php/Home/Product/order?id=".$canid."&current_num=".$cancurrent_num."';";
                echo "</script>";
                exit;
            }
        }
        $this->assign('allprice',$allprice);
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
        $product_list = M('p_product')->where(array('state'=>1))->select();
        $this->assign('product_list',$product_list);
        $this->display();
    }


    public function lists(){
        $product =M('p_product');
        if (I('ftype')) {
            
        }
        if (I('ctype')) {
           $where['ctype'] = I('ctype');
        }
        if($_POST){
            $canid ='';
            $cancurrent_num ='';
            foreach ($_POST as $k=>$value){
                if($value){
                    $canid=$canid.','.$k;
                    $cancurrent_num=$cancurrent_num.','.$value;
                }
            }
            $canid = substr($canid,1,strlen($canid));
            $cancurrent_num = substr($cancurrent_num,1,strlen($cancurrent_num));
            if($canid){
                echo "<script>";
                echo "window.location.href='".__ROOT__."/index.php/Home/Product/order?id=".$canid."&current_num=".$cancurrent_num."';";
                echo "</script>";
                exit;
            }

        }
        $result  = $product->where($where)->order('salenum DESC')->select();
        $this->assign('res',$result);
        $this->display();
    }
}