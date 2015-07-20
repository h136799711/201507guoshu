<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;
use Common\Api\Api;
use Admin\Model\AuthRuleModel;
class AuthRuleApi extends Api{
    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Admin/AuthRule/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Admin/AuthRule/add";
    /**
     * 保存
     */
    const SAVE = "Admin/AuthRule/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Admin/AuthRule/saveByID";

    /**
     * 删除
     */
    const DELETE = "Admin/AuthRule/delete";

    /**
     * 查询
     */
    const QUERY = "Admin/AuthRule/query";

    /**
     * 查询所有模块
     */
    const ALL_MODULES = "Admin/AuthRule/allModules";

	protected function _init(){
		$this->model = new AuthRuleModel();
	}
	
	/*
	 * 获取不重复module字段数据
	 *  
	 */
	public function allModules(){
		$result = $this->model->distinct(true)->field('module')->select();
		if($result === false){			
			return $this->apiReturnErr($this->model->getDbError());
		}else{
			return $this->apiReturnSuc($result);
		}
	}
		
}
