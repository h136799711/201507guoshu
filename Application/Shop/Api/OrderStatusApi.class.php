<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/2
 * Time: 11:00
 */

namespace Shop\Api;
use Shop\Api\OrdersApi;
use Shop\Model\OrdersModel;
use Shop\Model\OrderStatusHistoryModel;
use Distributor\Api\CommissionCountApi;

/**
 * 订单状态变更接口
 * Class OrdersStatusApi
 * @package Shop\Api
 */
class OrderStatusApi{

    /**
     * 待支付－》货到付款状态变更
     */
    const CASH_ON_DELIVERY = "Shop/OrderStatus/cashOndelivery";
    /**
     * 待发货－》已发货状态变更
     */
    const SHIPPED = "Shop/OrderStatus/shipped";
    /**
     * 待确认－》已确认状态变更
     */
    const CONFIRM_ORDER = "Shop/OrderStatus/confirmOrder";
    /**
     * 待确认－》取消状态变更
     */
    const BACK_ORDER = "Shop/OrderStatus/backOrder";
    /**
     * 已发货 -> 已收货 状态变更
     */
    const CONFIRM_RECEIVE = "Shop/OrderStatus/confirmReceive";
    /**
     * 已收货 －》退货 状态变更
     */
    const RETURNED = "Shop/OrderStatus/returned";
    /**
     * 订单评价－》完成 状态变更
     */
    const EVALUATION = "Shop/OrderStatus/evaluation";
	
	
	/**
	 * 更新状态=》已取消
	 */
	const ORDER_STATUS_TO_CANCEL = "Shop/OrderStatus/orderStatusToCancel";
	
	/**
	 * 更新状态=》已收货
	 */
	const ORDER_STATUS_TO_RECIEVED = "Shop/OrderStatus/orderStatusToRecieved";
	
	/**
	 * 更新状态=》已完成
	 */
	const ORDER_STATUS_TO_COMPLETED = "Shop/OrderStatus/orderStatusToCompleted";
	
	
	

    /**
     *
     */
    function __construct(){
        $this->model = new OrdersModel();
    }

    /**
     * 订单支付－货到付款操作
     * @param $ids  订单id数组
     * @return array
     */
    public function cashOndelivery($ids,$isauto,$uid){
        $orderStatusHistoryModel = new OrderStatusHistoryModel();
        //
        foreach($ids as  $id){

            $result = $this->model->where(array('id'=>$id))->find();

            if($result == false){
                return $this->returnErr($this->model->getDbError());
            }

            if(is_null($result)){
                return $this->returnErr("订单ID错误!");
            }

            if($result['pay_status'] !=  OrdersModel::ORDER_TOBE_PAID){
                return $this->returnErr("当前订单状态无法变更！");
            }
			//dump($result['orderid']);
            $entity = array(
                'reason'=>"用户选择货到付款支付!",
                'orders_id'=>$result['id'],
                'operator'=>$uid,
                'status_type'=>'PAY',
                'cur_status'=>$result['pay_status'],
                'isauto'=>0,
                'next_status'=> OrdersModel::ORDER_CASH_ON_DELIVERY,
            );

            $this->model->startTrans();
            $flag = true;
            $return = "";

            //设置订单状态为货到付款
            $result = $this->model->where(array('id'=>$id))->save(array('pay_status'=> OrdersModel::ORDER_CASH_ON_DELIVERY));
            if($result === false){
                $flag = false;
                $return = $this->model->getDbError();
            }
            if($result == 0){
                $flag = false;
                $return = "订单ID有问题!";
            }

            if($orderStatusHistoryModel->create($entity,1)){
                $result = $orderStatusHistoryModel->add();
                if($result === false){
                    $flag = false;
                    $return = $orderStatusHistoryModel->getDbError();
                }
            }else{
                $flag = false;
                $return = $orderStatusHistoryModel->getError();
            }
            //单个
        }

        if($flag){
            $this->model->commit();
            return $this->returnSuc($return);
        }else{
            $this->model->rollback();
            return $this->returnErr($return);
        }

    }

	/**
	 * 返回错误结构
	 * @return array('status'=>boolean,'info'=>Object)
	 */
	protected function returnErr($info) {
		return array('status' => false, 'info' => $info);
	}
	
	/**
	 * 返回成功结构
	 * @return array('status'=>boolean,'info'=>Object)
	 */
	protected function returnSuc($info) {
		return array('status' => true, 'info' => $info);
	}
	

