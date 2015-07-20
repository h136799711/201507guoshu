<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Shop\Api;

use Common\Api\Api;
use Shop\Model\OrdersInfoViewModel;

class OrdersInfoViewApi extends Api
{

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/OrdersInfoView/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/OrdersInfoView/add";
    /**
     * 保存
     */
    const SAVE = "Shop/OrdersInfoView/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/OrdersInfoView/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/OrdersInfoView/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/OrdersInfoView/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/OrdersInfoView/getInfo";

    protected function _init()
    {
        $this->model = new OrdersInfoViewModel();
    }


}
