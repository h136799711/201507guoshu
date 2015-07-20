<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;

use Shop\Model\OrderStatusHistoryModel;
use Common\Api\Api;

class OrderStatusHistoryApi extends Api{

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/OrderStatusHistory/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/OrderStatusHistory/add";
    /**
     * 保存
     */
    const SAVE = "Shop/OrderStatusHistory/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/OrderStatusHistory/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/OrderStatusHistory/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/OrderStatusHistory/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/OrderStatusHistory/getInfo";


	protected function _init(){
		$this->model = new OrderStatusHistoryModel();
	}	

	
}

