<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/2
 * Time: 14:21
 */

namespace Shop\Api;


use Common\Api\Api;
use Shop\Model\WalletHisModel;

class WalletHisApi extends Api{
	const ADD="Shop/WalletHis/add";
	
	const QUERY_NO_PAGING="Shop/WalletHis/queryNoPaging";
	
    protected function _init(){
        $this->model = new WalletHisModel();
    }

}