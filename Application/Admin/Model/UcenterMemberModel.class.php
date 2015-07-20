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
 * 统一用户成员信息表
 */
class UcenterMemberModel extends Model {
		
	protected $_auto = array(
		array('status',1,self::MODEL_INSERT),
		array('status',1,self::MODEL_INSERT),
	);
	
	
}