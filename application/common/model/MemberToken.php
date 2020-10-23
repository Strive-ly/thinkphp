<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 8:09 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;

/*CREATE TABLE `dr_member_token` (
  `token` varchar(50) NOT NULL COMMENT 'Token',
  `account` varchar(45) NOT NULL,
  `member_id` int unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `create_time` int unsigned NOT NULL COMMENT '创建时间',
  `expire_time` int unsigned NOT NULL COMMENT '过期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员授权信息';*/

class MemberToken extends Common
{
    public function initialize()
    {
        parent::initialize();
        $this->pk = 'member_id';
        $this->name = 'member_token';
        $this->table = config('database.prefix') . $this->name;
    }

    public function getToken($account, $member_id, $is_update = false)
    {
        $create_time = NEW_TIME;
        $expire_time = $create_time + (3600 * 24 * 10);
        $token = $this->where($this->pk, $member_id)->value('token');
        if ($is_update && !empty($token)){
            $this->where($this->pk, $member_id)->setField('expire_time', $expire_time);
        }else{
            $token = md5(base64_encode($account . $member_id . $expire_time));
            $save_data = [
                'token'=>$token,
                'account'=>$account,
                'member_id'=>$member_id,
                'create_time'=>$create_time,
                'expire_time'=>$expire_time,
            ];
            $this->where($this->pk, $member_id)->delete();
            $this->insert($save_data);
        }
        return $token;
    }

    public function tokenAuth($token)
    {
        $token_data = $this->getFind(['token'=>$token]);
        if (!empty($token_data)){
            if ($token_data['expire_time'] > NEW_TIME){
                $this->getToken($token_data['account'], $token_data['member_id'], true);
                return $token_data;
            }
        }
        return false;
    }

}