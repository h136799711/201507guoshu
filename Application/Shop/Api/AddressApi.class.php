<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;
use Common\Api\Api;
use Common\Model\AddressModel;

class AddressApi extends Api{
    /**
     * 添加
     */
    const ADD = "Shop/Address/add";
    /**
     * 保存
     */
    const SAVE = "Shop/Address/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/Address/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/Address/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/Address/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/Address/getInfo";
	protected function _init(){
		$this->model = new AddressModel();
	}
	
	
}

