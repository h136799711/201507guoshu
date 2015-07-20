<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;

use Common\Api\Api;

use Shop\Model\ProductGroupModel;

class ProductGroupApi extends  Api{

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/ProductGroup/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/ProductGroup/add";
    /**
     * 保存
     */
    const SAVE = "Shop/ProductGroup/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/ProductGroup/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/ProductGroup/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/ProductGroup/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/ProductGroup/getInfo";

	protected function _init(){
		$this->model = new ProductGroupModel();
	}
	
}