    /**
     * 订单发货操作
     * @param $id
     * @param $uid
     * @return array
     * @internal param $isauto
     */
    public function shipped($id,$uid){
        $orderStatusHistoryModel = new OrderStatusHistoryModel();
        $result = $this->model->where(array('id'=>$id))->find();

        if($result == false){
            return $this->returnErr($this->model->getDbError());
        }

        if(is_null($result)){
            return $this->returnErr("订单ID错误!");
        }

        if($result['order_status'] != OrdersModel::ORDER_TOBE_SHIPPED){
            return $this->returnErr("当前订单状态无法变更！");
        }

        $entity = array(
            'reason'=>"订单发货操作!",
            'orders_id'=>$result['id'],
            'operator'=>$uid,
            'status_type'=>'ORDER',
            'isauto'=>0,
            'cur_status'=>$result['order_status'],
            'next_status'=> OrdersModel::ORDER_SHIPPED,
        );

        $this->model->startTrans();
        $flag = true;
        $return = "";

        $result = $this->model->where(array('id'=>$id))->save(array('order_status'=> OrdersModel::ORDER_SHIPPED));
        if($result === false){
            $flag = false;
            $return = $this->model->getDbError();
        }
        if($result == 0){
            $flag = false;
            $return = "订单ID有问题!";
        }

        if($orderStatusHistoryModel->create($entity,1)){
            $result = $orderStatusHistoryModel->add();
            if($result === false){
                $flag = false;
                $return = $orderStatusHistoryModel->getDbError();
            }
        }else{
            $flag = false;
            $return = $orderStatusHistoryModel->getError();
        }



        if($flag){
            $this->model->commit();
            return $this->returnSuc($return);
        }else{
            $this->model->rollback();
            return $this->returnErr($return);
        }

    }


    /**
     * 订单确认
     */
    public function confirmOrder($id,$isauto,$uid){
        $orderStatusHistoryModel = new OrderStatusHistoryModel();
        $result = $this->model->where(array('id'=>$id))->find();

        if($result == false){
            return $this->returnErr($this->model->getDbError());
        }

        if(is_null($result)){
            return $this->returnErr("订单ID错误!");
        }

        if($result['order_status'] != OrdersModel::ORDER_TOBE_CONFIRMED){
            return $this->returnErr("当前订单状态无法变更！");
        }

        $entity = array(
            'reason'=>"订单确认操作!",
            'orders_id'=>$result['id'],
            'operator'=>$uid,
            'status_type'=>'ORDER',
            'isauto'=>0,
            'cur_status'=>$result['order_status'],
            'next_status'=> OrdersModel::ORDER_TOBE_SHIPPED,
        );

        $this->model->startTrans();
        $flag = true;
        $return = "";

        $result = $this->model->where(array('id'=>$id))->save(array('order_status'=> OrdersModel::ORDER_TOBE_SHIPPED));
        if($result === false){
            $flag = false;
            $return = $this->model->getDbError();
        }
        if($result == 0){
            $flag = false;
            $return = "订单ID有问题!";
        }

        if($orderStatusHistoryModel->create($entity,1)){
            $result = $orderStatusHistoryModel->add();
            if($result === false){
                $flag = false;
                $return = $orderStatusHistoryModel->getDbError();
            }
        }else{
            $flag = false;
            $return = $orderStatusHistoryModel->getError();
        }



        if($flag){
            $this->model->commit();
            return $this->returnSuc($return);
        }else{
            $this->model->rollback();
            return $this->returnErr($return);
        }

    }

    /**
     * 退回订单
     */
    public function backOrder($id,$reason,$isauto,$uid){

        $orderStatusHistoryModel = new OrderStatusHistoryModel();
        $result = $this->model->where(array('id'=>$id))->find();

        if($result == false){
            return $this->returnErr($this->model->getDbError());
        }

        if(is_null($result)){
            return $this->returnErr("订单ID错误!");
        }
//		dump($result);
        if($result['order_status'] != OrdersModel::ORDER_TOBE_CONFIRMED){
            return $this->returnErr("当前订单状态无法变更！");
        }

        $entity = array(
            'reason'=>$reason,
            'orders_id'=>$result['orderid'],
            'operator'=>$uid,
            'status_type'=>'ORDER',
            'isauto'=>0,
            'cur_status'=>$result['order_status'],
            'next_status'=> OrdersModel::ORDER_BACK,
        );

        $this->model->startTrans();
        $flag = true;
        $return = "";

        $result = $this->model->where(array('id'=>$id))->save(array('order_status'=>OrdersModel::ORDER_BACK));
        if($result === false){
            $flag = false;
            $return = $this->model->getDbError();
        }
        if($result == 0){
            $flag = false;
            $return = "订单ID有问题!";
        }

        if($orderStatusHistoryModel->create($entity,1)){
            $result = $orderStatusHistoryModel->add();
            if($result === false){
                $flag = false;
                $return = $orderStatusHistoryModel->getDbError();
            }
        }else{
            $flag = false;
            $return = $orderStatusHistoryModel->getError();
        }



        if($flag){
            $this->model->commit();
            return $this->returnSuc($return);
        }else{
            $this->model->rollback();
            return $this->returnErr($return);
        }


    }

