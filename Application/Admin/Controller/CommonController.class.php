<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
		// 所有方法调用之前，先执行
	public function _initialize(){
        $_SESSION['uname'] = "admin";

		if(!$_SESSION['uname']){
		    echo "暂停维护";
		    exit();
//			echo "<script>alert('请登录');";
//	            echo "window.location.href = '".__ROOT__."/index.php/Admin/User/login';";
//	            echo "</script>";
//				exit;
		}

		$ip =$this->get_remote_addr();
		$allowip = M('s_dict')->where(array('code'=>'DICT_IP'))->find();
		if ($allowip['displayvalue'] != $ip) {
			echo "IP错误";
		    exit();
		}

		$user = M('p_user');
		$result= $user->where(array('name'=>$_SESSION['uname']))->select();
		if(!$result[0]['state']){
            echo "账号已被禁用";
            exit();
        }
		$_SESSION['manager'] =$result[0]['manager'];
		$this->assign('names',$_SESSION['uname']);
		$this->assign('manager',$result[0]['manager']);
	}


	private  function get_remote_addr()
	{
	    if (isset($_SERVER["HTTP_X_REAL_IP"]))
	    {
	        return $_SERVER["HTTP_X_REAL_IP"];
	    }
	    else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
	    {
	        return preg_replace('/^.+,\s*/', '', $_SERVER["HTTP_X_FORWARDED_FOR"]);
	    }
	    else
	    {
	        return $_SERVER["REMOTE_ADDR"];
	    }
	}

}