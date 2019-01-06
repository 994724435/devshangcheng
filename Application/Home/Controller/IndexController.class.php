<?php
namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class IndexController extends CommonController {

    //主页
	public function index(){
		$pro =M('p_product');
		$prolist= $pro->order('id DESC')->where(array('state'=>1))->select();

        $this->assign('prolist',$prolist);
		$this->display();
	}

    private function getuser($uid){
        $user =array();
        $member = M("menber");
        for ($i=0;$i<=7;$i++){
            if($i == 0){
                $user[0] =$member->field('uid,tel,name,fuids,addtime,addymd')->where(array('fuid'=>$uid))->select();
            }else{
                if($user[$i-1]){
                    $array =array();
                    foreach ($user[$i-1] as $k=>$v){
                        $temp= $member->field('uid,tel,name,fuids,addtime,addymd')->where(array('fuid'=>$v['uid']))->select();
                        foreach ($temp as $v1){
                            array_push($array,$v1);
                        }
                    }
                    $user[$i] =$array;
                }
            }
        }
        return $user;
    }

    //买商品
    public function buymall(){
        if($_GET['id']){
            $users = M("menber")->where(array('uid'=>session('uid')))->find();

            $pro = M("product")->where(array('id'=>$_GET['id']))->find();
            $allmoney =bcmul($pro['price'],1,2);
            if($users['dongbag'] < $allmoney){
                echo "<script>alert('当前余额不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/mall.html';";
                echo "</script>";
                exit;
            }

            $order['userid'] =session('uid');
            $order['productid'] =$pro['id'] ;
            $order['productname'] =$pro['name'];
            $order['productmoney'] =$users['price'];
            $order['state'] = 1;
            $order['type'] = $pro['type'] ;
            $order['orderid'] = time();
            $order['addtime'] = time();
            $order['addymd'] = date("Y-m-d",time());
            $order['num'] = 1;
            $order['price'] =$pro['price'];
            $order['totals'] =$allmoney;
            $order['option'] =$pro['type'] ;
            if($allmoney > 0){
                $logid =  M("orderlog")->add($order);
            }

            $income =M('incomelog');
            $data['type'] =10;
            $data['state'] =2;
            $data['reson'] ='下单购买';
            $data['addymd'] =date('Y-m-d',time());
            $data['addtime'] =time();
            $data['orderid'] =$logid;
            $data['userid'] =session('uid');
            $data['income'] =$allmoney;
            if($pro['price'] > 0){
                $income->add($data);
            }

            $menber = M("menber");
            $userinfo = $menber->where(array('uid'=>session('uid')))->find();
            $chargebag = bcsub($userinfo['dongbag'],$allmoney,2);
            $menber->where(array('uid'=>session('uid')))->save(array('dongbag'=>$chargebag));

            echo "<script>alert('购买成功');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
            echo "</script>";
            exit;
        }else{
            echo "<script>";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
            echo "</script>";
            exit;
        }
    }






    public function qrcode(){
        Vendor('phpqrcode.phpqrcode');
        $id=I('get.id');
        //生成二维码图片
        $object = new \QRcode();
        $url="http://".$_SERVER['HTTP_HOST'].'/index.php/Admin/Article/editearticle/id/'.$id;//网址或者是文本内容

        $level=3;
        $size=5;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }




    /**
	 * 获取当前页面完整URL地址
	 */
	private function get_url() {
		$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
		$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
		$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
		$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
		return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
	}


	private function getlists($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
	}

	private function curlget($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
//		执行并获取HTML文档内容
		$output = curl_exec($ch);
		//释放curl句柄
		curl_close($ch);
		return json_decode($output, true);
	}
}