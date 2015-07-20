<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;
use Common\Api\Api;
use Shop\Model\CategoryModel;


class CategoryApi extends Api{

    /**
     * 添加
     */
    const ADD = "Shop/Category/add";
    /**
     * 保存
     */
    const SAVE = "Shop/Category/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/Category/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/Category/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/Category/query";
    /**
     * 不分页，查询
     */
    const QUERY_NO_PAGING = "Shop/Category/queryNoPaging";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/Category/getInfo";

	protected function _init(){
		
		$this->model = new CategoryModel();
		
	}
}