    /**
     * 确认收货操作
     */
    public function confirmReceive($id,$isauto,$uid){
        $orderStatusHistoryModel = new OrderStatusHistoryModel();
        $result = $this->model->where(array('id'=>$id))->find();

        if($result == false){
            return $this->returnErr($this->model->getDbError());
        }

        if(is_null($result)){
            return $this->returnErr("订单ID错误!");
        }

        if($result['order_status'] != OrdersModel::ORDER_SHIPPED){
            return $this->returnErr("当前订单状态出错!");
        }

        $entity = array(
            'reason'=>"确认收货操作!",
            'orders_id'=>$result['id'],
            'operator'=>$uid,
            'isauto'=>0,
            'status_type'=>'ORDER',
            'cur_status'=>$result['order_status'],
            'next_status'=>OrdersModel::ORDER_RECEIPT_OF_GOODS,
        );

        $this->model->startTrans();
        $flag = true;
        $return = "";

        $result = $this->model->where(array('id'=>$id))->save(array('order_status'=>OrdersModel::ORDER_RECEIPT_OF_GOODS));
        if($result === false){
            $flag = false;
            $return = $this->model->getDbError();
        }
        if($result == 0){
            $flag = false;
            $return = "订单ID有问题!";
        }

        if($orderStatusHistoryModel->create($entity,1)){
            $result = $orderStatusHistoryModel->add();
            if($result === false){
                $flag = false;
                $return = $orderStatusHistoryModel->getDbError();
            }
        }else{
            $flag = false;
            $return = $orderStatusHistoryModel->getError();
        }



        if($flag){
            $this->model->commit();
            return $this->returnSuc($return);
        }else{
            $this->model->rollback();
            return $this->returnErr($return);
        }
    }

    /**
     * 退货操作
     * @param $id
     * @param $isauto
     * @param $uid
     * @return
     */
    public function returned($id,$isauto,$uid){
        $orderStatusHistoryModel = new OrderStatusHistoryModel();
        $result = $this->model->where(array('id'=>$id))->find();

        if($result == false){
            return $this->returnErr($this->model->getDbError());
        }

        if(is_null($result)){
            return $this->returnErr("订单ID错误!");
        }

        if($result['order_status'] == OrdersModel::ORDER_RECEIPT_OF_GOODS ){
            return $this->returnErr("当前订单状态出错!");
        }

        $entity = array(
            'reason'=>"订单退货操作!",
            'orders_id'=>$result['orderid'],
            'operator'=>$uid,
            'status_type'=>'ORDER',
            'isauto'=>0,
            'cur_status'=>$result['order_status'],
            'next_status'=>Model\OrdersModel::ORDER_RETURNED,
        );

        $this->model->startTrans();
        $flag = true;
        $return = "";

        $result = $this->model->where(array('id'=>$id))->save(array('order_status'=>OrdersModel::ORDER_RETURNED));
        if($result === false){
            $flag = false;
            $return = $this->model->getDbError();
        }

        if($result == 0){
            $flag = false;
            $return = "订单ID有问题!";
        }

        if($orderStatusHistoryModel->create($entity,1)){
            $result = $orderStatusHistoryModel->add();
            if($result === false){
                $flag = false;
                $return = $orderStatusHistoryModel->getDbError();
            }
        }else{
            $flag = false;
            $return = $orderStatusHistoryModel->getError();
        }



        if($flag){
            $this->model->commit();
            return $this->returnSuc($return);
        }else{
            $this->model->rollback();
            return $this->returnErr($return);
        }
    }

    /**
     * 订单评价操作
     * @param $id
     * @param $isauto
     * @param $uid
     * @return
     */
    public function evaluation($id,$isauto,$uid){
        $orderStatusHistoryModel = new OrderStatusHistoryModel();
        $result = $this->model->where(array('id'=>$id))->find();

        if($result == false){
            return $this->returnErr($this->model->getDbError());
        }

        if(is_null($result)){
            return $this->returnErr("订单ID错误!");
        }

        if($result['order_status'] != OrdersModel::ORDER_RECEIPT_OF_GOODS ){
            return $this->returnErr("当前订单状态出错!");
        }

        $entity = array(
            'reason'=>"订单评价操作!",
            'orders_id'=>$result['orderid'],
            'operator'=>$uid,
            'status_type'=>'COMMENT',
            'isauto'=>0,
            'cur_status'=>$result['order_status'],
            'next_status'=>OrdersModel::ORDER_COMPLETED,
        );

        $this->model->startTrans();
        $flag = true;
        $return = "";

        $result = $this->model->where(array('id'=>$id))->save(array('comment_status'=>OrdersModel::ORDER_HUMAN_EVALUATED,'order_status'=>OrdersModel::ORDER_COMPLETED));
        if($result === false){
            $flag = false;
            $return = $this->model->getDbError();
        }

        if($result == 0){
            $flag = false;
            $return = "订单ID有问题!";
        }

        if($orderStatusHistoryModel->create($entity,1)){
            $result = $orderStatusHistoryModel->add();
            if($result === false){
                $flag = false;
                $return = $orderStatusHistoryModel->getDbError();
            }
        }else{
            $flag = false;
            $return = $orderStatusHistoryModel->getError();
        }



        if($flag){
            $this->model->commit();
            return $this->returnSuc($return);
        }else{
            $this->model->rollback();
            return $this->returnErr($return);
        }
    }



/****************************以下为原OrdersApi内容*******************************/

	

