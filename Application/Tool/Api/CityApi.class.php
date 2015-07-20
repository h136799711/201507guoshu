<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2015, http://www.gooraye.net. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Tool\Api;
use Common\Api\Api;
use Tool\Model\CityModel;


class CityApi extends Api{

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Tool/City/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Tool/City/add";
    /**
     * 保存
     */
    const SAVE = "Tool/City/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Tool/City/saveByID";

    /**
     * 删除
     */
    const DELETE = "Tool/City/delete";

    /**
     * 查询
     */
    const QUERY = "Tool/City/query";

    /**
     * 查询一条数据
     */
    const GET_INFO = "Tool/City/getInfo";

    /**
     * getListByProvinceID
     */
    const GET_LIST_BY_PROVINCE_ID = "Tool/City/getListByProvinceID";



	protected function _init(){
		$this->model = new CityModel();
	}
	
	/**
	 * 根据省id获取城市
	 */
	public function getListByProvinceID($provinceId){
		$map['father']= $provinceId;
		$map['city']  = array(array('neq','县'),array('neq','市辖区'),'and'); 
		return $this->queryNoPaging(array($map),"cityID desc","cityID,city");
	}
	
}
