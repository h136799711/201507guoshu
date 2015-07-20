<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Weixin\Api;
use Common\Api\Api;
use Common\Model\WxuserFamilyModel;

class WxuserFamilyApi extends  Api{



    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Weixin/WxuserFamily/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Weixin/WxuserFamily/add";
    /**
     * 保存
     */
    const SAVE = "Weixin/WxuserFamily/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Weixin/WxuserFamily/saveByID";

    /**
     * 删除
     */
    const DELETE = "Weixin/WxuserFamily/delete";

    /**
     * 查询
     */
    const QUERY = "Weixin/WxuserFamily/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Weixin/WxuserFamily/getInfo";
    /**
     * 如果不存在记录则插入一条
     */
    const CREATE_ONE_IF_NONE = "Weixin/WxuserFamily/createOneIfNone";


	protected function _init(){
		$this->model = new WxuserFamilyModel();
	}

    /**
     * 根据参数创建一个wxuserfamily记录
     *
     * @param $uid
     * @param $referrer
     * @return array|bool
     * @internal param $wxaccount_id
     * @internal param $openid
     */
    public function createOneIfNone($uid,$referrer){

        $userfamily = $this->model->where(array('uid'=>$uid))->find();

        if($userfamily === false ){
            $error = $this->model->getDbError();
            return $this -> apiReturnErr($error);
        }elseif(is_array($userfamily)){
            //已存在,不更新，返回数据
            return $this -> apiReturnSuc($userfamily);
        }
        $parent_family = null;
        if($referrer > 0){
            //推荐人的父级关系
            $parent_family = $this->model->where(array('uid'=>$referrer))->find();

            if($parent_family === false){
                $error = $this->model->getDbError();
                return $this -> apiReturnErr($error);
            }

            if(is_null($parent_family)){
                return $this -> apiReturnErr("推荐人家族关系BUG！");
            }
        }

        $entity = array(
            'uid'=>$uid,
            'parent_1'=>$referrer,
            'parent_2'=>0,
            'parent_3'=>0,//三级
            'parent_4'=>0,
            'create_time'=>time(),
            'wxaccount_id'=>-1, //无效字段
        );

        if($referrer > 0 && is_array($parent_family)){
            $entity['parent_1'] = $parent_family['parent_1'];
            $entity['parent_2'] = $parent_family['parent_2'];
        }


        return  $this->add($entity);
    }
	
}
