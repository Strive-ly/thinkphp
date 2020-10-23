<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 8:09 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;

/*CREATE TABLE `dr_member` (
  `member_id` int unsigned NOT NULL AUTO_INCREMENT,
  `account` int unsigned NOT NULL COMMENT '系统账户',
  `email` varchar(45) NOT NULL COMMENT '邮箱',
  `mobile` varchar(11) NOT NULL COMMENT '手机号',
  `nickname` varchar(20) NOT NULL COMMENT '昵称',
  `face` varchar(255) NOT NULL COMMENT '头像',
  `alias` char(32) NOT NULL COMMENT '推送别名',
  `token` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '登录授权token',
  `app_type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '设备类型：1安卓，2IOS，3微信小程序，4支付宝小程序',
  `app_version` varchar(45) NOT NULL COMMENT '客服端版本',
  `device_type` varchar(45) NOT NULL COMMENT '手机机型',
  `create_ip` char(15) NOT NULL,
  `last_ip` char(15) NOT NULL,
  `create_time` int NOT NULL,
  `last_time` int NOT NULL,
  `update_time` int NOT NULL,
  `sex` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '1,男，2,女',
  `state` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0正常。1拉黑，2已锁定，3冻结',
  `status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0离线。1在线',
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=105631 DEFAULT CHARSET=utf8 COMMENT='会员';*/

class Member extends Common
{
    public function initialize()
    {
        parent::initialize();
        $this->pk = 'member_id';
        $this->name = 'member';
        $this->table = config('database.prefix') . $this->name;
    }

}