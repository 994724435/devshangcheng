<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize(){
		$function = explode('/',__ACTION__);
		$curfunction =$function[count($function)-1];
		session('uid',5);
		if(!session('uid')){
			echo "<script>";
			echo "window.location.href='https://www.365sjf.com/html/login.html';";
			echo "</script>";
			exit;
		}
		$menber =M('s_user');
		$res_user =$menber->where(array('id'=>session('uid')))->select();
		if($res_user[0]['status'] !=1){
            echo "<script>alert('账号已被禁用');";
            echo "window.location.href='https://www.365sjf.com/html/login.html';";
            echo "</script>";
            exit;
        }

        $shop =M('p_shop')->where(array('userid'=>session('uid')))->find();
		if($shop['id']){
		    $isshop =1;
        }else{
            $isshop =0;
        }
        $this->assign('isshop',$isshop);

	}

	private function getfunction($curfunction){
		if($curfunction=='index'){
			return 1;
		}elseif($curfunction=='financial'){
			return 2;
		}elseif($curfunction=='product'){
			return 3;
		}elseif($curfunction=='user'){
			return 4;
		}else{
			return 1;
		}
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

}