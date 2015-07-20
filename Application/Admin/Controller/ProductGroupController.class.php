<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

use Shop\Api\ProductGroupApi;

class ProductGroupController extends AdminController{
		
	public function add(){
		$product_id = I('post.product_id',0);
		$groups = I('post.groups',array());
		if($product_id == 0){
			$this->error("参数错误！");
		}
		$map=array(
			'p_id'=>$product_id,
		);
		apiCall(ProductGroupApi::DELETE , array($map));
		if(count($groups) > 0){
			foreach($groups as $groupid){
				
				$entity  = array(
					'p_id'=>$product_id,
					'g_id'=>$groupid,
				);
				if($groupid==14){
					
					$entity['start_time']=strtotime(I('post.start_time_1'));
					$entity['end_time']=strtotime(I('post.end_time_1'));
				}
				
				if($groupid==15){
					$entity['start_time']=strtotime(I('post.start_time_2'));
					$entity['end_time']=strtotime(I('post.end_time_2'));
				}
				
				if($groupid==16){
					$entity['start_time']=strtotime(I('post.start_time_3'));
					$entity['end_time']=strtotime(I('post.end_time_3'));
				}
				
				if($groupid==40){
					$entity['start_time']=strtotime(I('post.start_time_13'));
					$entity['end_time']=strtotime(I('post.end_time_13'));
					$entity['price']=(float)I('price')*100;
				}
				
				$result = apiCall(ProductGroupApi::GET_INFO, array($entity));
				if(!$result['status']){
					$this->error($result['info']);
				}

				if(is_null($result['info'])){
					$result = apiCall(ProductGroupApi::ADD, array($entity));
					if(!$result['status']){
						$this->error($result['info']);
					}				
				}
				
				
			}
			array_push($groups,"-1");
			$map = array('g_id'=>array('not in',$groups));
			$map['p_id'] = $product_id;
			$result = apiCall(ProductGroupApi::DELETE , array($map));
		
		}else{
			$result = array('status'=>true);
			$result = apiCall(ProductGroupApi::DELETE, array(array('p_id'=>$product_id)));
		}
		
		if($result['status']){
			$this->success("操作成功！");
		}else{
			$this->error($result['info']);
		}
	}
	
}
