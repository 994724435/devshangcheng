/*
Navicat MySQL Data Transfer

Source Server         : 本机
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : cs_365_6

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-01-06 20:35:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `p_article`
-- ----------------------------
DROP TABLE IF EXISTS `p_article`;
CREATE TABLE `p_article` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `type` int(11) DEFAULT '1' COMMENT '1首页 2公告 3值班团队 4分析专家 5公司简介',
  `cont` text,
  `addtime` varchar(128) DEFAULT NULL,
  `addymd` varchar(128) DEFAULT NULL,
  `admin` varchar(64) DEFAULT NULL,
  `num` int(11) DEFAULT '1',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_article
-- ----------------------------
INSERT INTO `p_article` VALUES ('1', '公司简介', '1', '1<img src=\"/devmuchang/Public/Admin/js/attached/image/20171121/20171121124432_79040.png\" alt=\"\" /><img src=\"/dev/devmuchang/Public/Admin/js/attached/image/20171125/20171125135922_56452.jpg\" alt=\"\" />', '2017-11-25 13:59:24', '2017-11-25', 'admin', '1');
INSERT INTO `p_article` VALUES ('6', '分析专家李云龙标题', '2', '1<img src=\"/dev/devmuchang/Public/Admin/js/attached/image/20171125/20171125135939_82358.jpg\" alt=\"\" />', '2017-11-25 13:59:40', '2017-11-25', 'admin', '1');
INSERT INTO `p_article` VALUES ('7', '公告', '2', '1', '2017-11-21 20:37:22', '2017-11-21', 'admin', '1');

-- ----------------------------
-- Table structure for `p_banner`
-- ----------------------------
DROP TABLE IF EXISTS `p_banner`;
CREATE TABLE `p_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(256) DEFAULT NULL,
  `coment` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_banner
-- ----------------------------

-- ----------------------------
-- Table structure for `p_cart`
-- ----------------------------
DROP TABLE IF EXISTS `p_cart`;
CREATE TABLE `p_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `productid` int(11) NOT NULL,
  `num` int(11) DEFAULT NULL,
  `type` int(1) DEFAULT '1' COMMENT '1购物车  2 收藏',
  `addtime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_cart
-- ----------------------------

-- ----------------------------
-- Table structure for `p_incomelog`
-- ----------------------------
DROP TABLE IF EXISTS `p_incomelog`;
CREATE TABLE `p_incomelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT '1' COMMENT '1分红收益2充值 3静态提现  4升级  5 注册下级 6下单购买 7积分体现 8话费充值 9 回馈奖 10积分商城购买',
  `state` int(11) DEFAULT '1' COMMENT '1收入   2支出 3失败',
  `reson` varchar(255) DEFAULT NULL COMMENT '原因',
  `addymd` date DEFAULT NULL,
  `addtime` int(12) DEFAULT NULL,
  `orderid` varchar(100) DEFAULT '1' COMMENT '1 卖方 2 买方',
  `userid` int(11) DEFAULT NULL,
  `income` varchar(64) DEFAULT '0' COMMENT '金额',
  `cont` varchar(1000) NOT NULL COMMENT '后台备注',
  `username` varchar(100) DEFAULT NULL,
  `tel` varchar(100) DEFAULT NULL,
  `commitid` varchar(64) DEFAULT '1',
  `weixin` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=695 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_incomelog
-- ----------------------------
INSERT INTO `p_incomelog` VALUES ('669', '6', '2', '下单购买', '2017-11-30', '1512031727', '87', '1', '200', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('670', '6', '2', '下单购买', '2017-11-30', '1512032076', '88', '1', '100', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('668', '6', '2', '下单购买', '2017-11-30', '1512031063', '86', '1', '100', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('671', '10', '2', '下单购买', '2017-11-30', '1512032178', '89', '1', '100', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('672', '2', '1', '充值', '2017-11-30', '1512032223', '201711301657033082', '1', '100', 'http://chongzhi.dev.com/index.php/Home/User/recharge.html?', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('673', '7', '0', '余额提现', '2017-11-30', '1512032386', '1', '1', '100', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('674', '2', '0', '充值', '2017-11-30', '1512033941', '201711301725418707', '1', '100', 'http://chongzhi.dev.com/index.php/Home/User/recharge.html?', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('685', '1', '1', '分红收益', '2017-11-30', '1512047655', '0', '1', '2.00', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('686', '8', '2', '话费充值', '2017-12-01', '1512139024', '1512139024', '1', '10', '', null, '18883287644', '1', null);
INSERT INTO `p_incomelog` VALUES ('687', '4', '2', '会员升级(代金券)', '2017-12-02', '1512175550', '1', '1', '1399', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('688', '4', '2', '会员升级(代金券)', '2017-12-02', '1512175727', '1', '1', '2699', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('689', '4', '2', '会员升级(代金券)', '2017-12-02', '1512175997', '1', '1', '1399', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('690', '4', '2', '会员升级(代金券)', '2017-12-03', '1512265238', '1', '1', '700', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('691', '11', '1', null, '2017-12-03', '1512266843', '1', '1', '10', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('692', '11', '1', null, '2017-12-03', '1512267009', '1', '1', '1', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('693', '11', '1', '个人工单', '2017-12-03', '1512267737', '1', '1', '100', '', null, null, '1', null);
INSERT INTO `p_incomelog` VALUES ('694', '11', '1', '个人工单', '2017-12-03', '1512267793', '1', '1', '10', '', null, '100', '1', null);

-- ----------------------------
-- Table structure for `p_login`
-- ----------------------------
DROP TABLE IF EXISTS `p_login`;
CREATE TABLE `p_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `pwd` text CHARACTER SET utf8,
  `addymd` date DEFAULT NULL,
  `addtime` int(11) DEFAULT NULL,
  `ip` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of p_login
-- ----------------------------
INSERT INTO `p_login` VALUES ('1', 'admin', '123asd', '2017-09-16', '1505552484', null);
INSERT INTO `p_login` VALUES ('2', 'admin', '123asd', '2017-09-16', '1505552539', '127.0.0.1');

-- ----------------------------
-- Table structure for `p_menber`
-- ----------------------------
DROP TABLE IF EXISTS `p_menber`;
CREATE TABLE `p_menber` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `pwd` varchar(100) DEFAULT NULL,
  `tel` varchar(64) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `type` int(4) DEFAULT '1' COMMENT '1普通 2高级 3豪华 4至尊',
  `dongbag` varchar(50) DEFAULT '' COMMENT '商城积分',
  `jingbag` varchar(50) DEFAULT '0' COMMENT '智能余额',
  `djbag` varchar(50) DEFAULT '0' COMMENT '冻结钱包',
  `fuid` int(11) DEFAULT '0' COMMENT '注册上家',
  `fuids` varchar(1000) DEFAULT NULL COMMENT '上家',
  `two` int(11) DEFAULT '0' COMMENT '二级父类',
  `three` int(11) DEFAULT '0' COMMENT '三级父类',
  `four` int(11) DEFAULT '0' COMMENT '四级父类',
  `addtime` int(12) DEFAULT NULL,
  `addymd` date DEFAULT NULL,
  `pwd2` varchar(255) DEFAULT NULL,
  `chargebag` varchar(50) DEFAULT '0' COMMENT '个人钱包',
  `realname` varchar(100) DEFAULT NULL COMMENT '真实姓名',
  `zhifubao` varchar(100) DEFAULT NULL COMMENT '支付宝账号',
  `zhifubaoname` varchar(100) DEFAULT NULL COMMENT '支付宝姓名',
  `weixin` varchar(64) DEFAULT NULL,
  `bank` varchar(100) DEFAULT NULL COMMENT '银行卡号',
  `bankname` varchar(64) DEFAULT NULL COMMENT '银行卡姓名',
  `bankfrom` varchar(100) DEFAULT NULL COMMENT '银行卡开户行',
  `isdelete` int(1) DEFAULT '0' COMMENT '0 未经用 1禁用',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_menber
-- ----------------------------
INSERT INTO `p_menber` VALUES ('1', '100', '1', '100', null, '4', '1', '1', '0.00', '0', '1,', null, '0', '0', null, null, '1', '110.00', 'fsda', '1121', '121', '12121', null, null, null, '0');
INSERT INTO `p_menber` VALUES ('2', '101', '1', '101', null, '1', '4.00', '0.00', '0.00', '1', '1,2,', null, '0', '0', '1502892880', '2017-08-16', '1', '716.00', null, null, null, null, null, null, null, '0');
INSERT INTO `p_menber` VALUES ('34', '102', '1', '102', null, '1', '0', '0.00', '0.00', '2', '1,2,34,', '1', '0', '0', null, null, '1', '0', null, null, null, null, null, null, null, '0');
INSERT INTO `p_menber` VALUES ('35', '103', '1', '103', null, '1', '4.00', '0.00', '0.00', '34', '1,2,34,35,', '2', '1', '0', null, null, '1', '16.00', null, null, null, null, null, null, null, '0');
INSERT INTO `p_menber` VALUES ('36', '104', '1', '104', null, '1', '0', '0.00', '0.00', '35', '1,2,34,35,36,', '34', '2', '1', null, null, '1', '0', null, null, null, null, null, null, null, '0');
INSERT INTO `p_menber` VALUES ('37', '105', '1', '105', null, '1', '0', '0.00', '0.00', '36', '1,2,34,35,36,37,', '35', '34', '2', null, null, '1', '0', null, null, null, null, null, null, null, '0');
INSERT INTO `p_menber` VALUES ('39', '18883287644', '1', '18883287644', null, '1', '0', '0', '0', '0', '39,', '0', '0', '0', '1512141220', '2017-12-01', '1', '0', null, null, null, null, null, null, null, '0');

-- ----------------------------
-- Table structure for `p_orderlog`
-- ----------------------------
DROP TABLE IF EXISTS `p_orderlog`;
CREATE TABLE `p_orderlog` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '用户id',
  `productid` int(11) NOT NULL,
  `productname` varchar(64) DEFAULT NULL,
  `productmoney` varchar(32) DEFAULT NULL COMMENT '产品带来的利润',
  `state` int(1) NOT NULL DEFAULT '0' COMMENT '0待支付 1收益中 2已完成',
  `orderid` varchar(128) NOT NULL COMMENT '订单id',
  `addtime` int(12) DEFAULT NULL,
  `num` int(5) DEFAULT NULL COMMENT '购买数量',
  `price` varchar(40) DEFAULT NULL COMMENT '购买单价',
  `totals` varchar(40) DEFAULT NULL,
  `addymd` date DEFAULT NULL,
  `type` int(2) DEFAULT '1' COMMENT '1买地  2 1000买幼崽 3 成年5000 4母牦牛10000  10买商城物品',
  `option` varchar(1000) DEFAULT NULL COMMENT '其他说明',
  PRIMARY KEY (`logid`)
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_orderlog
-- ----------------------------
INSERT INTO `p_orderlog` VALUES ('88', '1', '2', '钱付贰号', null, '1', '1512032076', '1512032076', '1', '100', '100', '2017-11-30', '2', '2');
INSERT INTO `p_orderlog` VALUES ('86', '1', '2', '钱付贰号', '100', '2', '1512031063', '1512031063', '1', '100', '100', '2017-11-30', '10', '');
INSERT INTO `p_orderlog` VALUES ('87', '1', '3', '钱付叁号', '100', '1', '1512031726', '1512031726', '1', '200', '200', '2017-11-30', '1', '1');
INSERT INTO `p_orderlog` VALUES ('89', '1', '2', '钱付贰号', null, '1', '1512032178', '1512032178', '1', '100', '100', '2017-11-30', '2', '2');

-- ----------------------------
-- Table structure for `p_product`
-- ----------------------------
DROP TABLE IF EXISTS `p_product`;
CREATE TABLE `p_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '产品名',
  `cont` text COMMENT '产品描述',
  `type` int(1) DEFAULT '1' COMMENT '1',
  `pic` varchar(255) DEFAULT NULL COMMENT '产品图片',
  `price` decimal(10,0) DEFAULT NULL COMMENT '售卖价格',
  `ftype` int(10) DEFAULT NULL COMMENT '父级别分类',
  `ctype` int(10) DEFAULT NULL COMMENT '子类类型',
  `istui` int(1) DEFAULT '0' COMMENT '0 不推荐   1推荐',
  `iste` int(1) DEFAULT '0' COMMENT '0非特色  1特色',
  `isjing` int(1) DEFAULT '0' COMMENT '0不是精品  1精品',
  `state` int(3) DEFAULT '1' COMMENT '1上架  2下架',
  `addtime` varchar(100) DEFAULT NULL,
  `salenum` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_product
-- ----------------------------
INSERT INTO `p_product` VALUES ('12', '法国加力果12个装 进口新鲜水果 嘎啦苹果 包邮3', '31241234124123412', '1', '/Public/Uploads/2019-01-02/5c2cc1183e08a.png', '111', '1', '6', '1', '1', '0', '1', '2019-01-06 10:29:53', '0');
INSERT INTO `p_product` VALUES ('9', '特价武夷山桐木关正山小种红茶高档礼盒1', '', '1', '/Public/Uploads/2019-01-02/5c2cbdab50395.png', '12', '1', '6', '1', '0', '1', '1', '2019-01-06 10:30:01', '0');
INSERT INTO `p_product` VALUES ('10', '特价武夷山桐木关正山小种红茶高档礼盒2', '123adsfasdfasdf', '1', '/Public/Uploads/2019-01-02/5c2cbe49e0073.png', '1', '1', '6', '0', '1', '1', '1', '2019-01-06 10:30:12', '0');
INSERT INTO `p_product` VALUES ('11', '法国加力果12个装 进口新鲜水果 嘎啦苹果 包邮', '', '1', '/Public/Uploads/2019-01-02/5c2cbeebcf33e.png', '12', '1', '6', '1', '1', '1', '1', '2019-01-06 10:30:20', '0');
INSERT INTO `p_product` VALUES ('13', '13000000003', '32312423', '1', 'http://df.cqyuyan.cn/_2019-01-06_5c31990d78d69.png', '12', '1', '6', '0', '0', '0', '1', '2019-01-06 13:58:37', '0');
INSERT INTO `p_product` VALUES ('14', '104', '21312', '1', 'http://df.cqyuyan.cn/_2019-01-06_5c3198ddcc021.png', '24', '1', '6', '0', '0', '0', '1', '2019-01-06 13:57:50', '0');

-- ----------------------------
-- Table structure for `p_product_banner`
-- ----------------------------
DROP TABLE IF EXISTS `p_product_banner`;
CREATE TABLE `p_product_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proid` int(11) DEFAULT NULL COMMENT '产品ID',
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `addtime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_product_banner
-- ----------------------------
INSERT INTO `p_product_banner` VALUES ('7', '12', null, 'http://df.cqyuyan.cn/_2019-01-06_5c31b0a9148c2.png', '2019-01-06 15:39:21');

-- ----------------------------
-- Table structure for `p_type`
-- ----------------------------
DROP TABLE IF EXISTS `p_type`;
CREATE TABLE `p_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL COMMENT '父ID',
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  `pic` varchar(255) DEFAULT NULL COMMENT '图片地址',
  `state` int(1) DEFAULT '1' COMMENT '状态1 有效',
  `sort` int(5) DEFAULT NULL COMMENT '排序',
  `addtime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_type
-- ----------------------------
INSERT INTO `p_type` VALUES ('1', '0', '粮油大米', null, '1', '1', '2019-01-02 21:16:00');
INSERT INTO `p_type` VALUES ('2', '0', '烟酒饮料', null, '1', '2', '2019-01-02 21:16:01');
INSERT INTO `p_type` VALUES ('3', '0', '休闲食品', null, '1', '3', '2019-01-02 21:16:02');
INSERT INTO `p_type` VALUES ('4', '0', '个人洗护', null, '1', '4', '2019-01-02 21:16:03');
INSERT INTO `p_type` VALUES ('5', '0', '家居家纺', null, '1', '5', '2019-01-02 21:16:05');
INSERT INTO `p_type` VALUES ('6', '1', '米面粮油', null, '1', null, '2019-01-02 21:10:52');
INSERT INTO `p_type` VALUES ('7', '1', '食用油', null, '1', null, '2019-01-02 21:11:01');
INSERT INTO `p_type` VALUES ('8', '1', '厨房调料', null, '1', null, '2019-01-02 21:11:27');
INSERT INTO `p_type` VALUES ('9', '1', '特色干货', null, '1', null, '2019-01-02 21:11:43');

-- ----------------------------
-- Table structure for `p_user`
-- ----------------------------
DROP TABLE IF EXISTS `p_user`;
CREATE TABLE `p_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT '登录名',
  `openid` varchar(255) DEFAULT NULL COMMENT '微信ID',
  `nickname` varchar(255) DEFAULT NULL COMMENT '微信昵称',
  `address` varchar(255) DEFAULT NULL COMMENT '微信地址',
  `userface` varchar(255) DEFAULT NULL COMMENT '维信头像',
  `addtime` varchar(255) DEFAULT NULL COMMENT '注册时间',
  `manager` int(2) DEFAULT '1' COMMENT '0 禁用账号 1管理员 2 超级管理员',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of p_user
-- ----------------------------
INSERT INTO `p_user` VALUES ('1', '123asd', 'admin', null, null, null, null, '2017-03-10 13:56:27', '2');
INSERT INTO `p_user` VALUES ('2', '123456', 'admin2', null, null, null, null, '2017-03-10 13:56:27', '2');
