<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Api;

use Common\Api\Api;
use Weixin\Model\WxreplyNewsModel;

class WxreplyNewsApi extends Api{

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Weixin/WxreplyNews/queryNoPaging";
    /**
     * 查询包含图片
     */
    const QUERY_WITH_PICTURE = "Weixin/WxreplyNews/queryWithPicture";
    /**
     * 查询
     */
    const QUERY = "Weixin/WxreplyNews/query";
    /**
     * 添加
     */
    const ADD = "Weixin/WxreplyNews/add";
    /**
     * 保存
     */
    const SAVE = "Weixin/WxreplyNews/save";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Weixin/WxreplyNews/getInfo";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Weixin/WxreplyNews/saveByID";

    /**
     * 删除
     */
    const DELETE = "Weixin/WxreplyNews/delete";
    /**
     * 获取图文回复的所有不重复关键词
     */
    const GET_KEYWORDS = "Weixin/WxreplyNews/getKeywords";
	
	protected function _init(){
		$this->model = new WxreplyNewsModel();	
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

    /**
     * 查询数据做链接picture表
     */
    public function queryWithPicture($map,$order){
        $list = $this -> model ->alias("news ")->field("news.id,news.wxaccount_id,news.keyword,news.description,news.title,news.url, news.sort, news.pictureid , pic.path as piclocal,pic.url as picremote") ->join('LEFT JOIN __PICTURE__ as pic ON pic.id = news.pictureid')
            -> where($map)->order($order) -> select();

        if ($list === false) {
            $error = $this -> model -> getDbError();
            return $this -> apiReturnErr($error);
        }

        return $this -> apiReturnSuc($list);
    }
}
