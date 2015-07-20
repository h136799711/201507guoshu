<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Api;

use Common\Api\Api;
use Weixin\Model\WxreplyTextModel;

class WxreplyTextApi extends Api{

    /**
     * 查询，不分页
     */
    const QUERY = "Weixin/WxreplyText/query";
    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Weixin/WxreplyText/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Weixin/WxreplyText/add";
    /**
     * 保存
     */
    const SAVE = "Weixin/WxreplyText/save";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Weixin/WxreplyText/getInfo";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Weixin/WxreplyText/saveByID";

    /**
     * 删除
     */
    const DELETE = "Weixin/WxreplyText/delete";

    /**
     * 获取文本回复的所有不重复关键词
     */
    const GET_KEYWORDS = "Weixin/WxreplyText/getKeywords";

	protected function _init(){
		$this->model = new WxreplyTextModel();	
	}
	
	/**
	 * 获取所有keyword，不重复
	 */
	public function getKeywords(){
		$result = $this->model->distinct(true)->field('keyword')->select();
		if($result === false){
			return $this->apiReturnErr($this->model->getDbError());
		}else{
			return $this->apiReturnSuc($result);
		}
	}
	
}
