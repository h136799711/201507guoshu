<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;
use Common\Api\Api;
use \Shop\Model\SkuvalueModel;

class SkuvalueApi extends Api{


    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/Skuvalue/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/Skuvalue/add";
    /**
     * 保存
     */
    const SAVE = "Shop/Skuvalue/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/Skuvalue/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/Skuvalue/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/Skuvalue/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/Skuvalue/getInfo";

	protected function _init(){
		$this->model = new SkuvalueModel();
	}
}

