<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Api;
use Common\Api\Api;

use Admin\Model\UserPictureModel;

class UserPictureApi extends Api{


    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Admin/UserPicture/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Admin/UserPicture/add";
    /**
     * 保存
     */
    const SAVE = "Admin/UserPicture/save";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Admin/UserPicture/getInfo";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Admin/UserPicture/saveByID";

    /**
     * 删除
     */
    const DELETE = "Admin/UserPicture/delete";

    /**
     * 查询
     */
    const QUERY = "Admin/UserPicture/query";

	protected function _init(){
		$this->model = new UserPictureModel();
	}
	
}
