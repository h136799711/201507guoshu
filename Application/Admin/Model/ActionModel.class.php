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
 * 行为模型
 * @author 贝贝 <hebiduhebi@163.com>
 */

class ActionModel extends Model {
	
	/**
	 * 1.  用户登录
	 */
	const UserLogin = "user_login";
	
	/**
	 * 2. 发布文章
	 */
	const AddArticle = "add_article";
	
	/**
	 * 3. 评论文章
	 */
	const Review = "review";
	
	/**
	 * 4. 新增或修改配置
	 */
	const UpdateConfig = "update_config";
	
	
	
	protected $tablePrefix = "common_";

    /* 自动验证规则 */
    protected $_validate = array(
        array('name', 'require', '行为标识必须', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('name', '/^[a-zA-Z]\w{0,39}$/', '标识不合法', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('name', '', '标识已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),
        array('title', 'require', '标题不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('title', '1,80', '标题长度不能超过80个字符', self::MUST_VALIDATE, 'length', self::MODEL_INSERT),
        array('remark', 'require', '行为描述不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
        array('remark', '1,140', '行为描述不能超过140个字符', self::MUST_VALIDATE, 'length', self::MODEL_INSERT),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 1, self::MODEL_INSERT, 'string'),
        array('update_time', 'time', self::MODEL_BOTH, 'function'),
    );

    

}
