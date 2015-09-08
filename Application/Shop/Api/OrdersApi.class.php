<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016 杭州博也网络科技, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Shop\Api;

use Common\Api\Api;
use Shop\Model\OrdersContactinfoModel;
use Shop\Model\OrdersItemModel;
use Shop\Model\OrdersModel;

class OrdersApi extends Api
{


    /**
     * 统计和
     */
    const SUM = "Shop/Orders/sum";

    /**
     * 假删除
     */
    const PRETEND_DELETE = "Shop/Orders/pretendDelete";

    /**
     * 添加
     */
    const COUNT = "Shop/Orders/count";
    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/Orders/queryNoPaging";
    /**
     * 添加
     */
    const ADD = "Shop/Orders/add";
    /**
     * 保存
     */
    const SAVE = "Shop/Orders/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/Orders/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/Orders/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/Orders/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/Orders/getInfo";
    /**
     * 统计指定商品的月销量
     */
    const MONTHLY_SALES = "Shop/Orders/monthlySales";
    /**
     * 添加订单信息
     */
    const ADD_ORDER = "Shop/Orders/addOrder";
    /**
     * 更新订单状态
     */
    const SAVE_PAY_STATUS = "Shop/Orders/savePayStatus";

    protected function _init()
    {
        $this->model = new OrdersModel();
    }

    /**
     * 统计指定商品的月销量
     * @param array|商品ID数组 $p_ids 商品ID数组
     * @return array
     */
    public function monthlySales(array $p_ids)
    {

        $currentTime = time();
        $prevTime = $currentTime - 24 * 3600 * 30;
        if (count($p_ids) == 0) {
            $p_ids = array(-1);
        }
        $map['ord_it.p_id'] = array('in', $p_ids);
        $map['ord.createtime'] = array(array('gt', $prevTime), array('lt', $currentTime));
        $result = $this->model->field("count(ord_it.p_id ) as sales,p_id")->alias(" ord ")->join(" LEFT JOIN __ORDERS_ITEM__ as ord_it on ord.id = ord_it.orders_id and (ord.pay_status = 1 or ord.pay_status = 5) ")->where($map)->group("ord_it.p_id")->fetchSql(false)->select();

        if ($result === false) {
            return $this->apiReturnErr($this->model->getDbError());
        }

        return $this->apiReturnSuc($result);
    }


    /**
     * 事务增加订单信息
     */
    public function addOrder($entity)
    {

        $flag = true;
        $error = "";
        //1. 增加order表记录
        $order = array(
            'storeid' => $entity['storeid'],
            'wxaccountid' => $entity['wxaccountid'],
            'wxuser_id' => $entity['wxuser_id'],
            'price' => $entity['price'],
            'note' => $entity['note'],
            'orderid' => $entity['orderid'],
            'comment_status'=>0,
//				'items'=>'',
            'items' => serialize($entity['items']),
        );
//		addWeixinLog($order,'add order 订单 1');

        $this->model->startTrans();
        $result = $this->add($order);

//		addWeixinLog($result,'add order 订单 2');

        $orderid = '';
        if ($result['status']) {
            $orderid = $result['info'];
            //2. 增加order_contactinfo记录
            $orderContactInfo = array(
                'wxuser_id' => $entity['wxuser_id'],
                'orderid' => $entity['orderid'],
                'mobile' => $entity['mobile'],
                'wxno' => $entity['wxno'],
                'contactname' => $entity['contactname'],
                'country' => $entity['country'],
                'province' => $entity['province'],
                'city' => $entity['city'],
                'area' => $entity['area'],
                'wxno' => $entity['wxno'],
                'detailinfo' => $entity['detailinfo'],
                'notes'=>''
            );
            $model = new OrdersContactinfoModel();
            $result = $model->create($orderContactInfo, 1);

            if ($result) {
                $result = $model->add();
                if ($result === FALSE) {
                    //新增失败
                    $flag = false;
                    $error = $model->getDbError();
                }

            } else {//自动验证失败
                $flag = false;
                $error = $model->getError();
            }

        } else {
            $flag = false;
            $error = $result['info'];
        }

        if ($flag) {
            //上面的都没有错误
            //3. 插入到orders_item表中
            $products = $entity['items']['products'];
            $items_arr = array();
            $currentTime = time();
            foreach ($products as $vo) {
                $tmp = array(
                    'orders_id' => $orderid,
                    'has_sku' => $vo['has_sku'],
                    'p_id' => $vo['p_id'],
                    'name' => $vo['name'],
                    'ori_price' => $vo['ori_price'],
                    'price' => $vo['price'],
                    'img' => $vo['img'],
                    'count' => $vo['count'],
                    'post_price' => $vo['post_price'] * 100.0,
                    'sku_id' => '',
                    'sku_desc' => '',
                    'createtime' => $currentTime,
                    'pay_status'=>0
                );

                if (intval($vo['has_sku']) == 1) {
                    //
                    $tmp['sku_id'] = $vo['sku_id'];
                    $tmp['sku_desc'] = $vo['sku_desc']['sku_desc'];

                    $tmp['ori_price'] = $vo['sku_desc']['ori_price'];
                    $tmp['price'] = $vo['sku_desc']['price'];

                    if (!empty($vo['sku_desc']['icon_url'])) {
                        $tmp['img'] = $vo['sku_desc']['icon_url'];
                    }

                }
                array_push($items_arr, $tmp);
            }

            $model = new OrdersItemModel();

            $result = $model->addAll($items_arr);

            if ($result === false) {
                //新增失败
                $flag = false;
                $error = $model->getDbError();
            }

        }

        if ($flag) {
            $this->model->commit();
            return $this->apiReturnSuc($orderid);
        } else {
            $this->model->rollback();
            return $this->apiReturnErr($error);
        }

    }


    /**
     * 设置支付状态
     * @param $map
     * @param $paystatus
     * @return array
     * @internal param 数组 $orderid
     */
    public function savePayStatus($map, $paystatus)
    {

        $result = $this->model->where($map)->lock(true)->save(array('pay_status' => $paystatus));
        if ($result === FALSE) {
            $error = $this->model->getDbError();
            return $this->apiReturnErr($error);
        } else {
            return $this->apiReturnSuc($result);
        }
    }


}
