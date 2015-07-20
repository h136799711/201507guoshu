<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;
use Common\Api\Api;
use Shop\Model\CategoryPropModel;

class CategoryPropApi extends Api{


    /**
     * 查询,不分页
     */
    const QUERY_NO_PAGING = "Shop/CategoryProp/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/CategoryProp/add";
    /**
     * 保存
     */
    const SAVE = "Shop/CategoryProp/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/CategoryProp/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/CategoryProp/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/CategoryProp/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/CategoryProp/getInfo";
    /**
     * 查询类目属性的值表
     */
    const QUERY_PROP_TABLE = "Shop/CategoryProp/queryPropTable";



	protected function _init(){
		$this->model = new CategoryPropModel();	
	}

    /**
     * 查询属性
     * @param $map
     * @return array
     */
	public function queryPropTable($map){
				
		$result = $this->model->where($map)->select();	
		
		if($result === false){
			return $this->apiReturnErr($this->model->getError());
		}
		
		$propvalueApi = new CategoryPropvalueApi();
		$return = array();
		foreach($result as $prop){
			$one = array(
				'id'=>$prop['id'],
				'name'=>$prop['propname'],
				'property_value'=>array()
			);
			$map = array('prop_id'=>$prop['id']);
			$propvalue = $propvalueApi->queryNoPaging($map);
			if($propvalue['status']){
				$one['property_value'] = $propvalue['info'];
			}else{
				return $this->apiReturnErr($propvalue['info']);
			}
			array_push($return,$one);
		}
		
		return $this->apiReturnSuc($return);
		
	}
	
}


