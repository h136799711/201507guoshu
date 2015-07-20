<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;
use Common\Api\Api;
use \Shop\Model\SkuModel;

class SkuApi extends Api{


    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/Sku/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/Sku/add";
    /**
     * 保存
     */
    const SAVE = "Shop/Sku/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/Sku/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/Sku/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/Sku/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/Sku/getInfo";
    /**
     * 查询sku以及其属性
     */
    const QUERY_SKU_TABLE = "Shop/Sku/querySkuTable";

	protected function _init(){
		$this->model = new SkuModel();
	}
	
	
	public function querySkuTable($cate_id){
		
		$result = $this->model->where(array('cate_id'=>$cate_id))->select();	
		
		if($result === false){
			return $this->apiReturnErr($this->model->getError());
		}
		
		$skuvalueApi = new SkuvalueApi();
		$return = array();
		foreach($result as $sku){
			$one = array(
				'id'=>$sku['id'],
				'name'=>$sku['name'],
				'sku_id'=>$sku['sku_id'],
				'value_list'=>array()
			);
			$map = array('sku_id'=>$sku['id']);
			$skuvalue = $skuvalueApi->queryNoPaging($map);
			if($skuvalue['status']){
				$one['value_list'] = $skuvalue['info'];
			}else{
				return $this->apiReturnErr($skuvalue['info']);
			}
			array_push($return,$one);
		}
		
		return $this->apiReturnSuc($return);
		
	}
	
}

