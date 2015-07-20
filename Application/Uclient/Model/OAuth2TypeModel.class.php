<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/1
 * Time: 16:11
 */

namespace Uclient\Model;

/**
 * 第三方账户登录\注册类型枚举
 * Class OAuth2FromEnum
 * @package Uclient\Model
 */
class OAuth2TypeModel {
    /**
     * 来源QQ注册
     */
    const  QQ = 1;
    /**
     * 来源微信注册
     */
    const  WEIXIN = 2;
    /**
     * 来源百度注册
     */
    const  SINA = 3;
    /**
     * 来源百度注册
     */
    const  BAIDU = 4;
    /**
     * 来源其它内部应用登录
     */
    const  OTHER_APP = 99;
    /**
     * 来源其它内部应用登录
     */
    const  SELF = 0;

    /**
     * 检测类型是否合法
     * @param $type
     * @return bool
     */
    static public  function checkType($type){
        $type = intval($type);
        if(is_int($type)){
            if($type >= 0 && $type < 5){
                return true;
            }
            if($type == 99){
                return true;
            }

        }

        return false;
    }

}