<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------



namespace Weixin\Api;

use Common\Api\Api;
use Weixin\Model\WxaccountModel;

class WxaccountApi extends Api{


    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Weixin/Wxaccount/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Weixin/Wxaccount/add";
    /**
     * 保存
     */
    const SAVE = "Weixin/Wxaccount/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Weixin/Wxaccount/saveByID";

    /**
     * 删除
     */
    const DELETE = "Weixin/Wxaccount/delete";

    /**
     * 查询
     */
    const QUERY = "Weixin/Wxaccount/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Weixin/Wxaccount/getInfo";

	protected function _init(){
		$this->model = new WxaccountModel();
	}
	
}
