<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Api;
use Common\Api\Api;
use Weixin\Model\WxmenuModel;

class WxmenuApi extends  Api{

    /**
    * 查询，不分页
    */
    const QUERY_NO_PAGING = "Weixin/Wxmenu/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Weixin/Wxmenu/add";
    /**
     * 保存
     */
    const SAVE = "Weixin/Wxmenu/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Weixin/Wxmenu/saveByID";

    /**
     * 删除
     */
    const DELETE = "Weixin/Wxmenu/delete";

    /**
     * 查询
     */
    const QUERY = "Weixin/Wxmenu/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Weixin/Wxmenu/getInfo";

	protected function _init(){
		$this->model = new WxmenuModel();
	}
	
	
	
}
