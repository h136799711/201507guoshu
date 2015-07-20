<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Admin\Api;

use Common\Api\Api;
use Common\Model\WxuserGroupModel;
use Common\Model\GroupAccessModel;

class WxuserGroupApi extends Api{


    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Admin/WxuserGroup/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Admin/WxuserGroup/add";
    /**
     * 保存
     */
    const SAVE = "Admin/WxuserGroup/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Admin/WxuserGroup/saveByID";

    /**
     * 删除
     */
    const DELETE = "Admin/WxuserGroup/delete";

    /**
     * 查询
     */
    const QUERY = "Admin/WxuserGroup/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Admin/WxuserGroup/getInfo";

    /**
     *
     * 添加实体并增加权限
     */
    const ADD_WITH_ACCESS = "Admin/WxuserGroup/addWithAccess";
    /**
     *
     * 删除用户组，同时删除对应权限记录
     */
    const DEL_WITH_ACCESS = "Admin/WxuserGroup/delWithAccess";

	protected function _init(){
		$this->model = new WxuserGroupModel();
	}
	
	/**
	 * 添加实体并增加groupaccess
	 */
	public function addWithAccess($entity){
		$this->model->startTrans();
		$result = FALSE;
		$result2 = FALSE;
		$error = "";
		if($this->model->create($entity)){
			$result = $this->model->add();
		}else{
			$error = $this->model->getError();
		}
		
		if($result > 0){
			$groupaccess = D('GroupAccess');
			if($groupaccess->create(array('wxuser_group_id'=>$result))){
				$result2 =  $groupaccess->add();
			}else{
				$error = $groupaccess->getError();
			}
		}
		if($result === FALSE || $result2 === FALSE){
			if(empty($error)){
				if($result === FALSE){
					$error = $this->model->getDbError();
				}elseif($result2 === FALSE){
					$error = $groupaccess->getDbError();
				}
			}
			$this->model->rollback();
			return $this->apiReturnErr($error);
		}else{
			$this->model->commit();
			return $this->apiReturnSuc($result);
		}

	}
	
	
	
	/**
	 * 删除用户组，同时删除其对应权限
	 */
	public function delWithAccess($id){
		$this->model->startTrans();
		$result = FALSE;
		$result2 = FALSE;
		
		$groupaccess = D('GroupAccess');
		$result2 =  $groupaccess->where("wxuser_group_id = $id")->delete();
		
		$result = $this->model->where("id = $id")->delete();
		
		if($result === false || $result2 === false){
			if($result === FALSE){
				$error = $this->model->getDbError();
			}elseif($result2 === FALSE){
				$error = $groupaccess->getDbError();
			}
			$this->model->rollback();
			return $this->apiReturnErr($error);
		}else{
			$this->model->commit();
			return $this->apiReturnSuc($result);
		}
	}
	
}
