<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Controller;

use Shop\Api\OrdersApi;
use Shop\Model\OrdersModel;
use Admin\Api\DatatreeApi;
use Shop\Api\WalletApi;
use Shop\Api\WalletHisApi;


class UserController extends ShopController{
	
	
	
	/**
	 * 用户个人中心
	 */	
	public function info(){
		if(IS_GET){
			
			$tobePaid = $this->orderCount(1);
			$tobeShipped = $this->orderCount(2);
			$tobeReceipt = $this->orderCount(3);
			$tobeEval = $this->orderCount(4);
			
			$this->assign("tobepaid",$tobePaid);
			$this->assign("tobeshipped",$tobeShipped);
			$this->assign("tobereceipt",$tobeReceipt);
			$this->assign("tobeeval",$tobeEval);
			
			$rank = convert2LevelImg($this->userinfo['exp']);
			$this->assign("rank",$rank);
			$this->theme($this->themeType)->display();
		}
	}
	
	/**
	 * 个人订单
	 */
	public function order(){
		
		if(IS_GET){
			$datatype = I('get.datatype',0);
			$this->assign("datatype",$datatype);
			$this->theme($this->themeType)->display();
		}
		
	}
	
	public function withdraw(){
		$map=array(
			'uid'=>$this->userinfo['uid'],
			//'uid'=>236,
		);
		$result=apiCall(WalletApi::GET_INFO_If_NOT_EXIST_THEN_ADD,array($map));
		$this->assign('wallet',$result);
		$this->theme($this->themeType)->display();
		
	}
	
	
	public function add(){
		if(IS_GET){
			$map=array(
			'parentid'=>27,
			);
			$result=apiCall(DatatreeApi::QUERY_NO_PAGING,array($map));
			$this->assign('accountType',$result['info']);
			$this->theme($this->themeType)->display();
		}else if(IS_AJAX){
			$map=array(
				'uid'=>$this->userinfo['uid'],
				'money'=>I('money'),
				'accountType'=>I('accountType'),
				'accountName'=>I('accountName'),
				'bankBranch'=>I('bankBranch'),
				'cashAccount'=>I('cashAccount'),
				'reason'=>'提现'
			);
			$result=apiCall(WalletApi::MINUS,array($map));
			if($result['status']){
				$this->success('操作成功',U('Shop/Index/index'));
			}else{
				LogRecord($result['info'], __FILE__.__LINE__);
				$this->error($result['info']);
			}
		}
		
		/*$map=array(
			'uid'=>$this->userinfo['uid'],
			'money'=>50,
			'accountType'=>28,
			'accountName'=>'张铭',
			'bankBranch'=>'1235',
			'cashAccount'=>'3212356465464',
			'reason'=>'提现'
			
		);
		apiCall(WalletApi::MINUS,array($map));*/
		
		
	}

	/**
	 * 提现记录
	 */
	public function withdrawRecord(){
		$map=array(
			'uid'=>$this->userinfo['uid'],
			'dtree_type'=>getDatatree('COMMISSION_WITHDRAW'),
		);
		$order="create_time desc";
		$result=apiCall(WalletHisApi::QUERY_NO_PAGING,array($map,$order));
		$this->assign('recordList',$result['info']);
		$this->theme($this->themeType)->display();
		
	}
	
	
	
	
	private function orderCount($type){
		
		$map = array();
		if ($type == 1) {
			//待付款
			$map['pay_status'] = OrdersModel::ORDER_TOBE_PAID;
		} elseif($type != 0) {
			//货到付款，在线已支付
			$map['pay_status'] = array('in', array(OrdersModel::ORDER_PAID, OrdersModel::ORDER_CASH_ON_DELIVERY));

		}

		if ($type == 2) {
			//1. 已支付、货到付款
			//2. 待发货
			//
			$map['order_status'] = OrdersModel::ORDER_TOBE_SHIPPED;

		} elseif ($type == 3) {
			//1. 已支付、货到付款
			//2. 已发货
			$map['order_status'] = OrdersModel::ORDER_SHIPPED;
			$shouldGetExpressInfo = true;
		} elseif ($type == 4) {
			//1. 已支付、货到付款
			//2. 已收货
			//3. 待评论
			$map['order_status'] = OrdersModel::ORDER_RECEIPT_OF_GOODS;
			$map['comment_status'] = OrdersModel::ORDER_TOBE_EVALUATE;
			$shouldGetExpressInfo = true;
		}

		$map['wxuser_id'] = $this -> userinfo['id'];
		//TODO: 订单假删除时不查询
		$map['status'] = 1;
		$orders = " createtime desc ";

		$result = apiCall(OrdersApi::COUNT, array($map));
		
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		return $result['info'];
	}
	
	
	
}

