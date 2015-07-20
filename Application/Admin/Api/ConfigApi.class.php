<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;
use Common\Api\Api;
use Admin\Model\ConfigModel;

class ConfigApi extends Api{



    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Admin/Config/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Admin/Config/add";
    /**
     * 保存
     */
    const SAVE = "Admin/Config/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Admin/Config/saveByID";

    /**
     * 删除
     */
    const DELETE = "Admin/Config/delete";

    /**
     * 查询
     */
    const QUERY = "Admin/Config/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Admin/Config/getInfo";

    /**
     * 配置设置
     */
    const SET = "Admin/Config/set";

	protected function _init(){
		$this->model = new ConfigModel();
	}
	
	/**
	 * 设置
	 */
	public function set($config){
		$result = $this->model->set($config);
		
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		} 
		else{
			return $this->apiReturnSuc($result);
		}
	}
	
}
