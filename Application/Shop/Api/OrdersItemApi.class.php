<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Shop\Api;

use Common\Api\Api;
use Shop\Model\OrdersItemModel;
use Shop\Model\OrdersModel;

class OrdersItemApi extends Api
{

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/OrdersItem/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/OrdersItem/add";
    /**
     * 保存
     */
    const SAVE = "Shop/OrdersItem/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/OrdersItem/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/OrdersItem/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/OrdersItem/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/OrdersItem/getInfo";
    /**
     * 统计单个商品的月销量
     */
    const MONTHLY_SALES = "Shop/OrdersItem/monthlySales";

    protected function _init()
    {
        $this->model = new OrdersItemModel();
    }



    /**
     * 统计单个商品的月销量
     */
    public function monthlySales($p_id)
    {
        //TODO: 当前计算不管是否成交，都计入销量
        $map['p_id'] = $p_id;
        $currentTime = time();
        //30天
        $map['createtime'] = array(array('gt', $currentTime - 30 * 3600 * 24), array('lt', $currentTime));

        $result = $this->model->where($map)->select();

        if ($result === false) {
            return $this->apiReturnErr($this->model->getDbError());
        }
        if (is_null($result)) {
            //空无任何销量
            return $this->apiReturnSuc(0);
        }
        $orders_id_arr = array();

        foreach ($result as $vo) {
            array_push($orders_id_arr, $vo['orders_id']);
        }
        if (count($orders_id_arr) == 0) {
            return $this->apiReturnSuc(0);
        }

        $model = new OrdersModel();
        $mapOrders = array();

        //TODO: in 语句， 性能问题，需要修正
        $mapOrders['id'] = array('in', $orders_id_arr);
        // 已支付
//        $mapOrders['pay_status'] = OrdersModel::ORDER_PAID;

        $result = $model->where(array($mapOrders))->select();

        if ($result === false) {
            return $this->apiReturnErr($this->model->getDbError());
        }

        $cnt = 0;
        foreach ($result as $vo) {
            if(in_array($vo['id'],$orders_id_arr)){
                $cnt++;
            }
        }

        return $this->apiReturnSuc($cnt);

    }


}
