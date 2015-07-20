<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/1
 * Time: 08:52
 */

namespace Admin\Api;


use Admin\Model\HooksModel;
use Common\Api\Api;

class HooksApi extends Api{

    /**
     * 获取单条数据
     */
    const GET_INFO = "Admin/Hooks/getInfo";
    /**
     * 删除
     */
    const DELETE = "Admin/Hooks/delete";
    /**
     * 不分页查询
     */
    const QUERY_NO_PAGING = "Admin/Hooks/queryNoPaging";
    /**
     * 分页查询
     */
    const QUERY = "Admin/Hooks/query";

    /**
     * 移除插件
     */
    const REMOVE_ADDONS = "Admin/Hooks/removeAddons";
    /**
     * 移除插件对应的所有钩子数据
     */
    const REMOVE_HOOKS = "Admin/Hooks/removeHooks";
    /**
     * 更新钩子对应的插件
     */
    const UPDATE_HOOKS = "Admin/Hooks/updateHooks";
    /**
     * 更新插件对应的所有钩子数据
     */
    const UPDATE_ADDONS = "Admin/Hooks/updateAddons";

    protected  function _init(){
        $this->model = new HooksModel();
    }


    /**
     * 更新插件里的所有钩子对应的插件
     */
    public function updateHooks($addons_name){
        $addons_class = get_addon_class($addons_name);//获取插件名
        if(!class_exists($addons_class)){
            $error = "未实现{$addons_name}插件的入口文件";
            return $this->apiReturnErr($error);
        }
        $methods = get_class_methods($addons_class);
        $hooks = $this->getModel()->getField('name', true);
        $common = array_intersect($hooks, $methods);
        if(!empty($common)){
            foreach ($common as $hook) {
                $flag = $this->updateAddons($hook, array($addons_name));
                if(false === $flag){
                    $this->removeHooks($addons_name);
                    return $this->apiReturnErr($this->getModel()->getDbError());
                }
            }
        }
        return $this->apiReturnSuc("更新成功!");
    }

    /**
     * 更新单个钩子处的插件
     */
    public function updateAddons($hook_name, $addons_name){
        $o_addons = $this->getModel()->where("name='{$hook_name}'")->getField('addons');
        if($o_addons)
            $o_addons = explode(",",$addons);
        if($o_addons){
            $addons = array_merge($o_addons, $addons_name);
            $addons = array_unique($addons);
        }else{
            $addons = $addons_name;
        }

        $flag = $this->getModel()->where("name='{$hook_name}'")->setField('addons',implode(",",$addons));
        if($flag === FALSE){
            return $this->apiReturnErr($this->getModel()->getDbError());
        }
        return $this->apiReturnSuc($flag);
    }

    /**
     * 去除插件所有钩子里对应的插件数据
     */
    public function removeHooks($addons_name){
        $addons_class = get_addon_class($addons_name);
        if(!class_exists($addons_class)){
            return false;
        }
        $methods = get_class_methods($addons_class);
        $hooks = $this->getModel()->getField('name', true);
        $common = array_intersect($hooks, $methods);
        if($common){
            foreach ($common as $hook) {
                $flag = $this->removeAddons($hook, array($addons_name));
                if(false === $flag) {
                    return $this->apiReturnErr($this->getModel()->getDbError());
                }
            }
        }
        return $this->apiReturnSuc("操作成功!");
    }

    /**
     * 去除单个钩子里对应的插件数据
     * @param $hook_name
     * @param $addons_name
     * @return array|bool
     */
    public function removeAddons($hook_name, $addons_name){
        $o_addons = $this->getModel()->where("name='{$hook_name}'")->getField('addons');

        $o_addons = explode(",",$o_addons);

        if($o_addons){
            $addons = array_diff($o_addons, $addons_name);
        }else{
            return true;
        }
        $flag = $this->getModel()->where("name='{$hook_name}'")
            ->setField('addons',implode(",",$addons));
        if(false === $flag)
            $flag = $this->getModel()->where("name='{$hook_name}'")->setField('addons',implode(",",$addons));

        if($flag === FALSE){
            return $this->apiReturnErr($this->getModel()->getDbError());
        }
        return $this->apiReturnSuc($flag);
    }


}