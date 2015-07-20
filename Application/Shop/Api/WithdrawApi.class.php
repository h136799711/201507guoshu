<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/2
 * Time: 14:22
 */

namespace Shop\Api;


use Common\Api\Api;
use Shop\Model\WithdrawModel;
use Shop\Api\WalletApi;

class WithdrawApi extends Api{

    /**
     * 查询，不分页
     */
    const QUERY_NO_PAGING = "Shop/Withdraw/queryNoPaging";
    /**
     * 查询，分页
     */
    const QUERY = "Shop/Withdraw/query";
    /**
     * 添加
     */
    const ADD = "Shop/Withdraw/add";
    /**
     * 保存
     */
    const SAVE = "Shop/Withdraw/save";
    /**
     * 获取信息
     */
    const GET_INFO = "Shop/Withdraw/getInfo";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/Withdraw/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/Withdraw/delete";

	const PASS_WITHDRAW="Shop/Withdraw/passWithdraw";

    protected function _init(){
        $this->model = new WithdrawModel();
    }
	
	
	/**
	 * 审核通过
	 */
	public function passWithdraw($map){
		
		$trans = M();
        $trans->startTrans(); //开启事务
		
		$result=apiCall(WithdrawApi::SAVE_BY_ID,array($map['id'],array('status'=>WithdrawModel::PASS)));
		if($result['status']){
			$result=apiCall(WithdrawApi::QUERY_NO_PAGING,array($map));
			$map=array(
				'uid'=>$result['info'][0]['uid'],
			);
			$result1=apiCall(WalletApi::QUERY_NO_PAGING,array($map));
			$array=$result1['info'][0];
			$array['frozen_funds']=(float)$array['frozen_funds']-(float)$result['info'][0]['money'];
			
			$result2=apiCall(WalletApi::SAVE_BY_ID,array($array['id'],$array));
			if($result2['status']){
				$map=array(
				'uid'=>$result['info'][0]['uid'],
				'before_money'=>(float)$array['account_balance']+(float)$result['info'][0]['money'],
				'plus'=>0,
				'minus'=>$result['info'][0]['money'],
				'after_money'=>$array['account_balance'],
				'create_time'=>time(),
				'dtree_type'=>getDatatree('COMMISSION_CHECK_PASS'),
				'reason'=>'审核通过'
				);
				$result=apiCall(WalletHisApi::ADD,array($map));
				if($result['status']){
					$trans->commit();//提交事务
					return $result;
				}else{
					$trans->rollback();//回滚事务
					return $result['info'];
				}
			}else{
				$trans->rollback();//回滚事务
				return $result2['info'];
			}
			
		}else{
			$trans->rollback();//回滚事务
			return $result['info'];
		}
		
	}

}