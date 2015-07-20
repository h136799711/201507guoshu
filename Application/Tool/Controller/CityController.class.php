<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Tool\Controller;
use Think\Controller;
use Tool\Api\AreaApi;
use Tool\Api\CityApi;
use Tool\Api\ProvinceApi;

class CityController extends Controller{
	
	public function index(){
		$result = apiCall(ProvinceApi::QUERY_NO_PAGING,array());
		if($result['status']){
			$this->assign("provinces",$result['info']);		
			$this->display();
		}
		
	}
	
	public function getCitys(){
		
		$provinceID = I('post.provinceid','');
		
		$result = apiCall(CityApi::GET_LIST_BY_PROVINCE_ID,array($provinceID));
		if($result['status']){
			$this->success($result['info']);
		}else{
			$this->error($result['info']);
		}
	}
	
	public function getArea(){
		
		$cityid = I('post.cityid','');
		
		$result = apiCall(AreaApi::GET_LIST_BY_CITY_ID,array($cityid));
		if($result['status']){
			$this->success($result['info']);
		}else{
			$this->error($result['info']);
		}
	}
	
}
