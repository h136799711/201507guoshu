<?php
namespace Distributor\Controller;
use Distributor\Api\CommissionCountApi;
use Think\Controller;
use Admin\Api\GroupAccessApi;

class TestController extends Controller{
	
	public function index(){
		$id_arr=array(4,6);
		apiCall(CommissionCountApi::ADD,array($id_arr));
		//dump(time());
	}
}
