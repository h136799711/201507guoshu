<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/2
 * Time: 13:46
 */

namespace Admin\Controller;


use Admin\Api\AuthGroupAccessApi;
use Admin\Api\DatatreeApi;
use Admin\Api\MemberApi;
use Shop\Api\WithdrawApi;
use Shop\Model\WithdrawModel;
use Uclient\Api\UserApi;

class WithdrawController extends AdminController{
    protected  function _initialize(){
        parent::_initialize();
    }

    /**
     * 提现审核
     */
    public function verify(){

        $uid = I('uid',0);
        $where = array();
        $params = array();
        if($uid != 0){
            $where['uid'] = $uid;
            $params['uid'] = $uid;
        }

        $where['status'] = WithdrawModel::WAIT_VERIFY;
        $page = array('curpage'=>I('get.p',0),'size'=>10);
        $order = " update_time asc ";
        $result = apiCall(WithdrawApi::QUERY,array($where,$page,$order,$params));

        ifFailedLogRecord($result,__FILE__.__LINE__);

        $list = $this->type2dtree($result['info']['list']);


        $this->assign("list",$list);
        $this->assign("show",$result['info']['show']);

        $this->display();
    }

    /**
     * 提现历史查询
     */
    public function query(){

        $arr = getDataRange(3);
		//dump($arr);
        $uid = I('uid',0);
        $where = array();
        $params = array();
        $status = I('status',"");
        if($uid != 0){
            $where['uid'] = $uid;
            $params['uid'] = $uid;
        }

        $startdatetime = urldecode($arr[0]);
        $enddatetime = urldecode($arr[1]);

        $params = array('startdatetime' => $startdatetime, 'enddatetime' => ($enddatetime),'wxaccountid'=>getWxAccountID());

        $startdatetime = strtotime($startdatetime);
        $enddatetime = strtotime($enddatetime);

        if ($startdatetime === FALSE || $enddatetime === FALSE) {
            LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
            $this -> error(L('ERR_DATE_INVALID'));
        }

        $where['create_time'] = array( array('EGT', $startdatetime), array('elt', $enddatetime), 'and');
		//dump(!empty($status));
       	if($status!=""&& $status != -1 ){
            $where['status'] = $status;
        }

        $page = array('curpage'=>I('get.p',0),'size'=>10);
        $order = " update_time asc ";

        $result = apiCall(WithdrawApi::QUERY,array($where,$page,$order,$params));
		//dump($where);
		//dump($result);
		
       ifFailedLogRecord($result,__FILE__.__LINE__);

        $list = $this->type2dtree($result['info']['list']);

        $list = int_to_string($list,"status",array(0=>"待审","1"=>"通过",2=>"驳回"));
		$this->assign("status",$status);
        $this->assign("list",$list);
        $this->assign("show",$result['info']['show']);
        $this->assign("startdatetime",$startdatetime);
        $this->assign("enddatetime",$enddatetime);
        $this->display();
    }


    public function viewAccount(){
        $id = I('get.id',0);

        $result = apiCall(MemberApi::GET_INFO, array(array("uid"=>$id)));
        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->assign("userinfo",$result['info']);

        $result = apiCall(WithdrawApi::GET_INFO, array(array('id'=>$id)));

        if(!$result['status']){
            $this->error($result['info']);
        }

        $this->assign("withdraw",$result['info']);

        $this->display();
    }

    public function pass(){
        $id = I('get.id',0);
       // $id=11;
        if(empty($id)){
            $this->error("ID 参数缺失!");
        }
		$map=array(
			'id'=>$id
		);
		
		$result=apiCall(WithdrawApi::PASS_WITHDRAW,array($map));
		
        /*$result = apiCall(WithdrawApi::SAVE_BY_ID,array($id,array('status'=>WithdrawModel::PASS)));*/

        ifFailedLogRecord($result,__FILE__.__LINE__);

        $this->success("操作成功!");

    }
	
	

    public function deny(){
        $id = I('get.id',0);
        if(empty($id)){
            $this->error("ID 参数缺失!");
        }

        $result = apiCall(WithdrawApi::SAVE_BY_ID,array($id,array('status'=>WithdrawModel::DENY)));

        ifFailedLogRecord($result,__FILE__.__LINE__);

        $this->success("操作成功!");
    }


    //private

    private  function type2dtree($list){
        $dtree_ids = array(-1);
        foreach($list as $vo){
            array_push($dtree_ids,$vo['dtree_account_type']);
        }

        $where = array(
            'id'=>array("in",$dtree_ids)
        );

        $result = apiCall(DatatreeApi::QUERY_NO_PAGING,array($where));

        ifFailedLogRecord($result,__LINE__.__LINE__);

        $dtree_type = $result['info'];

        foreach($list as &$vo){
            foreach ($dtree_type as $type) {

                if($vo['dtree_account_type'] == $type['id']){
                    $vo['_account_type'] = $type['name'];
                }

            }

        }
        return $list;
    }
}