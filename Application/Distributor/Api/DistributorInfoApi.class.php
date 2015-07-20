<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/4
 * Time: 16:55
 */

namespace Distributor\Api;

use Common\Api\Api;
use Distributor\Model\DistributorInfoModel;

class DistributorInfoApi extends Api{
    /**
     *
     * 删除
     */
    const DELETE = "Distributor/DistributorInfo/delete";
    /**
     *
     * 获取一条数据，根据ID
     */
    const GET_INFO = "Distributor/DistributorInfo/getInfo";
    /**
     *
     * 保存，根据ID
     */
    const SAVE_BY_ID = "Distributor/DistributorInfo/saveByID";
	
	   /**
     *
     * 保存
     */
    const ADD = "Distributor/DistributorInfo/add";
	
	
	
	
    /**
     *
     * 保存
     */
    const SAVE = "Distributor/DistributorInfo/save";

    /**
     *
     * 分页获取
     */
    const QUERY = "Distributor/DistributorInfo/query";


    protected function _init(){
        $this->model = new DistributorInfoModel();
    }


}