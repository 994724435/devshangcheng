<?php

namespace Admin\Controller;
use Think\Controller;
vendor('Qiniu.Auth');
vendor('Qiniu.Storage.UploadManager');
use Qiniu\Auth ;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
header('content-type:text/html;charset=utf-8');
class IndexController extends CommonController {
	public function comproduct(){
	    M("p_orderlog")->where(array('logid'=>$_GET['id']))->save(array('state'=>2));
        echo "<script>alert('修改成功');window.location.href = '".__ROOT__."/index.php/Admin/Index/select';</script>";
        exit();
    }

    public function main(){
        $user = M('p_user');
//        if($_SESSION['manager']==2){
            $user_res= $user->select();
//        }
        $this->assign('re',$user_res);
        $this->display();
    }

    // 密码修改
    public function userpwd(){
        $user = M('p_user');
        if($_POST){
            $data['password'] =$_POST['psd1'];
            $data['manager'] =$_POST['manager'];
            $result = $user->where(array('id'=>$_GET['id']))->save($data);
            if($result){
                echo "<script>alert('修改成功');window.location.href = '".__ROOT__."/index.php/Admin/Index/main';</script>";
            }
        }
        $res= $user->where(array('id'=>$_GET['id']))->select();
        $this->assign('re',$res[0]);
        $this->display();
    }

    public function addproduct(){
        $types= M('p_type')->order('sort asc')->select();
        $temp_array=array();    
        foreach ($types as $key => $value) {
                if ($value['pid'] == 0) {
                    $temp_array[]= $value;
                    unset($types[$key]);
                }
        }   
       
        if($_POST){
            $pic='';
            if($_FILES['thumb']['name']){   // 上传文件
                $setting = C('UPLOAD_FILE_QINIU');
                $Upload = new \Think\Upload($setting);
                $info = $Upload->upload($_FILES);
                if(!$info) {// 上传错误提示错误信息

                }else{// 上传成功
                    $pic=$info['thumb']['url'];
                }
            }
            $data['istui'] =$_POST['istui'];
            $data['iste'] =$_POST['iste'];
            $data['isjing'] =$_POST['isjing'];
            $data['name'] =$_POST['name'];
            $data['ftype'] =$_POST['ftype'];
            $data['ctype'] =$_POST['ctype'];
            $data['cont'] =$_POST['content1'];
            $data['pic'] =$pic;
            $data['price'] =$_POST['price'];
            $data['addtime'] =date('Y-m-d H:i:s',time());

            $product =M('p_product');
            $result = $product->add($data);
            if($result){
                echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Index/productlist';</script>";
            }else{
                echo "<script>alert('添加失败');window.location.href = '".__ROOT__."/index.php/Admin/Index/addproduct';</script>";
            }

        }
         $this->assign('fid',$temp_array);
        $this->display();
    }

    public function productlist(){
        $product =M('p_product');
        $result = $product->order('state asc')->select();
        $this->assign('res',$result);
        $this->display();
    }

    public function editeproductbanner()
    {
        $product=M('p_product_banner');
        if($_FILES['thumb']['name']){
            $pic='';
            if($_FILES['thumb']['name']){   // 上传文件
                $setting = C('UPLOAD_FILE_QINIU');
                $Upload = new \Think\Upload($setting);
                $info = $Upload->upload($_FILES);
                if(!$info) {// 上传错误提示错误信息

                }else{// 上传成功
                    $pic=$info['thumb']['url'];
                }
            }

            $data['url'] =$pic;
            $data['proid'] =$_GET['id'];
            $data['addtime'] =date('Y-m-d H:i:s',time());

            $result = $product->add($data);
            if($result){
                echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Index/editeproductbanner/id/".$_GET['id']."';</script>";
            }else{
                echo "<script>alert('添加失败');window.location.href = '".__ROOT__."/index.php/Admin/Index/editeproductbanner/id/".$_GET['id']."';</script>";
            }

        }

        $result = $product->where(array('proid'=>$_GET['id']))->select();
        $this->assign('res',$result);
        $this->display();
    }

    public function deletebanner(){
        $product =M('p_product_banner');
        $res = $product->where(array('id'=>$_GET['id']))->delete();

        if($res){
            echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Index/editeproductbanner/id/".$_GET['pid']."';</script>";
        }else{
            echo "<script>alert('修改失败');window.location.href = '".__ROOT__."/index.php/Admin/Index/editeproductbanner/id/".$_GET['pid']."';</script>";
        }
    }

