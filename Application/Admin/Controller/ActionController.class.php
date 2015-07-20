<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 行为控制器
 * @author 贝贝 <hebiduhebi@163.com>
 */
class ActionController extends AdminController {
	
	 /**
     * 用户行为列表
     */
    public function index(){
    		$map = array('status'=>array('gt',-1));
		$page = array('curpage'=>I("p",0),'size'=>C("LIST_ROWS"));
		$order = "update_time desc";
        //获取列表数据
        $result = apiCall("Admin/Action/query", array($map,$page,$order));
		$this->exitIfError($result);
		
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->assign('list', $result['info']['list']);
        $this->assign('show', $result['info']['show']);
        $this->assignTitle('用户行为');
        $this->display();
    }

    /**
     * 新增行为
     */
    public function add(){
        	if(IS_GET){
        		$this->assignTitle('新增行为');
        		$this->display();
		}else{
			$entity = I('post.');
			
			$result = apiCall("Admin/Action/add", array($entity));
			
			$this->exitIfError($result);
			
			$this->success("添加成功!",Cookie('__forward__'));
			
		}
    }
	
    /**
     * 编辑行为
     */
    public function edit(){
        $id = I('get.id');
        empty($id) && $this->error('参数不能为空！');
        	if(IS_GET){
        		$result = apiCall("Admin/Action/getInfo", array(array("id"=>$id)));
			$this->exitIfError($result);
			
			$this->assign("vo",$result['info']);
        		$this->assignTitle('编辑行为');
        		$this->display();
		}else{
			$entity = I('post.');
			
			$result = apiCall("Admin/Action/saveByID", array($id,$entity));
			
			$this->exitIfError($result);
			
			$this->success("更新成功!",Cookie('__forward__'));
			
		}
    }
	
	
	/**
	 * 禁用/批量禁用
	 * id,ids
	 */
	public function disable(){
		
		$ids = I("get.ids", -1);
		if ($ids === -1) {
			$ids = I("post.ids", -1);
		}
		if ($ids === -1) {
			$this -> error("缺少参数！");
		}
		
		
		if(is_array($ids)){
			$map = array('id' => array('in', $ids));
		}else{
			$map = array("id"=>$ids);
		}
		
		$result = apiCall("Admin/Action/disable", array($map));
		
		if ($result['status']) {
			if (IS_AJAX) {
				$this -> success("禁用成功！");
			} else {
				$this -> success("禁用成功！", U('Admin/Action/index'));
			}
		} else {
			$this -> error($result['info']);
		}
	
	}
	
	/**
	 * 启用/批量启用
	 * id,ids
	 */
	public function enable(){
		
		$ids = I("get.ids", -1);
		if ($ids === -1) {
			$ids = I("post.ids", -1);
		}
		if ($ids === -1) {
			$this -> error("缺少参数！");
		}
		
		if(is_array($ids)){
			$map = array('id' => array('in', $ids));
		}else{
			$map = array("id"=>$ids);
		}
		
		$result = apiCall("Admin/Action/enable", array($map));
		
		if ($result['status']) {
			if (IS_AJAX) {
				$this -> success("启用成功！");
			} else {
				$this -> success("启用成功！", U('Admin/Action/index'));
			}
		} else {
			$this -> error($result['info']);
		}
	
	}
	
	/**
	 * 删除
	 * id,ids
	 */
	public function delete(){
		$ids = I("get.ids", -1);
		if ($ids === -1) {
			$ids = I("post.ids", -1);
		}
		if ($ids === -1) {
			$this -> error("缺少参数！");
		}
		
		$id_arr = implode(',', $ids);
		
		if(is_array($id_arr)){
			$map = array('id' => array('in', $id_arr));
		}else{
			$map = array("id"=>$ids);
		}
		
		
		$result = apiCall("Admin/Action/pretendDelete", array($map));
		
		if ($result['status']) {
			if (IS_AJAX) {
				$this -> success("删除成功！");
			} else {
				$this -> success("删除成功！", U('Admin/Action/index'));
			}
		} else {
			$this -> error($result['info']);
		}
	
	}
	
}
