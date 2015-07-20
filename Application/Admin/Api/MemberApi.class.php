<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;

use Admin\Model\MemberModel;
use Common\Api\Api;

class MemberApi extends Api{

    /**
     * 添加
     */
    const ADD = "Admin/Member/add";
    /**
     * 保存
     */
    const SAVE = "Admin/Member/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Admin/Member/saveByID";

    /**
     * 删除
     */
    const DELETE = "Admin/Member/delete";

    /**
     * 查询
     */
    const QUERY = "Admin/Member/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Admin/Member/getInfo";
    /**
     *
     */
    const QUERY_BY_GROUP = "Admin/Member/queryByGroup";

	//初始化
	protected function _init(){
		$this->model = new MemberModel();
	}
	
	public function queryByGroup($map,$page)
    {
        $result = $this->model->queryByGroup($map, $page);
        if ($result === false) {
            return $this->apiReturnErr($this->model->getDbError());
        } else {
            return $this->apiReturnSuc($result);
        }
    }

}