    public function editeproduct(){
        $types= M('p_type')->order('sort asc')->select();
        $temp_array=array();    
        foreach ($types as $key => $value) {
                if ($value['pid'] == 0) {
                    $temp_array[]= $value;
                    unset($types[$key]);
                }
        }  
        $product =M('p_product');
        if($_POST){
            $pic='';
            if($_FILES['thumb']['name']){   // 上传文件
                $setting = C('UPLOAD_FILE_QINIU');
                $Upload = new \Think\Upload($setting);
                $info = $Upload->upload($_FILES);
                if(!$info) {// 上传错误提示错误信息

                }else{// 上传成功
                    $pic=$info['thumb']['url'];
                }
            }

            $data['istui'] =$_POST['istui'];
            $data['iste'] =$_POST['iste'];
            $data['isjing'] =$_POST['isjing'];

            $data['name'] =$_POST['name'];
            $data['ftype'] =$_POST['ftype'];
            $data['ctype'] =$_POST['ctype'];
            $data['cont'] =$_POST['content1'];
            if($pic){
                $data['pic'] =$pic;
            }
            $data['price'] =$_POST['price'];
            $data['addtime'] =date('Y-m-d H:i:s',time());
            $result = $product->where(array('id'=>$_GET['id']))->save($data);
            if($result){
                echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Index/productlist';</script>";
            }else{
                echo "<script>alert('修改失败');window.location.href = '".__ROOT__."/index.php/Admin/Index/productlist';</script>";
            }

        }
        $result = $product->where(array('id'=>$_GET['id']))->select();
        $this->assign('res',$result[0]);
        $this->assign('fid',$temp_array);
        $this->display();
    }


    public function deleteproduct(){
        $product =M('p_product');
        $result = $product->where(array('id'=>$_GET['id']))->select();
        if($result[0]){
            $state =$result[0]['state'];
        }else{
            echo "<script>alert('产品不存在');window.location.href = '".__ROOT__."/index.php/Admin/Index/productlist';</script>";
        }
        if($state==1){
            $state=2;
        }else{
            $state=1;
        }
        $res= $product->where(array('id'=>$_GET['id']))->save(array('state'=>$state));
        if($res){
            echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Index/productlist';</script>";
        }else{
            echo "<script>alert('修改失败');window.location.href = '".__ROOT__."/index.php/Admin/Index/productlist';</script>";
        }
    }

    public function select(){
        $orderlog = M('p_orderlog');
        if($_GET['state']){
            $map['state'] =$_GET['state'];
        }
        if($_GET['uid']){
            $select_user = M('p_user')->where(array('userAccount'=>$_GET['uid']))->find();
            $map['userid'] = $select_user['id'];
        }
        if($_GET['orderid']){
            $map['orderid'] =$_GET['orderid'];
        }

        $users= $orderlog ->alias('t')-> field('t.*,u.useraccount')->join('left join s_user as u on t.userid=u.id ')->where($map)->order('t.id DESC')->select();
       
        $this->assign('users',$users);
        $this->display();
    }

    public function orderdetail()
    {
        
        $orderid =$_GET['orderid'];
        $cond['orderid'] =$orderid;
        if($_GET['id']){
            $cond['id'] =$_GET['id'];
        }

        $orderinfo = M('p_orderlog')->where($cond)->select();
        
        
        // addr
        $addr = M('p_addr')->where(array('id'=>$orderinfo[0]['addrid']))->find();
        // commit
        $commits = M('p_ordercommits')->where(array('orderid'=>$orderid))->find();

        $this->assign('addr',$addr);
        $this->assign('orderlog',$orderinfo[0]);
        $this->assign('commits',$commits);
        $this->display();
    }

    // 添加管理员
    public function useradd(){
        if ($_POST) {
            $user = M('p_user');
            $data['password'] =$_POST['psd1'];
            $data['name']   = $_POST['name'];
            $data['manager']   = $_POST['manager'];
            $data['addtime']  = date('Y-m-d H:i:s',time());
            $user_res= $user->where(array('name'=>$data['name']))->select();
            if($user_res[0]['id']){
                echo "<script>alert('用户名已存在');window.location.href = '".__ROOT__."/index.php/Admin/Index/main';</script>";exit();
            }
            $res= $user->add($data);
            if ($res) {
                echo "<script>alert('添加成功');window.location.href = '".__ROOT__."/index.php/Admin/Index/main';</script>";
            }else{
                echo "<script>alert('添加失败');window.location.href = '".__ROOT__."/index.php/Admin/Index/main';</script>";
            }

        }
        $this->display();
    }

    public function userdelete(){
        $user = M('p_user');
        $res = $user->where(array('id'=>$_GET['id']))->delete();
        if ($res) {
            echo "<script>alert('删除成功');window.location.href = '".__ROOT__."/index.php/Admin/Index/main';</script>";
        }else{
            echo "<script>alert('删除失败');window.location.href = '".__ROOT__."/index.php/Admin/Index/main';</script>";
        }
    }
}