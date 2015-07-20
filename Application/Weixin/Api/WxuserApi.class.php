<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Weixin\Api;

use Common\Api\Api;
use Common\Model\WxuserModel;
use Think\Page;

class WxuserApi extends Api{

    /**
     * 增加用户字段值
     */
    const SET_INC = "Weixin/Wxuser/setInc";
    /**
     * 注册
     */
    const REGISTER = "Weixin/Wxuser/register";
    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Weixin/Wxuser/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Weixin/Wxuser/add";
    /**
     * 保存
     */
    const SAVE = "Weixin/Wxuser/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Weixin/Wxuser/saveByID";

    /**
     * 删除
     */
    const DELETE = "Weixin/Wxuser/delete";

    /**
     * 查询
     */
    const QUERY = "Weixin/Wxuser/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Weixin/Wxuser/getInfo";

    /**
     * 查询一条数据包含家族关系
     */
    const GET_INFO_WITH_FAMILY = "Weixin/Wxuser/getInfoWithFamily";

    /**
     * 升级用户组
     */
    const GROUP_UP = "Weixin/Wxuser/groupUp";

    /**
     * 降级用户组
     */
    const GROUP_DOWN = "Weixin/Wxuser/groupDown";

    /**
     * 统计用户数目
     */
    const COUNT_WXUSERS = "Weixin/Wxuser/countWxusers";

    /**
     * 查询子级会员信息
     */
    const QUERY_SUB_MEMBER = "Weixin/Wxuser/querySubMember";

	protected function _init(){
		$this->model = new WxuserModel();
	}


    /**
     *
     */
    public function register($wxuser){
        //
        if($this->model->create($wxuser)){
            $result = $this->model->add();
            if($result === false){
                return $this->apiReturnErr($this->model->getDbError());
            }else{
                return $this->apiReturnSuc($result);
            }


        }else{
            return $this->apiReturnErr($this->model->getError());
        }

    }

	/**
	 * 获取家族关系
	 */
	public function getInfoWithFamily($id){
		$result = $this->model->alias(" wu ")->field("wu.nickname,wu.referrer,wu.id as wxuserid,wu.openid,wu.wxaccount_id,wf.parent_1,wf.parent_2,wf.parent_3,wf.parent_4,wf.parent_5")->join("LEFT JOIN __WXUSER_FAMILY__ as wf on wu.openid = wf.openid and wu.wxaccount_id = wf.wxaccount_id")->where(array('wu.id'=>$id))->find();
	
		if($result === false){
			$error = $this->model->getDbError();
			return $this -> apiReturnErr($error);
		}else{
			return $this->apiReturnSuc($result);
		}
	}

    /**
     * 升级用户组
     */
    public function groupUp($wxuserid){

        $result = $this->model->where(array('id'=>$wxuserid))->find();

        if($result === FALSE){
            $error = $this->model->getDbError();
            return $this -> apiReturnErr($error);

        }else{
            $groupid = $result['groupid'];
//			dump($groupid);
            // 获取用户组信息
            $group = apiCall("Admin/WxuserGroup/getInfo",array(array('id'=>$groupid)));
//			dump($group);
            if($group['status']){
                if(is_null($group['info'])){
                    $error = false;
                    return $this -> apiReturnSuc($error);
                }
                $nextgroupid = $group['info']['nextgroupid'];
                if($nextgroupid >= 0){
                    $result = $this->model->create(array('groupid'=>$nextgroupid));
                    if($result === false){
                        $error = $this->model->getError();
                        return $this -> apiReturnErr($error);
                    }
                    $result = $this->model->where(array('id'=>$wxuserid))->save();
                    if($result === false){
                        $error = $this->model->getDbError();
                        return $this -> apiReturnErr($error);
                    }else{
                        return $this -> apiReturnSuc($result);
                    }
                }else{
                    return $this -> apiReturnSuc($nextgroupid);
                }
            }else{
                $error = $group['info'];
                return $this -> apiReturnErr($error);
            }
        }

    }

    /**
     * 降级用户组
     */
    public function groupDown($wxuserid){

        $result = $this->model->where(array('id'=>$wxuserid))->find();

        if($result === FALSE){
            $error = $this->model->getDbError();
            return $this -> apiReturnErr($error);
        }else{
            $groupid = $result['groupid'];
            // 获取用户组信息
            $group = apiCall("Admin/WxuserGroup/getInfo",array(array('nextgroupid'=>$groupid)));
            if($group['status']){
                if(is_null($group['info'])){
                    $error = false;
                    return $this -> apiReturnSuc($error);
                }
                $prevgroupid = $group['info']['id'];
                $result = $this->model->where(array('id'=>$wxuserid))->save(array('groupid'=>$prevgroupid));
                if($result === false){
                    $error = $this->model->getDbError();
                    return $this -> apiReturnErr($error);
                }else{
                    return $this -> apiReturnSuc($result);
                }

            }else{
                $error = $group['info'];
                return $this -> apiReturnErr($error);
            }
        }
    }

    /**
     * 统计某一会员组的会员数目
     * @param $groupid 会员组id
     * @return array
     */
    public function countWxusers($groupid){
        $userCount = $this->model->where(array("groupid"=>$id))->count();
        if($userCount === false){
            return $this -> apiReturnErr($this->model->getDbError());
        }else{
            return $this->apiReturnSuc($userCount);
        }
    }

    /**
     * 查询子级会员
     * @param $wxuserid
     * @param $page
     * @param $params
     * @return array
     */
    public function querySubMember($wxuserid,$level,$page,$params){
        if($level < 0 || $level > 3){
            //
            return $this->apiReturnErr("参数错误!");
        }

        $countsql = "SELECT count(uid) as cnt FROM  __WXUSER_FAMILY__ where `parent_$level` = $wxuserid";

        $subsql = "SELECT wxaccount_id,uid FROM  __WXUSER_FAMILY__ where `parent_$level` = $wxuserid ";


        $sql = "select wu.subscribe_time, wu.wxaccount_id,wu.id,wu.nickname,wu.avatar,wu.referrer,wu.uid,wu.score,wu.money,wu.status
from ($subsql) as wf
left join __WXUSER__ as wu on wf.wxaccount_id = wu.wxaccount_id and wf.uid = wu.uid
where wu.status =  1 limit ".$page['curpage'] . ',' . $page['size'];
        $model = M();
        $subQuery = $model->table('__WXUSER_FAMILY__')->alias("wf")->field('wf.wxaccount_id,wf.uid')->where("`parent_$level` = $wxuserid")->buildSql();

        $result = $model->query($sql);

        $count = $model->query($countsql);
        $count = $count[0]["cnt"];

        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        if($result === false){
            $error = $this->model->getDbError();
            return $this -> apiReturnErr($error);
        }else{
            return $this -> apiReturnSuc(array("show" => $show, "list" => $result));
        }

    }
}
