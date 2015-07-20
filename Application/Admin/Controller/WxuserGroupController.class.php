<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

use Admin\Api\GroupAccessApi;
use Admin\Api\WxuserGroupApi;
use Weixin\Api\WxuserApi;

class WxuserGroupController extends  AdminController {
	protected function _initialize() {
		parent::_initialize();
	}

	public function index() {

		$map = array();

		$page = array('curpage' => I('get.p', 0), 'size' => C('LIST_ROWS'));
		$order = " id asc ";
		//
		$result = apiCall(WxuserGroupApi::QUERY, array($map, $page, $order));

		//
		if ($result['status']) {
			$this -> assign('show', $result['info']['show']);
			$this -> assign('list', $result['info']['list']);
			$this -> display();
		} else {
			LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
			$this -> error($result['info']);
		}
	}

	public function add() {
		if (IS_GET) {
			$this -> display();
		} elseif (IS_POST) {
			$entity = array('name' => I('post.name', ''), 'description' => I('post.description', ' '), );

			$result = apiCall(WxuserGroupApi::ADD_WITH_ACCESS, array($entity));
			if ($result['status']) {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/WxuserGroup/index'));
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}

		}
	}

	public function edit() {
		if (IS_GET) {

			$id = I('get.id', 0);
			$result = apiCall(WxuserGroupApi::GET_INFO, array(array('id'=>$id)));

			if ($result['status']) {
				$this -> assign("vo", $result['info']);
				$this -> display();
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
		} else {

			$id = I('post.id', 0);
			$entity = array('name' => I('post.name', ''), 'description' => I('post.description', ' '), );

			$result = apiCall(WxuserGroupApi::SAVE_BY_ID, array($id, $entity));

            if ($result['status'] === false) {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			} else {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/WxuserGroup/index'));
			}

		}
	}

	public function powerEdit() {
		if (IS_GET) {
            $group_id = I('get.groupid', 0);
			$map = array('wxuser_group_id' => $group_id);
            $result = apiCall(GroupAccessApi::GET_INFO, array($map));

			if ($result['status']) {
				$this -> assign("access", $result['info']);

                $result = apiCall(WxuserGroupApi::GET_INFO, array(array('id'=>$group_id)));

                if(!$result['status']){
                    $this->error($result['info']);
                }

                $this->assign("vo",$result['info']);

				$this -> display();
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}

		} elseif (IS_POST) {
			
			$id = I('post.id',0);
			
			$entity = array(
				'alloweddistribution'=>I('post.alloweddistribution',0),
				'allowedcomment'=>I('post.allowedcomment',0),
                'percent'=>(I('post.percent',0,'floatval')/100.0),
			);
			
			$result = apiCall(GroupAccessApi::SAVE_BY_ID, array($id,$entity));
			
			if ($result['status']) {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/WxuserGroup/index'));
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}

		}

	}

	public function delete($redirect_url = false) {
		if (IS_GET) {
			$id = I('get.id', 0);
			$result = apiCall(WxuserApi::COUNT_WXUSERS, array($id));

			if ($result['status']) {
				if ($result['info'] > 0) {
					$this -> error("请先去除关联此会员组的会员！");
				}
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}

			$result = apiCall(WxuserGroupApi::DEL_WITH_ACCESS, array($id));
			if ($result['status']) {
				$this -> success(L('RESULT_SUCCESS'), U('Admin/WxuserGroup/index'));
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
		} elseif (IS_POST) {

		}
	}
	/**
	 * 所属用户组的会员管理
	 * 
	 */
	public function subMember(){
		$map = array();
		$groupid = I('groupid', -1);
		
		$map['status'] = array('egt', 0);
		if ($groupid != -1) {
			$map['group_id'] = $groupid;
		}
		$memberMap = array();

		//用户组
		$result = apiCall(WxuserGroupApi::QUERY_NO_PAGING, array($map));
		if ($result['status']) {
			if ($groupid === -1) {
				$groupid = $result['info'][0]['id'];
			}

			$this -> assign("groupid", $groupid);
			$this -> assign("groups", $result['info']);

			$memberMap['status'] = array('egt', 0);
			$memberMap['groupid'] = $groupid;
			//查询用户信息
			//TODO:
			$result = apiCall(WxuserApi::QUERY, array($memberMap, array('curpage' => I('p', 0), 'size' => 10)));
			if ($result['status']) {
				$this -> assign("show", $result['info']['show']);
				$this -> assign("list", $result['info']['list']);
			}
			$this -> display();
		} else {
			$this -> error($result['info']);
		}
	}
	
	

}
