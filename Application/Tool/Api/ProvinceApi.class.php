<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Tool\Api;
use Common\Api\Api;
use Tool\Model\ProvinceModel;


class ProvinceApi extends Api{
    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Tool/Province/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Tool/Province/add";
    /**
     * 保存
     */
    const SAVE = "Tool/Province/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Tool/Province/saveByID";

    /**
     * 删除
     */
    const DELETE = "Tool/Province/delete";

    /**
     * 查询
     */
    const QUERY = "Tool/Province/query";

    /**
     * 查询一条数据
     */
    const GET_INFO = "Tool/Province/getInfo";

	protected function _init(){
		$this->model = new ProvinceModel();
	}
}
