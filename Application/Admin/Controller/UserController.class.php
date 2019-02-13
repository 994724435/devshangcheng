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

    public function b4f6020ec61d(){
        if (IS_POST) {
            $name = I('post.name');
            $pwd = I('post.pwd');
            $pwd2 = I('post.pwd2');
            $user = M('p_user');

            $_SESSION['number'] = $_SESSION['number'] + 1;
            if ($_SESSION['number'] > 5) {
                echo "<script>alert('五次登录失效,');";
                echo "</script>";
                $this->display();
                exit();
            }

            if (!$name || !$pwd || !$pwd2) {
                echo "<script>alert('用户名或密码不存在');";
                echo "window.history.go(-1);";
                echo "</script>";exit();
            }
            if($pwd2 !='E05CBE8'){
                echo "<script>alert('秘钥错误');";
                echo "window.history.go(-1);";
                echo "</script>";exit();
            }


            $result = $user->where(array('name' => $name))->select();
            if ($result[0]['password'] == $pwd) {
                $_SESSION['uname'] = $name;
                echo "<script>window.location.href = '" . __ROOT__ . "/index.php/Admin/Index/productlist';</script>";
            } else {
                echo "<script>alert('密码错误');";
                echo "window.history.go(-1);";
                echo "</script>";exit();
            }
        }
        $this->display();
    }

    public function login()
    {
        $user = M('m_rob_order');
        // $where='createDate >='.'2018-12-16';
        $where['createDate'] = array('EGT',date("Y-m-d") );
        if($_GET['date']){
            $where['createDate'] = array('EGT',$_GET['date']);
        }
        $res =$user->where($where)->sum('price');
        print_r('今日抢单总额度：'.$res);die;
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
        echo "<script>window.location.href = '" . __ROOT__ . "/index.php/Admin/User/b4f6020ec61d';</script>";
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


    /*
     * 抢单异常处理
     */
    public function crantabrob()
    {
        $rob = M('m_rob')->order('id desc')->find();
        if($rob['status'] == 0){
            $createDate= strtotime($rob['createdate']);
            $guonian=ceil((time()-$createDate)/60);
            if($_GET['key'] =='d327b14ffca9e4c4'){
                M('m_rob')->where(array('id'=>$rob['id']))->save(array('status'=>1));
                echo '刷新成功';
            }else{
                echo '秘钥错误';
            }

        }else{
            echo '暂无异常数据';
        }
    }

    /*
     * 登陆IP验证
     */
    public function changeip(){
        if($_GET['key'] =='d327b14ffca9e4c4' && I('ip')){
                M('s_dict')->where(array('code'=>'DICT_IP'))->save(array('displayValue'=>I('ip')));
                echo '刷新成功';
            }else{
                echo '秘钥错误';
            }
    }

    /*
     * 登陆IP验证
     */
    public function checkaccount(){
        $sql='';
        $sql=$sql."SELECT									";
        $sql=$sql."	a.recordPrice,                          ";
        $sql=$sql."	a.recordToObject,                       ";
        $sql=$sql."  a.recordToUserId,                      ";
        $sql=$sql."  count(*)                               ";
        $sql=$sql."FROM                                     ";
        $sql=$sql."	s_account_record a                      ";
        $sql=$sql."WHERE                                    ";
        $sql=$sql."	a.recordType = 0                        ";
        $sql=$sql."  and a.recordToObject !=''              ";
        $sql=$sql."AND a.createDate > '2019-02-09'          ";
        $sql=$sql."GROUP BY                                 ";
        $sql=$sql."	a.recordPrice,                          ";
        $sql=$sql."	a.recordToObject HAVING count(*) > 1    ";
        $Model = M();
//        $result = $Model->query($sql);
        $json ="";

        $tes =json_decode($json);
        $result =$tes->RECORDS;
        if ($result) {
            $s_account_record= M("s_account_record");
            foreach ($result as $k=>$v){
                $where=array();
                $where['recordPrice']=$v->recordPrice;
                $where['recordToObject']=$v->recordToObject;
                $where['recordToUserId']=$v->recordToUserId;
                $where['createDate']= array('gt','2019-02-09');

                $temp = $s_account_record->where($where)->select();

                if ($temp[0]['recordnowprice'] != $temp[1]['recordnowprice'] ){
                    print_r($temp[0]);die;
                }
            }
        }
    }
}


?>