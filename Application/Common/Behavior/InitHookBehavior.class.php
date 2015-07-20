<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/6/29
 * Time: 20:59
 */

namespace Common\Behavior;
use Think\Behavior;
use Think\Hook;

defined('PROJECT_NAME') or exit();

class InitHookBehavior extends Behavior{

    public function run(&$parms){
        //安装时不执行
        if(defined('BIND_MODULE') && BIND_MODULE === 'Install') return;

        $data = S('global_hooks');
        if(!$data){
            $hooks = M('Hooks',"common_")->getField('name,addons');
            foreach ($hooks as $key => $value) {
                if($value){
                    $map['status']  =   1;
                    $names          =   explode(',',$value);
                    $map['name']    =   array('IN',$names);
                    $data = M('Addons',"common_")->where($map)->getField('id,name');
                    if($data){
                        $addons = array_intersect($names, $data);
                        Hook::add($key,array_map('get_addon_class',$addons));
                    }
                }
            }
            S('global_hooks',Hook::get());
        }else{
            Hook::import($data,false);
        }
    }
}