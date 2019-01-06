<?php
return array(
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => '127.0.0.1', // 服务器地址
	'DB_NAME'   => 'cs_365_6', // 数据库名
	'DB_USER'   => 'root', // 用户名
	'DB_PWD'    => 'mysql', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => '', // 数据库表前缀
    //支付宝 支付配置
    'ALI_CONFIG'  => array(
        'gatewayUrl'            => 'https://openapi.alipay.com/gateway.do',//支付宝网关（固定)'
        'appId'                 => '2017120800466009',//APPID即创建应用后生成
        //由开发者自己生成: 请填写开发者私钥去头去尾去回车，一行字符串
        'rsaPrivateKey'         =>  '',
        //支付宝公钥，由支付宝生成: 请填写支付宝公钥，一行字符串
        'alipayrsaPublicKey'    =>  '',
        'notifyUrl'             => 'http://ydc.shuijingjiafang.com', // 支付成功通知地址
        'returnUrl'             => 'http://ydc.shuijingjiafang.com', // 支付后跳转地址
        'returnPcUrl'           => 'http://ydc.shuijingjiafang.com', // PC端扫码支付后跳转地址
    ),
    'ACCESSKEY' => 'aTzHI3UGTwGRfea6vcSSNtTXlZs0y8_K2XEIzcRa',//你的accessKey
    'SECRETKEY' => 'wyPrwVsm5fVZ_-bP0-EKv1bi-zKEi3gsefo-2Wh1',//你的secretKey
    'BUCKET' => 'cs-365',//上传的空间
    'DOMAIN'=>'http://df.cqyuyan.cn/',//空间绑定的域名
    'UPLOAD_FILE_QINIU'     => array (
        'maxSize'          => 5 * 1024 * 1024,//文件大小
        'rootPath'          => '/',
        'savePath'          => '/',// 文件上传的保存路径
        'saveName'          => array ('uniqid', ''),
        'exts'              => ['zip', 'rar', 'txt', 'doc', 'docx', 'xlsx', 'xls', 'pptx', 'pdf', 'chf', 'png', 'jpg', 'jpeg'],  // 设置附件上传类型
        'driver'            => 'Qiniu', //七牛驱动
        'driverConfig'      => array (
            'secretKey'        => 'wyPrwVsm5fVZ_-bP0-EKv1bi-zKEi3gsefo-2Wh1',
            'accessKey'        => 'aTzHI3UGTwGRfea6vcSSNtTXlZs0y8_K2XEIzcRa',
            'domain'           => 'df.cqyuyan.cn',
            'bucket'           => 'cs-365',
        )
    )
//	'URL_MODEL' => '2',
//	 'DEFAULT_MODULE' => 'Index'
);


