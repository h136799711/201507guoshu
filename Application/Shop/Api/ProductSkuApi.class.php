<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Api;
use Common\Api\Api;
use Shop\Model\ProductModel;
use Shop\Model\ProductSkuModel;


class ProductSkuApi extends Api{


    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/ProductSku/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/ProductSku/add";
    /**
     * 保存
     */
    const SAVE = "Shop/ProductSku/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/ProductSku/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/ProductSku/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/ProductSku/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/ProductSku/getInfo";
    /**
     * 保存sku数据
     */
    const ADD_SKU_LIST = "Shop/ProductSku/addSkuList";


    /**
     *
     */
    protected function _init(){
		$this->model = new ProductSkuModel();
	}


    /**
     * 保存sku数据
     * @param $id
     * @param $sku_info
     * @param $list
     * @return array
     */
    public function addSkuList($id,$sku_info,$list){
        $this->model->startTrans();
        $sql = "";
        $flag = true;
        $error = "";
        $map = array('product_id'=>$id);

        $result = $this->model->where($map)->delete();
        if($result === false){
            $flag = false;
            $error = $this->model->getDbError();
        }

        foreach($list as $vo){
            $entity = array(
                'product_id'=>$id,
                'sku_id'=>$vo['sku_id'],
                'ori_price'=>$vo['ori_price']*100,
                'price'=>$vo['price']*100,
                'quantity'=>$vo['quantity'],
                'product_code'=>$vo['product_code'],
                'icon_url'=>$vo['icon_url'],
                'sku_desc'=>$vo['sku_desc'],
            );

            if($this->model->create($entity,1)){
                $result = $this->model->add();
                if($result === false){
                    $flag = false;
                    $error = $this->model->getError();
                }
            }else{
                $flag = false;
                $error = $this->model->getError();
            }

        }


        if($flag){
            //更新 产品信息
            $entity = array(
                'has_sku'=>1,
                'sku_info'=>json_encode($sku_info,JSON_UNESCAPED_UNICODE),
            );
            $map = array('id'=>$id);
            $model = new ProductModel();
            $result = $model->where($map)->save($entity);
            if($result === false){
                $flag = false;
                $error = $this->model->getDbError();
            }
        }


        if($flag){
            $this->model->commit();
            return $this->apiReturnSuc($result);
        }else{
            $this->model->rollback();
            return $this->apiReturnErr($error);
        }

    }


}
