<?php
namespace Distributor\Api;
use Common\Model\GroupAccessModel;
use	Common\Api\Api;

class GroupAccessApi extends Api{
	
	const QUERY_NO_PAGING="Distributor/GroupAccess/queryNoPaging";
	
	
	protected function _init(){
		$this->model = new GroupAccessModel();
	}
	
}
