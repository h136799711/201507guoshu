<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Admin\Model;
use Think\Model;

/**
 * 基本用户成员信息表
 */
class MemberModel extends Model {
	
	/**
	 * 前缀
	 */
	protected $tablePrefix = "common_";
	
	/* 
	 * 用户模型自动完成 
	 * 
	 * */
	protected $_auto = array(
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('reg_ip', 'getLongIp', self::MODEL_INSERT, 'callback'),
		array('last_login_time', NOW_TIME, self::MODEL_INSERT),
		array('last_login_ip', 'getLongIp', self::MODEL_INSERT, 'callback'),
		array('update_time', NOW_TIME,self::MODEL_BOTH),
		array('status', '1', self::MODEL_INSERT),
	);


    public function getLongIp(){
        return ip2long(get_client_ip());
    }
	
}