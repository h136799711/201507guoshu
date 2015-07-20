<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Admin\Api;

use Common\Api\Api;
use Common\Model\DatatreeModel;

class DatatreeApi extends Api{


    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Admin/Datatree/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Admin/Datatree/add";
    /**
     * 保存
     */
    const SAVE = "Admin/Datatree/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Admin/Datatree/saveByID";

    /**
     * 删除
     */
    const DELETE = "Admin/Datatree/delete";

    /**
     * 查询
     */
    const QUERY = "Admin/Datatree/query";

	protected function _init(){
		$this->model = new DatatreeModel();
	}
}

