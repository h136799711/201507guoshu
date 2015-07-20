<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 行为日志控制器
 * @author 贝贝 <hebiduhebi@163.com>
 */
class ActionLogController extends AdminController {
	/**
	 * 根据条件字段获取数据
	 * @param mixed $value 条件，可用常量或者数组
	 * @param string $condition 条件字段
	 * @param string $field 需要返回的字段，不传则返回整个数据
	 * @author huajie <banhuajie@163.com>
	 */
	function get_document_field($value = null, $condition = 'id', $field = null){
	    if(empty($value)){
	        return false;
	    }
	
	    //拼接参数
	    $map[$condition] = $value;
	    $info = M('Model')->where($map);
	    if(empty($field)){
	        $info = $info->field(true)->find();
	    }else{
	        $info = $info->getField($field);
	    }
	    return $info;
	}
	
	/**
	 * 获取行为类型
	 * @param intger $type 类型
	 * @param bool $all 是否返回全部类型
	 * @author huajie <banhuajie@163.com>
	 */
	function get_action_type($type, $all = false){
	    $list = array(
	        1=>'系统',
	        2=>'用户',
	    );
	    if($all){
	        return $list;
	    }
	    return $list[$type];
	}

    /**
     * 行为日志列表
     */
    public function index(){
        //获取列表数据
        $map['status']    =   array('gt', -1);
		$page = array('curpage'=>I("p",0),'size'=>C("LIST_ROWS"));
		
		$result = apiCall("Admin/ActionLog/query", array($map));
		$this->exitIfError($result);
		
//      $list   =   $this->lists('ActionLog', $map);
		$list = $result['info']['list'];
        foreach ($list as $key=>$value){
//          $model_id                  =   $this->get_document_field($value['model'],"name","id");
//          $list[$key]['model_id']    =   $model_id ? $model_id : 0;
        }
		
		$this->assignTitle("行为日志");
        $this->assign('_list', $list);
		$this->assign("_page",$result['info']['show']);
        $this->display();
    }

    /**
     * 查看行为日志
     */
    public function view($id = 0){
        empty($id) && $this->error('参数错误！');
		
		$map = array('id'=>$id);
		
		$result = apiCall("Admin/ActionLog/getInfo", array($map));
		
		$this->exitIfError($result);
		
        $this->assign('info', $result['info']);
        $this->meta_title = '查看行为日志';
        $this->display();
    }

    /**
     * 删除日志
     * @param mixed $ids
     * @author huajie <banhuajie@163.com>
     */
    public function remove($ids = 0){
        empty($ids) && $this->error('参数错误！');
        if(is_array($ids)){
            $map['id'] = array('in', $ids);
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        }
		
        $res = D('ActionLog')->where($map)->delete();
        if($res !== false){
            $this->success('删除成功！');
        }else {
            $this->error('删除失败！');
        }
    }

    /**
     * 清空日志
     */
    public function clear(){
        $res = D('ActionLog')->where('1=1')->delete();
        if($res !== false){
            $this->success('日志清空成功！');
        }else {
            $this->error('日志清空失败！');
        }
    }

}
