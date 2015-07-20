<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;
use Common\Api\Api;
use Shop\Model\CategoryPropvalueModel;

class CategoryPropvalueApi extends Api{

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/CategoryPropvalue/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/CategoryPropvalue/add";
    /**
     * 保存
     */
    const SAVE = "Shop/CategoryPropvalue/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/CategoryPropvalue/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/CategoryPropvalue/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/CategoryPropvalue/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/CategoryPropvalue/getInfo";

	protected function _init(){
		$this->model = new CategoryPropvalueModel();
	}
	
}

