<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;

use \Common\Api\Api;
use \Common\Model\SuggestModel;

class SuggestApi extends  Api{
    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/Suggest/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/Suggest/add";
    /**
     * 保存
     */
    const SAVE = "Shop/Suggest/save";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/Suggest/getInfo";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/Suggest/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/Suggest/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/Suggest/query";

	protected function _init(){
		$this->model = new SuggestModel();
	}
}

