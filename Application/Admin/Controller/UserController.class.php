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
//        $account_sql="SELECT	S.recordToUserId FROM s_account_record S WHERE S.createDate >= curdate() AND S.createDate IN ( SELECT m.createDate FROM ( SELECT A.createDate,	A.recordToUserId,	COUNT(recordToUserId) AS num	FROM	s_account_record A	WHERE	A.recordType = 0	AND A.createDate >= curdate()	GROUP BY	A.createDate,	A.recordToUserId	) m	WHERE	m.num > 1) ORDER BY  S.createDate,	S.recordToUserId ASC";
//        $account_res = $Model->query($account_sql);
//
//        if ($account_res){
//            print_r("今日异常重复到账数据");
//            $message =$message."今日异常重复到账数据"."<br>";
//        }


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
//        $sql5 =$sql5."SELECT * FROM ( SELECT B.userid,B.dateform,B.price,count(*) as num FROM (                   ";
//        $sql5 =$sql5."SELECT                                                                                      ";
//        $sql5 =$sql5."  date_format(A.createDate, '%Y%m%d%H') as dateform,                                        ";
//        $sql5 =$sql5."  A.*                                                                                       ";
//        $sql5 =$sql5."FROM                                                                                        ";
//        $sql5 =$sql5."  `m_platoon_order` A                                                                       ";
//        $sql5 =$sql5."WHERE                                                                                       ";
//        $sql5 =$sql5."  A.STATUS = 0                                                                              ";
//        $sql5 =$sql5."  AND A.createDate >='2018-03-22' ) B GROUP BY B.dateform,B.price,B.userid ) c where c.num>2";
//        $result5 = $Model->query($sql5);
//        if ($result5) {
//            print_r("存在账户三次排单");
//            $message =$message."存在账户三次排单"."<br>";
//            $issend=1;
//        }

        $sql6 ="SELECT * from ( SELECT A.userId,COUNT(*) as num from m_platoon_order A where status =0 GROUP BY A.userId ORDER BY num desc ) B where B.num >2";
         $result6 = $Model->query($sql6);
        if ($result6) {
            print_r("存在账户三次排单");
            $message =$message."存在账户三次排单"."<br>";
            $issend=1;
        }

        $result7 = M("s_account_record")->where('TO_DAYS(createDate)  = TO_DAYS(NOW())')->find();
        if($result7['id']){

            $sql8 = $sql8." SELECT                                 ";
            $sql8 = $sql8." 	b.*                                ";
            $sql8 = $sql8." FROM                                   ";
            $sql8 = $sql8." 	s_account_record b,                ";
            $sql8 = $sql8." 	(                                  ";
            $sql8 = $sql8." 		SELECT                         ";
            $sql8 = $sql8." 			a.recordPrice,             ";
            $sql8 = $sql8." 			a.recordToObject,          ";
            $sql8 = $sql8." 			a.recordToUserId,          ";
            $sql8 = $sql8." 	    a.recordNowPrice,              ";
            $sql8 = $sql8." 			count(*)                   ";
            $sql8 = $sql8." 		FROM                           ";
            $sql8 = $sql8." 			s_account_record a         ";
            $sql8 = $sql8." 		WHERE                          ";
            $sql8 = $sql8." 			a.recordType = 0           ";
            $sql8 = $sql8." 		AND a.recordToObject != ''     ";
            $sql8 = $sql8." 		AND a.id >       ".$result7['id'];
            $sql8 = $sql8." 		GROUP BY                       ";
            $sql8 = $sql8." 			a.recordPrice,             ";
            $sql8 = $sql8." 			a.recordToObject           ";
            $sql8 = $sql8." 		HAVING                         ";
            $sql8 = $sql8." 			count(*) > 1               ";
            $sql8 = $sql8." 	) c                                ";
            $sql8 = $sql8." WHERE                                  ";
            $sql8 = $sql8." 	b.id > ".$result7['id'];
            $sql8 = $sql8." AND c.recordToUserId = b.recordToUserId";
            $sql8 = $sql8." AND b.recordPrice = c.recordPrice      ";
            $sql8 = $sql8." and b.recordToObject =c.recordToObject ";
            $sql8 = $sql8." and b.recordNowPrice !=c.recordNowPrice";

            $result8 = $Model->query($sql8);
            if($result8){
                print_r("存在重复到账");
                $message =$message."存在重复到账"."<br>";
                $issend=1;
            }

            // 重复数据
            $sql9="SELECT recordToUserId,Count(*) as num FROM s_account_record where createDate > CURDATE() and recordType =0 GROUP BY recordPrice,recordToObject,recordToUserId,createDate HAVING num > 1";
            $result9 = $Model->query($sql9);
            if($result9){
                print_r("账户记录存在重复数据");
                $message =$message."账户记录存在重复数据"."<br>";
                $issend=1;
            }
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
        echo 1;exit();
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


    public function checkReferee(){
        $id =$_GET['id'];
        if(empty($id)){
            $dist = M('s_dict')->where(array('code'=>'DICT_CRONTAB'))->find();
            $id =(int) $dist['realvalue'];
        }

        $con['id'] =array('GT',$id);
        $con['type'] = 0 ;
        $allmate =  M("m_mate")->where($con)->order('id asc')->limit(2)->select();

        $temuid = 0 ;
        if($allmate[0]){
          foreach($allmate as $k=>$v){
              $molde =M();
              $sql_userinfo = "SELECT s.refereeId from s_user s where s.id=".$v['plantoonuserid'];
              $refer_into = $molde->query($sql_userinfo);
             if($refer_into[0]['refereeid']){
                 if($refer_into[0]['refereeid'] == 20389){
                     continue;
                 }
                 $sql ="SELECT count(  DISTINCT m.planToonUserId ) as bnum from m_mate m where m.type =0 and  m.planToonUserId IN(SELECT id from s_user s_user where s_user.status =1 and s_user.refereeId = ".$refer_into[0]['refereeid']." )";
                 $num = $molde->query($sql);
                 $s_account = M("s_account");
                 $account_info = $s_account->where(array('userId'=>$refer_into[0]['refereeid']))->find();

                 if( $account_info['refereenum'] != $num[0]['bnum']) {
                     $updatebool= $s_account->where(array('userId'=>$refer_into[0]['refereeid']))->save(array('refereeNum'=>$num[0]['bnum']));
                     echo $v['id']."----".$refer_into[0]['refereeid'].'已经更新'.$num[0]['bnum'].'状态'. $updatebool.'<br/>';
                 }else{
                     echo $v['id']."----".$refer_into[0]['refereeid'].'无需更新'.'<br/>';
                 }
             }

              $temuid =  $v['id'];
          }
            M('s_dict')->where(array('code'=>'DICT_CRONTAB'))->save(array('realValue'=>(int)$temuid));
        }else{
            echo "更新完成";
        }


    }


    public function dealuseaoocount(){
        echo 1;die;
        $uid= 5 ;
        //删除记录表里面的体现
        $s_account_record = M("s_account_record");
        $cond_record['recordMold'] = 3;
        $cond_record['recordToUserId'] = $uid;
        $cond_record['id'] = array('gt',1153779);
        $cond_record['id'] = array('lt',1165196);
        $s_account_record->where($cond_record)->delete();

        //删除体现表的数据
        $m_platoon_order = M("m_platoon_order");
        $cond_platoon['userId']= $uid;
        $cond_platoon['status']= 0 ;
        $cond_platoon['id']= array('lt',197668);
        $cond_platoon['id']= array('gt',195926);
        $m_platoon_order->where($cond_platoon)->delete();

        $cond_recods['recordType'] = 0 ;
        $cond_recods['recordToUserId'] = $uid ;
        $cond_recods['id'] = array('gt',1153779);
        $cond_recods['recordStatus'] = 1 ;
        $account_recods = $s_account_record->where($cond_recods)->select();


    }

    public function freshreffer(){
        $molde =M();
        $userid =(int)I('userId');
        if(empty($userid)){
            echo "<script>alert('用户ID异常');window.location.href = 'https://www.365sjf.com/html/index.html';</script>";
            exit();
        }
        $sql ="SELECT count(  DISTINCT m.planToonUserId ) as bnum from m_mate m where m.type =0 and  m.planToonUserId IN(SELECT id from s_user s_user where s_user.status =1 and s_user.refereeId = ".$userid." )";
        $num = $molde->query($sql);
        $s_account = M("s_account");
        $account_info = $s_account->where(array('userId'=>$userid))->find();

        if( $account_info['refereenum'] != $num[0]['bnum']) {
            $updatebool= $s_account->where(array('userId'=>$userid))->save(array('refereeNum'=>$num[0]['bnum']));
            echo "<script>alert('刷新成功');window.location.href = 'https://www.365sjf.com/html/index.html';</script>";
            exit();
        }else{
            echo "<script>alert('已经刷新');window.location.href = 'https://www.365sjf.com/html/index.html';</script>";
            exit();
        }
    }

    /**
     * 处理冻结钱包余额
     */
    public function dealUserDong(){

        $m_user =M("s_user");
        $m_cashapply_order = M("m_cashapply_order");
        $m_rob_order = M("m_rob_order");
        $m_account   = M("s_account");

        $s_dict = M("s_dict")->where(array('code'=>'DICT_CRONTAB_DONG'))->find();
        $id =(int)$s_dict['realvalue'];

        $user_con['status'] = 1;
        $user_con['id'] =array('GT',$id);
        $user_list = $m_user->where($user_con)->limit(10)->select();
        if($user_list[0]){
            $new_id = 0;
            foreach ($user_list as $k=>$v){
                $new_id = $v['id'];
               // 最新提现
               $cash_con = array();
               $cash_con['status'] = 0 ;
               $cash_con['userId'] = $v['id'];
               $result_cash_list = $m_cashapply_order->where($cash_con)->order('price desc')->find();
               $account_info = $m_account->where(array('userId'=>$v['id']))->find();
               $upafter = $account_info['totalprice']/2;
               if($result_cash_list['id']){ // 有提现处理提现
                   //最新完成抢单
                   $rob_con['status'] = 1;
                   $rob_con['userId'] = $v['id'];
                   $result_rob_order=$m_rob_order->where($rob_con)->order('id desc')->find();

                   if($result_rob_order['id']){ // 最新抢单数据
                       $rob_price = $result_rob_order['price'] * 1.10;
                       if($result_cash_list['price'] == $rob_price) {
                           continue;
                       }
                   }

                   $cash_half = $result_cash_list['price']/2;
                   //如果没有抢单 处理提现
                   if($account_info['dongprice']  == $cash_half ){
                       continue;
                   }
                   $abs = abs($cash_half - $account_info['dongprice'] );
                   if($cash_half >  $account_info['dongprice']) {
                       $cash_new_price = $result_cash_list['price'] -($cash_half - $account_info['dongprice'])  ;
                       $m_cashapply_order->where(array('id'=>$result_cash_list['id']))->save(array('price'=>$cash_new_price,'remainPrice'=>$cash_new_price));

                       $dong_price = $cash_half;
                       $m_account->where(array('userId'=>$v['id']))->save(array('dongprice'=>$dong_price));
                       echo $v['id']."账户更新提现数据,提现ID：{$result_cash_list['id']},更变数据".$result_cash_list['price']."--->".$result_cash_list['price']/2;
                       echo "<br>";
                   }



               }else{ // 无体现 检测账户余额
                   if($account_info['dongprice'] > 0){
                       continue;
                   }
                   if($account_info['totalprice'] == 0){
                       continue;
                   }
                   echo $v['id']."账户没有提现数据,跳过";
//                   echo $v['id']."账户没有提现数据，更新账户余额:{$account_info['totalprice']}--->".$upafter."<br>";
//                   $uid = $v['id'];
//                   $update_con= array();
//                   $update_con['dongprice'] = $account_info['totalprice']/2;
//                   $update_con['canPrice'] = $account_info['totalprice']/2;
//                   $update_con['totalPrice'] = $account_info['totalprice']/2;
//                   $m_account->where(array('userId'=>$v['id']))->save($update_con);
               }
            }
            if($new_id > 0 ){
                M("s_dict")->where(array('code'=>'DICT_CRONTAB_DONG'))->save(array('realValue'=>$new_id));
            }
        }
    }

    /*
     * 处理匹配单子 冻结钱包
     */
    public function dealMeteDong(){
        $m_mate = M("m_mate");
        $m_account   = M("s_account");
        $m_dong_log  = M("p_mete_dong_log");
        $m_platoon_order= M("m_platoon_order");
        $m_rob_order =M("m_rob_order");
        $m_job   = M("m_job");
        $m_s_account_record=M("s_account_record");
        $s_dict = M("s_dict")->where(array('code'=>'DICT_CRONTAB_MATE'))->find();
        $id =(int)$s_dict['realvalue'];
//        $id =621;
        $new_id = 0;
        $jon_con['id'] =array('GT',$id);
        $job_list = $m_job->where($jon_con)->limit(10)->select();
        if(empty($job_list)){
            echo "暂无数据刷新";
        }
        foreach($job_list as $k=>$v){

            $new_id = $v['id'];
            $mate_info = $m_mate->where(array('id'=>$v['mateid']))->find();

            if($mate_info['type'] == 1){
                $dog_log =  $m_dong_log->where(array('plantoonid'=>$mate_info['plantoonid'],'type'=>3))->find();
                if(empty($dog_log['id'])) { //如果没有到账记录
                    $account_info =  $m_account->where(array('userId'=>$mate_info['plantoonuserid']))->find();
                    if($account_info['dongprice'] <= 0){ //账户已经扣除完了
                        echo $mate_info['plantoonuserid']."账户冻结钱包已经为0"."<br>";
                        continue;
                    }else{  // 扣除10%
                        $rob_info = $m_rob_order->where(array('id'=>$mate_info['plantoonid']))->find();
                        if(empty($rob_info['price'])){
                            continue;
                        }
                        $shi_price =(int) ($rob_info['price'] / 10 );
                        if($shi_price > $account_info['dongprice']){
                            $shi_price = $account_info['dongprice'];
                        }
                        $account_price= array();
                        $account_price['dongprice'] = $account_info['dongprice'] - $shi_price ;
                        $account_price['totalPrice'] =$account_info['totalprice'] + $shi_price;
                        $account_price['canPrice'] =$account_info['canprice'] + $shi_price;

                        $m_account->where(array('userId'=>$mate_info['plantoonuserid']))->save($account_price);
                        echo $mate_info['plantoonuserid']."更新释放10%"."<br>";
                        $datalog =array();
                        $datalog['plantoonid'] =$mate_info['plantoonid'];
                        $datalog['type'] =3;
                        $datalog['userid'] =$mate_info['plantoonuserid'];
                        $datalog['price'] =$shi_price;
                        $datalog['leftprice'] = $account_price['totalPrice'];
                        $datalog['addtime'] =date("Y-m-d H:i:s",time());
                        $datalog['updatetime'] =date("Y-m-d H:i:s",time());
                        $m_dong_log->add($datalog);

                        $account_recordlog=array();
                        $account_recordlog['recordBody'] ="冻结钱包解冻抢单";
                        $account_recordlog['recordPrice'] =$shi_price;
                        $account_recordlog['recordNowPrice'] =$account_price['totalPrice'];
                        $account_recordlog['recordStatus'] =1 ;
                        $account_recordlog['recordType'] =0;
                        $account_recordlog['recordMold'] =9;
                        $account_recordlog['recordToUserId'] =$mate_info['plantoonuserid'];
                        $account_recordlog['recordToAccountId'] =$mate_info['plantoonuserid'];
                        $account_recordlog['createDate'] =date("Y-m-d H:i:s",time());
                        $m_s_account_record->add($account_recordlog);
                    }
                }else{

                }
//                echo $mate_info['id']."抢单过滤".$mate_info['plantoonuserid']."<br>";
                continue;
            }

            if($mate_info['plantoonid']){
                $dog_log =  $m_dong_log->where(array('plantoonid'=>$mate_info['plantoonid'],'type'=>1))->find();
                if(empty($dog_log['id'])){ //如果没有到账记录
                    $account_info =  $m_account->where(array('userId'=>$mate_info['plantoonuserid']))->find();
                    if($account_info['dongprice'] <= 0){ //账户已经扣除完了
                        echo $mate_info['plantoonuserid']."账户冻结钱包已经为0"."<br>";
                        continue;
                    }else{  // 扣除10%
                        $plant_info = $m_platoon_order->where(array('id'=>$mate_info['plantoonid']))->find();
                        if(empty($plant_info['price'])){
                            continue;
                        }
                        $shi_price =(int) ($plant_info['price'] / 10 );
                        if($shi_price > $account_info['dongprice']){
                            $shi_price = $account_info['dongprice'];
                        }
                        $account_price= array();
                        $account_price['dongprice'] = $account_info['dongprice'] - $shi_price ;
                        $account_price['totalPrice'] =$account_info['totalprice'] + $shi_price;
                        $account_price['canPrice'] =$account_info['canprice'] + $shi_price;

                        $m_account->where(array('userId'=>$mate_info['plantoonuserid']))->save($account_price);
                        echo $mate_info['plantoonuserid']."更新释放10%"."<br>";
                        $datalog =array();
                        $datalog['plantoonid'] =$mate_info['plantoonid'];
                        $datalog['type'] =1;
                        $datalog['userid'] =$mate_info['plantoonuserid'];
                        $datalog['price'] =$shi_price;
                        $datalog['leftprice'] = $account_price['totalPrice'];
                        $datalog['addtime'] =date("Y-m-d H:i:s",time());
                        $datalog['updatetime'] =date("Y-m-d H:i:s",time());
                        $m_dong_log->add($datalog);

                        $account_recordlog=array();
                        $account_recordlog['recordBody'] ="冻结钱包解冻排单";
                        $account_recordlog['recordPrice'] =$shi_price;
                        $account_recordlog['recordNowPrice'] =$account_price['totalPrice'];
                        $account_recordlog['recordStatus'] =1 ;
                        $account_recordlog['recordType'] =0;
                        $account_recordlog['recordMold'] =9;
                        $account_recordlog['recordToUserId'] =$mate_info['plantoonuserid'];
                        $account_recordlog['recordToAccountId'] =$mate_info['plantoonuserid'];
                        $account_recordlog['createDate'] =date("Y-m-d H:i:s",time());
                        $m_s_account_record->add($account_recordlog);
                    }

                }else{
                    continue;
                }
            }

        }

        if($new_id > 0){
            M("s_dict")->where(array('code'=>'DICT_CRONTAB_MATE'))->save(array('realValue'=>$new_id));
        }
    }

    // 恢复冻结钱包
    public function repairDong(){
        $s_dict = M("s_dict")->where(array('code'=>'DICT_IP'))->find();
        $ip = $_SERVER['REMOTE_ADDR'];
        if($_GET['key'] =='d327b14ffca9e4c4de2' && I('tel')){
            if($s_dict['displayvalue'] == $ip){
                $s_user =  M("s_user")->where(array('userAccount'=>I('tel')))->select();
            }else{
                echo "IP地址错误";
                echo $ip;
                print_r($s_dict['displayvalue']);
            }
        }else{
            echo "秘钥错误";
        }


    }
}


?>
