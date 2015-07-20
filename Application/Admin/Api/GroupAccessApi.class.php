<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------



namespace Admin\Api;

use Common\Model\GroupAccessModel;

class GroupAccessApi extends \Common\Api\Api{



    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Admin/GroupAccess/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Admin/GroupAccess/add";
    /**
     * 保存
     */
    const SAVE = "Admin/GroupAccess/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Admin/GroupAccess/saveByID";

    /**
     * 删除
     */
    const DELETE = "Admin/GroupAccess/delete";

    /**
     * 查询
     */
    const QUERY = "Admin/GroupAccess/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Admin/GroupAccess/getInfo";

	protected function _init(){
		$this->model = new GroupAccessModel();
	}
	
}
