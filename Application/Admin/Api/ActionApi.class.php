<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------


namespace Admin\Api;

use \Common\Api\Api;
use \Admin\Model\ActionModel;

class ActionApi extends Api{


	protected function _init(){
		$this->model = new ActionModel();
	}
	
	/**
     * 新增或更新一个行为
     * @return boolean false 失败 ， int  成功 返回完整的数据
     */
    public function update($entity){
        /* 获取数据对象 */
        $data = $this->model->create($entity);
        if(empty($data)){
            return $this->apiReturnErr($this->model->getError());
        }
		
        /* 添加或新增行为 */
        if(empty($data['id'])){ //新增数据
            $id = $this->add(); //添加行为
            if(!$id){
	            return $this->apiReturnErr("新增行为出错!");
            }
        } else { //更新数据
            $status = $this->save(); //更新基础内容
            if(false === $status){
	            return $this->apiReturnErr("更新行为出错!");
            }
        }
		
        //内容添加或更新完成
        return $data;

    }
}
