<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;

use Admin\Model\AuthGroupModel;
use Common\Api\Api;

class AuthGroupApi extends Api{

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Admin/AuthGroup/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Admin/AuthGroup/add";
    /**
     * 保存
     */
    const SAVE = "Admin/AuthGroup/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Admin/AuthGroup/saveByID";

    /**
     * 删除
     */
    const DELETE = "Admin/AuthGroup/delete";

    /**
     * 查询
     */
    const QUERY = "Admin/AuthGroup/query";

    //初始化
	protected function _init(){
		$this->model = new AuthGroupModel();
	}
	
	/**
	 * 写入用户组的规则
	 */
	public function writeRules($groupid,$rules){
		if(empty($groupid)){
			return $this->apiReturnErr("用户组id错误");
		}
		if(!is_string($rules)){
			return $this->apiReturnErr("规则参数错误");
		}
		$result = $this->model->where(array('id'=>$groupid))->save(array('rules'=>$rules));
		if($result === false){			
			return $this->apiReturnErr($this->model->getDbError());
		}else{
			return $this->apiReturnSuc($result);
		}
		
	}
	
	/**
	 * 写入用户组的菜单列表
	 */
	public function writeMenuList($groupid,$menuList){
		if(empty($groupid)){
			return $this->apiReturnErr("用户组id错误");
		}
		if(!is_string($menuList)){
			return $this->apiReturnErr("规则参数错误");
		}
		$result = $this->model->where(array('id'=>$groupid))->save(array('menulist'=>$menuList));
		
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		}else{
			return $this->apiReturnSuc($result);
		}
		
	}
	
	
}
