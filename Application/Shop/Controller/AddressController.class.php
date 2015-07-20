<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;

use Shop\Api\AddressApi;
use Tool\Api\AreaApi;
use Tool\Api\CityApi;
use Tool\Api\ProvinceApi;

class AddressController extends ShopController{
	
	/**
	 * 收货地址选择
	 */
	public function choose(){
		$map = array();
		$map['wxuserid'] = $this->userinfo['id'];
		
		$result = apiCall(AddressApi::QUERY, array($map));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		
		$this->assign("list",$result['info']['list']);
		
		$this ->theme($this->themeType)->display();
		
	}
	
	public function add(){
		if(IS_GET){
		
			
            cookie("__forward__",$_SERVER['HTTP_REFERER']);
			$map = array();
			$map['wxuserid'] = $this->userinfo['id'];
			$map['default'] = 1;
			
			$result = apiCall(AddressApi::GET_INFO,array($map));

			if(!$result['status']){
				$this->error($result['info']);
			}
			if(is_array($result['info'])){
				//获取城市，区域信息
				$city  = apiCall( CityApi::GET_LIST_BY_PROVINCE_ID, array($result['info']['province']));
				if(!$city['status']){
					LogRecord($city['info'], __FILE__.__LINE__);
				}
				$this->assign("city",$city['info']);
				$area  = apiCall(AreaApi::GET_LIST_BY_CITY_ID , array($result['info']['city']));
				if(!$area['status']){
					LogRecord($city['info'], __FILE__.__LINE__);
				}
				$this->assign("area",$area['info']);
			}
            //dump($result['info']);
			$this->assign("address",$result['info']);
			
			$result = apiCall(ProvinceApi::QUERY_NO_PAGING, array());
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			$this->assign("provinces",$result['info']);
			$this->assign("back_url",cookie("__forward__"));
			$this ->theme($this->themeType)->display();
		}else{
			$id = I('post.id',0,'intval');
			$province = I('post.province','');
			$city = I('post.city','');
			$area = I('post.area','');
			$detail = I('post.detail','');
			$mobile = I('post.mobile','');
			$postcode = I('post.postcode','');
            $contactname = I('post.name','');
			
			$entity = array(
				'wxno'=>'',
				'country'=>'中国',
				'province'=>$province,
				'city'=>$city,
				'area'=>$area,
				'detailinfo'=>$detail,
				'mobile'=>$mobile,
				'default'=>1,
				'contactname'=>$contactname,			
				'zip_code'=>$postcode,
			);
			
			
			if(empty($id)){
				//新增
				$entity['wxuserid'] = $this->userinfo['id'];
				$result = apiCall(AddressApi::ADD,array($entity));
			}else{
				//保存
				$result = apiCall(AddressApi::SAVE_BY_ID,array($id,$entity));
			}
			
			if(!$result['status']){
				$this->error($result['info']);
			}
			$this->success("操作成功!~",cookie("__forward__").'?fromsession=1');
			
		}
	}
	
}
