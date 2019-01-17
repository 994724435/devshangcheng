<?php

namespace Admin\Controller;

use Think\Controller;

class UserController extends Controller
{

    public function qrcode()
    {
        Vendor('phpqrcode.phpqrcode');
        $id = I('get.id');
        //生成二维码图片 http://localhost/index.php/Home/Login/reg
        $object = new \QRcode();
        $url = "http://" . $_SERVER['HTTP_HOST'] . '/index.php/Home/Login/reg/fid/' . $id;

        $level = 3;
        $size = 5;
        $errorCorrectionLevel = intval($level);//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    public function login()
    {
//         $user = M('m_rob_order');
//         // $where='createDate >='.'2018-12-16';
//         $where['createDate'] = array('EGT',date("Y-m-d") );
//         $res =$user->where($where)->sum('price');
//         if ($res) {
//             # code...
//         }else{
//            $res=0;
//         }
//         print_r('今日抢单总额度：'.$res);die;
        if (IS_POST) {
            $name = I('post.name');
            $pwd = I('post.pwd');
            $user = M('p_user');
            if (!$name || !$pwd) {
                echo "<script>alert('用户名或密码不存在');";
                echo "window.history.go(-1);";
                echo "</script>";
            }
            $result = $user->where(array('name' => $name))->select();
            if ($result[0]['password'] == $pwd) {
                $_SESSION['uname'] = $name;
                echo "<script>window.location.href = '" . __ROOT__ . "/index.php/Admin/Index/main';</script>";
            } else {
                $_SESSION['number'] = $_SESSION['number'] + 1;
                if ($_SESSION['number'] > 2) {
                    $user->where(array('name' => $name))->save(array('manager' => 0));
                }
                echo "<script>alert('用户名或密码不存在,');";
                echo "</script>";
                $this->display();
            }
        }
        $this->display();
    }

    public function logOut()
    {
        session('uname', null);
        cookie('is_login', null);
        echo "<script>window.location.href = '" . __ROOT__ . "/index.php/Admin/User/login';</script>";
    }

    /*
     * 日常数据检查
     */
    public function crontab()
    {
        $issend=0;
        $message="";
        $Model = M();
        // 1、抢单数据重复
        $sql = "SELECT m.num from ( SELECT userId,count(*) as num from `m_rob_order` WHERE createDate>=curdate()  GROUP BY userId ORDER BY num desc ) m WHERE m.num>1 ";
        $rob_is_two = $Model->query($sql);

        if ($rob_is_two){
            $message ="今日抢单有大于2的异常数据"."<br>";
            $issend =1;
            print_r($message);
        }

        // 2、到账异常重复到账
        $account_sql="SELECT	S.recordToUserId FROM s_account_record S WHERE S.createDate >= curdate() AND S.createDate IN ( SELECT m.createDate FROM ( SELECT A.createDate,	A.recordToUserId,	COUNT(recordToUserId) AS num	FROM	s_account_record A	WHERE	A.recordType = 0	AND A.createDate >= curdate()	GROUP BY	A.createDate,	A.recordToUserId	) m	WHERE	m.num > 1) ORDER BY  S.createDate,	S.recordToUserId ASC";
        $account_res = $Model->query($account_sql);

        if ($account_res){
            print_r("今日异常重复到账数据");
            $message =$message."今日异常重复到账数据"."<br>";
        }

        // 3、重复匹配
        $sql_plan ="SELECT m.num from ( SELECT planToonUserId,count(*) as num from m_mate where createDate>curdate() GROUP BY price,planToonUserId,cashapplyUserId ORDER BY num desc) m where m.num>2";
        $account_res = $Model->query($sql_plan);
        if ($account_res){
            print_r("今日有排单重复");
            $message =$message."今日有排单重复"."<br>";
            $issend=1;
        }

        // 4、账户余额为负数
        $sql4="SELECT * from s_account WHERE totalPrice <0 or integral <0";
        $result4 = $Model->query($sql4);
        if ($result4) {
            print_r("账户余额为负数");
            $message =$message."账户余额为负数"."<br>";
            $issend=1;
        }

        // 5、存在三次排单用户
        $sql5 =$sql5."SELECT * FROM ( SELECT B.userid,B.dateform,B.price,count(*) as num FROM (                   ";
$sql5 =$sql5."SELECT                                                                                      ";
$sql5 =$sql5."  date_format(A.createDate, '%Y%m%d%H') as dateform,                                        ";
$sql5 =$sql5."  A.*                                                                                       ";
$sql5 =$sql5."FROM                                                                                        ";
$sql5 =$sql5."  `m_platoon_order` A                                                                       ";
$sql5 =$sql5."WHERE                                                                                       ";
$sql5 =$sql5."  A.STATUS = 0                                                                              ";
$sql5 =$sql5."  AND A.createDate >='2018-01-15' ) B GROUP BY B.dateform,B.price,B.userid ) c where c.num>2";
        $result5 = $Model->query($sql5);
        if ($result5) {
            print_r("存在账户三次排单");
            $message =$message."存在账户三次排单"."<br>";
            $issend=1;
        }

        $sql6 ="SELECT * from ( SELECT A.userId,COUNT(*) as num from m_platoon_order A where status =0 GROUP BY A.userId ORDER BY num desc ) B where B.num >2";
         $result6 = $Model->query($sql6);
        if ($result6) {
            print_r("存在账户三次排单");
            $message =$message."存在账户三次排单"."<br>";
            $issend=1;
        }

        if ($issend) {
            vendor('Ucpaas.Ucpaas','','.class.php');
            //初始化必填
            $options['accountsid']='91fab867d00475a570640abe64d7454f';
            $options['token']='89d730355ab7a6f3bcc02daa43d81557';
            $ucpass = new \Ucpaas($options);
            $appId = "cd9233a18f5f421c8a19381c9e8833e7";
            $to = '18883287644';
            $templateId = "421973";
            $param=$message ;
            $resmsg =$ucpass->templateSMS($appId,$to,$templateId,$param);
        }
        echo 'success';
    }



    private function isdong(){
        $config =M("config")->where(array('id'=>3))->find();
        if($config['value'] ==1){
            return 1;
        }else{
            return 2;
        }
    }

    /**
     * 用户收入
     * @param $uid
     * @param $money
     */
    private function addmoney($uid, $money)
    {
        $menber = M("menber");
        $userinfos = $menber->where(array('uid' => $uid))->select();
        $dongbag = $afterincom = bcadd($userinfos[0]['djbag'], $money, 2);
        $menber->where(array('uid' => $uid))->save(array('djbag'=>$dongbag));
    }

    /**
     * @param $uid
     * @param $type
     * @param $out
     * @return int
     * 每个类型的牛 收益是否到期
     */
    public function isincome($uid, $type, $out)
    {
        $daycomelogs = M('p_incomelog')->where(array('type' => $type, 'userid' => $uid))->sum('p_income');
        if ($daycomelogs >= $out) {
            return 1;
        }
        return 0;
    }

    /**
     * @return int 1大于  0小于 没有到上限
     * 每日收益上限
     */
    public function isshang($uid)
    {
        // 查询今日收益上线
        $todayincomeall = M("incomelog")->where(array('userid' => $uid, 'state' => 1, 'addymd' => date('Y-m-d', time())))->sum('p_income');
        $config = M("Config")->where(array('id' => 13))->find();
        if ($todayincomeall >= $config[0]['value']) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @return int ok
     * 是否有每日收益
     */
    public function getusernums($userid)
    {
        $income = M('p_incomelog');
        $daycomelogs = $income->where(array('type' => 10, 'userid' => $userid))->sum('p_income');
        $conf = M("config")->where(array('id' => 1))->find();
        if ($daycomelogs >= $conf['value']) {
            return 1;
        } else {
            return 0;
        }
    }

    private function savelog($data)
    {
        $incomelog = M('p_incomelog');
        return $incomelog->add($data);
    }


    public function crantabUserIncome()
    {
        $menber = M('p_menber');
        $income = M('p_incomelog');
        if ($_GET['uid']) {
            $map['uid'] = $_GET['uid'];
        } else {
            $map['uid'] = array('gt', 9);
        }
        $result_user = $menber->where($map)->select();
        foreach ($result_user as $k => $v) {
            $chargebag = $v['chargebag'];
            $incomebag = $v['incomebag'];
            $allIncome = bcadd($chargebag, $incomebag, 2);  // 所有钱包

            $daycomelogs = $income->where(array('state' => 1, 'userid' => $v['uid']))->select();
            $userIncome = 0;
            foreach ($daycomelogs as $k1 => $v1) {         // 收益
                $userIncome = bcadd($userIncome, $v1['income'], 2);
            }
            if ($_GET['uid']) {
                print_r("每日收益==》" . $userIncome);
            }
            $dayoutlogs = $income->where(array('state' => 2, 'userid' => $v['uid']))->select();

            $userOut = 0;                              // 支出
            foreach ($dayoutlogs as $k2 => $v2) {
                $userOut = bcadd($userOut, $v2['income'], 2);
            }
            if ($_GET['uid']) {
                print_r("<br>总支出==》" . $userOut);
            }
            $allIncomesUser = bcsub($userIncome, $userOut, 2);      // 总收入
            if ($allIncomesUser < 0) {
                print_r("userID" . $v['uid'] . "收入日志异常");
            }
            $layout = $allIncomesUser - $allIncome;
            if ($layout != 0) {
                print_r("用户ID：" . $v['uid'] . "<br>");
                print_r("钱包总额：" . $allIncome . "<br>");
                print_r("收入总额：" . $allIncomesUser . "<br><br><br>");
            }
        }
//        print_r($result_user);die;
    }


    function crontabRite()
    {
        $today = date('m-d', time());
        $isdate = M("Rite")->where(array('date' => $today))->select();
        if ($isdate[0]) {
//            $config= M("Config")->where(array('name'=>'daily_income'))->select();
//            M("Rite")->where(array('date'=>$today))->save(array('cont'=>$config[0]['val'],'date'=>$today));
            echo 2;
            exit();
        } else {
            $config = M("Config")->where(array('id' => 1))->select();
            M("Rite")->add(array('cont' => $config[0]['value'], 'date' => $today));
            echo 1;
            exit();
        }
    }
}


?>