	 /**
       * 设置订单状态
       * TODO：需要记录状态变更日志
       * @param $interval 判断的间隔时间 秒 为单位
       * @return array
       */
	public function orderStatusToCancel($interval){
		$map['updatetime'] = array('lt',time()-$interval);
		$map['order_status'] = OrdersModel::ORDER_TOBE_CONFIRMED;
		$map['pay_status'] = OrdersModel::ORDER_TOBE_PAID;
		$saveEntity = array('order_status'=>OrdersModel::ORDER_CANCEL);
		$result = $this->model->create($saveEntity,2);
		if($result === false){
			return $this->returnErr($this->model->getError());
		}
		$result = $this->model->where($map)->lock(true)->save();
//		addWeixinLog($this->model->getLastSql(),"[自动变更订单待确认、待支付为已取消SQL]");
		if($result === FALSE){
			$error = $this->model->getDbError();
			return $this->returnErr($error);
		}else{
			return $this->returnSuc($result);
		}
	}

      /**
       * 设置订单状态
       * TODO：需要记录状态变更日志
       * @param $interval 判断的间隔时间 秒 为单位
       * @return array
       */
	public function orderStatusToRecieved($interval){
		$map['updatetime'] = array('lt',time()-$interval);
		$map['order_status'] = OrdersModel::ORDER_SHIPPED;
		$saveEntity = array('order_status'=>OrdersModel::ORDER_RECEIPT_OF_GOODS);
		$result = $this->model->create($saveEntity,2);
		if($result === false){
			return $this->returnErr($this->model->getError());
		}
		$result = $this->model->where($map)->lock(true)->save();
//		addWeixinLog($this->model->getLastSql(),"[自动变更订单已发货为已收货SQL]");
		if($result === FALSE){
			$error = $this->model->getDbError();
			return $this->returnErr($error);
		}else{
			return $this->returnSuc($result);
		}
	}

      /**
       *
       * 设置订单状态
       * TODO：需要记录状态变更日志
       * @param $interval 判断的间隔时间 秒 为单位
       *
       * @return array
       */
	public function orderStatusToCompleted($interval){
		$map['updatetime'] = array('lt',time()-$interval);
		$map['order_status'] = OrdersModel::ORDER_RECEIPT_OF_GOODS;
		$map['comment_status']=array('NEQ',OrdersModel::ORDER_TOBE_EVALUATE);//评价状态不为0
		$result=apiCall(OrdersApi::QUERY_NO_PAGING,array($map));
		foreach($result['info'] as $order){
			$ids[]=$order['id'];
		}
		//如果有超过时间的且状态为已收货，佣金发放
		if(count($ids)>0){
			apiCall(CommissionCountApi::ADD,array($ids));
		}
		
		$saveEntity = array('order_status'=>OrdersModel::ORDER_COMPLETED);
		$result = $this->model->create($saveEntity,2);
		if($result === false){
			return $this->returnErr($this->model->getError());
		}
		$result = $this->model->where($map)->lock(true)->save();
//		addWeixinLog($this->model->getLastSql(),"[自动变更订单已收货为已完成SQL]");
		if($result === FALSE){
			$error = $this->model->getDbError();
			return $this->returnErr($error);
		}else{
			return $this->returnSuc($result);
		}
	}
	
	/**
	 * 自动评价
	 */
	public function toAutoEvaluation($interval){
		$map['updatetime'] = array('lt',time()-$interval);
		$map['order_status'] = OrdersModel::ORDER_RECEIPT_OF_GOODS;
		$map['comment_status']=OrdersModel::ORDER_TOBE_EVALUATE;
		
		$saveEntity = array('comment_status'=>OrdersModel::ORDER_SYSTEM_EVALUATED);
		$result = $this->model->create($saveEntity,2);
		if($result === false){
			return $this->returnErr($this->model->getError());
		}
		$result = $this->model->where($map)->lock(true)->save();
		if($result === FALSE){
			$error = $this->model->getDbError();
			return $this->returnErr($error);
		}else{
			return $this->returnSuc($result);
		}
	}
	
	
	


}