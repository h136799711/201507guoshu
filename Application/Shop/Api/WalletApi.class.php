<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/2
 * Time: 14:20
 */

namespace Shop\Api;


use Common\Api\Api;
use Shop\Model\WalletModel;
use Shop\Model\WithdrawModel;
use Shop\Api\WithdrawApi;
use Shop\Api\WalletHisApi;

class WalletApi extends Api{
	const GET_INFO_If_NOT_EXIST_THEN_ADD="Shop/Wallet/getInfoIfNotExistThenAdd";
	
	const PLUS="Shop/Wallet/plus";
	
	const MINUS="Shop/Wallet/minus";
	
	const SAVE_BY_ID="Shop/Wallet/saveByID";
	
	/**
	 * 改数字字段
	 */
	const SETINC="Shop/Wallet/setInc";
	
	const QUERY_NO_PAGING="Shop/Wallet/queryNoPaging";

    protected function _init(){
        $this->model = new WalletModel();
    }
	
	/**
	 * 有钱包则返回钱包，没有则添一个
	 * author:小帅
	 * time:2015/7/8
	 */
	public function getInfoIfNotExistThenAdd($maps){
		//UID
		$map=array(
			'uid'=>$maps['uid'],
		);
		//dump($map);
		
		$wallet = $this->model->where($map)->find();
		if($wallet==null){
			$wallet=M('Wallet');
			$time=time();
			$entity=array(
				'uid'=>$maps['uid'],
				'frozen_funds'=>0,
				'account_balance'=>0,
				'update_time'=>$time,
			);
			$w=$wallet->add($entity);
			$entity=array(
				'id'=>$w,
				'uid'=>$maps['uid'],
				'frozen_funds'=>0,
				'account_balance'=>0,
				'update_time'=>$time,
			);
			
			return $entity;
		}
		return $wallet;
	}
	
	/**
	 *	加佣金
	 * author:小帅
	 * time:2015/7/8
	 */
	public function plus($map){
		
		//操作三张表withdraw、wallet、wallet_his
		//dump($map);
		/*$entity=array(
			'uid'=>$map['uid'],
		);
		$wallet = $this->model->where($map)->find();
		$wallet['account_balance']=$wallet['account_balance']+$map['money'];
		//$wallet['account_balance']=$wallet['account_balance']-$map['money'];*/
		
		
	}
	
	/**
	 * 减佣金
	 */
	public function minus($map){
		$lastResult=array(
			'status'=>false,
			'info'=>'提取失败'
		);
		//先判断一下余额是否够减★★★★★★★★★★★★★★★★★★★★★★★★★★
		
		$trans = M();
        $trans->startTrans(); //开启事务
        //$error = "";
		
		
		$entity=array(
			'uid'=>$map['uid'],
		);
		
		$wallet = $this->model->where($map)->find();
		
		if((float)$wallet['account_balance']/100<(float)$map['money']){
			$lastResult['info']='余额不足，不能提取';
			return $lastResult;
		}
		$beforeMoney=$wallet['account_balance'];
		$wallet['account_balance']=(float)$wallet['account_balance']-(float)$map['money']*100;
		//$wallet['account_balance']=$wallet['account_balance']-$map['money'];
		$wallet['frozen_funds']=(float)$wallet['frozen_funds']+(float)$map['money']*100;
		$afterMoney=$wallet['account_balance'];
		$result=$this->saveByID($wallet['id'], $wallet);
		//如果修改失败则回滚
		//
		if($result['status']){
			$entity=array(
				'uid'=>$map['uid'],
				'money'=>(float)$map['money']*100,
				'create_time'=>time(),
				'status'=>0,
				'reason'=>'',
				'cash_account'=>$map['cashAccount'],
				'bank_branch'=>$map['bankBranch'],
				'account_name'=>$map['accountName'],
				'update_time'=>time(),
				'dtree_account_type'=>$map['accountType']
			);
			
			$result=apiCall(WithdrawApi::ADD,array($entity));
			if(!$result['status']){
				$trans->rollback();
				return $lastResult;
			}else{
				
				$entity=array(
					'uid'=>$map['uid'],
					'before_money'=>$beforeMoney,
					'plus'=>0,
					'minus'=>(float)$map['money']*100,
					'after_money'=>$afterMoney,
					'create_time'=>time(),
					'dtree_type'=>getDatatree('COMMISSION_WITHDRAW'),
					'reason'=>$map['reason'],
				);
				$result=apiCall(WalletHisApi::ADD,array($entity));
				
				if($result['status']){
					$trans->commit();//提交事务
					$lastResult['status']=true;
					$lastResult['info']='提取成功';
					return $lastResult;
				}else{
					$trans->rollback();//回滚事务
					return $lastResult;
				}
			}
			
		}else{
			$trans->rollback();//回滚事务
			return $lastResult;
		}
		//dump($wallet);
	}

}