<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

use Weixin\Api\WxaccountApi;

class WxaccountController extends AdminController{
	
	
	protected function _initialize(){
		parent::_initialize();
			
	}
	
	public function set(){
		$id = I('get.id',0);
		session("wxaccountid",$id);
		
		$this->success("操作成功！");
	}
	
	public function change(){
		$map = array('uid'=>UID);
		$page = array('curpage'=>I('get.p',0),'size'=>C("LIST_ROWS"));
		$params = array();
		$list = apiCall(WxaccountApi::QUERY, array($map,$page,"createtime desc",$params));
		if($list['status']){
			$this->assign("list",$list['info']['list']);
			$this->assign("show",$list['info']['show']);
			$this->display();
		}
	}

	/**
	 * 首次关注时响应关键词管理
	 */
	public function saveFirstResp(){
		$keyword = I('post.ss_keyword','');

        $result = apiCall(WxaccountApi::SAVE_BY_ID,array(getWxAccountID(),array("ss_keyword"=>$keyword)));

        if($result['status']){
			$this->success(L('RESULT_SUCCESS'));
		}else{
			$this->error($result['info']);
		}
	}
	
	/**
	 * 公众号帮助信息
	 */
	public function help(){
		
		if(IS_GET){
			$map = array('id'=>getWxAccountID());
			$result = apiCall(WxaccountApi::GET_INFO,array($map));
			
			if($result['status']){
				$this->assign("wxaccount",$result['info']);
				$this->display();
			}else{
				LogRecord($result['info'], __FILE__);
				$this->error($result['info']);
			}
		}
	}
	
	/**
	 * 微信账号信息编辑
	 */
	public function edit(){
		if(IS_GET){
			$map = array('id'=>getWxAccountID());
			$result = apiCall(WxaccountApi::GET_INFO,array($map));
			if($result['status']){
				$this->assign("wxaccount",$result['info']);
				$this->display();
			}else{
				LogRecord($result['info'], __FILE__);
				$this->error($result['info']);
			}
		}
	}
	
	public function store(){
		if(IS_POST){
			import("Org.String");
			$id= I('post.id',0,'intval');
			$len = 43;
			$EncodingAESKey= I('post.encodingAESKey','');
			if(empty($EncodingAESKey)){
        			$EncodingAESKey =  \String::randString($len,0,'0123456789');
			}
       	 	$tokenvalue = \String::randString(8,3);	
			$entity = array(
				'wxname'=>I('post.wxname',''),
				'appid'=>I('post.appid'),
				'appsecret'=>I('post.appsecret'),
//				'token'=>I('post.token'),
				'weixin'=>I('post.weixin'),
				'headerpic'=>I('post.headerpic',''),
				'qrcode'=>I('post.qrcode',''),
				'wxuid'=>I('post.wxuid'),
				'encodingAESKey'=>$EncodingAESKey,
			);
			
			if(!empty($id) && $id > 0){

				$result = apiCall(WxaccountApi::SAVE_BY_ID, array($id, $entity));
				if ($result['status'] === false) {
					LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
					$this -> error($result['info']);
				} else {
					$this -> success(L('RESULT_SUCCESS'), U('Admin/Wxaccount/edit'));
				}
			}else{
				$entity['uid'] = UID;
				$entity['token'] = $tokenvalue.time();			
				$result = apiCall(WxaccountApi::ADD, array($entity));
				if ($result['status'] === false) {
					LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
					$this -> error($result['info']);
				} else {
					$this -> success(L('RESULT_SUCCESS'),  U('Admin/Wxaccount/edit'));
				}
			}
		}
	}

	
}
