<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/6/29
 * Time: 21:42
 */

namespace Admin\Api;

use Admin\Model\AddonsModel;
use Common\Api\Api;
use Think\Log;

class AddonsApi  extends Api {


    /**
     *
     * 删除
     */
    const DELETE = "Admin/Addons/delete";
    /**
     *
     * 获取一条数据，根据ID
     */
    const GET_INFO = "Admin/Addons/getInfo";
    /**
     *
     * 保存，根据ID
     */
    const SAVE_BY_ID = "Admin/Addons/saveByID";
    /**
     *
     * 保存
     */
    const SAVE = "Admin/Addons/save";
    /**
     *
     * 分页获取
     */
    const QUERY = "Admin/Addons/query";
    /**
     *
     * 获取插件信息
     */
    const GET_LIST = "Admin/Addons/getList";
    /**
     *
     * 获取插件的后台列表
     */
    const GET_ADMIN_LIST = "Admin/Addons/getAdminList";
    /**
     *
     * 禁用
     */
    const DISABLE = "Admin/Addons/disable";
    /**
     *
     * 启用
     */
    const ENABLE = "Admin/Addons/enable";

    /**
     *
     */
    protected  function  _init(){
        $this->model = new AddonsModel();
    }


    /**
     * 获取插件列表
     * @param string $addon_dir
     * @return array
     */
    public function getList($addon_dir = ''){
        if(!$addon_dir)
            $addon_dir = ADDON_PATH;
        $dirs = array_map('basename',glob($addon_dir.'*', GLOB_ONLYDIR));
        if($dirs === FALSE || !file_exists($addon_dir)){
            $this->error = '插件目录不可读或者不存在';
            return FALSE;
        }
        $addons			=	array();
        $where['name']	=	array('in',$dirs);
        $list			=	$this->getModel()->where($where)->field(true)->select();
        foreach($list as $addon){
            $addon['uninstall']		=	0;
            $addons[$addon['name']]	=	$addon;
        }
        foreach ($dirs as $value) {
            if(!isset($addons[$value])){
                $class = get_addon_class($value);
                if(!class_exists($class)){ // 实例化插件失败忽略执行
                    Log::record('插件'.$value.'的入口文件不存在！');
                    continue;
                }
                $obj    =   new $class;
                $addons[$value]	= $obj->info;
                if($addons[$value]){
                    $addons[$value]['uninstall'] = 1;
                    unset($addons[$value]['status']);
                }
            }
        }

        $addons = int_to_string($addons, 'status',array(-1=>'损坏', 0=>'禁用', 1=>'启用', null=>'未安装'));
//        $addons = list_sort_by($addons,'uninstall','desc');
        return $addons;
    }


    /**
     * 获取插件的后台列表
     */
    public function getAdminList(){
        $admin = array();
        $db_addons = $this->getModel()->where("status=1 AND has_adminlist=1")->field('title,name')->select();
        if($db_addons){
            foreach ($db_addons as $value) {
                $admin[] = array('title'=>$value['title'],'url'=>"Addons/adminList?name={$value['name']}");
            }
        }else{
            return $this->apiReturnErr($this->model->getDbError());
        }
        return $this->apiReturnSuc($admin);
    }
}