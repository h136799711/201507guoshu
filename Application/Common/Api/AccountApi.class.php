<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/3
 * Time: 09:29
 */

namespace Common\Api;

use Admin\Api\MemberApi;
use Common\Model\WxuserGroupModel;
use Uclient\Api\UserApi;
use Weixin\Api\WxuserApi;
use Weixin\Api\WxuserFamilyApi;

interface IAccount
{


    function login($username, $password, $email, $phone, $from);

    function register($entity);

    function getInfo($id);

}

/**
 * 本系统账号相关操作统一接口
 * Class AccountApi
 * @package Common\Api
 */
class AccountApi implements IAccount
{
    /**
     * 登录
     */
    const LOGIN = "Common/Account/login";
    /**
     * 注册
     */
    const REGISTER = "Common/Account/register";
    /**
     * 获取用户信息
     */
    const GET_INFO = "Common/Account/getInfo";

    public function getInfo($id){

        $result = apiCall(UserApi::GET_INFO, array($id));
//        id,username,email,mobile,status
        if(!$result['status']){
            return array('status' => false, 'info' => $result['info']);
        }

        $user_info = $result['info'];

        $result = apiCall(MemberApi::GET_INFO, array(array('id'=>$id)));

        if(!$result['status']){
            return array('status' => false, 'info' => $result['info']);
        }

        $member_info = $result['info'];

        $result = apiCall(WxuserApi::GET_INFO, array(array('id'=>$id)));

        if(!$result['status']){
            return array('status' => false, 'info' => $result['info']);
        }

        $wxuser_info = $result['info'];

        $info = array_merge($user_info,$member_info);
        $info['_wxuser'] = $wxuser_info;
        return array('status'=>true,'info'=>$info);
    }


    public function login($username, $password, $email, $phone, $from)
    {
        // TODO: Implement login() method.
        return true;
    }

    /**
     *
     * @param $entity | key＝》username,password ,from . email,mobile非必须
     * @return array
     */
    public function register($entity)
    {


        if (!isset($entity['username']) || !isset($entity['password']) || !isset($entity['from'])) {
            return array('status' => false, 'info' => "账户信息缺失!");
        }

        if (!isset($entity['_wxuser'])) {
            return array('status' => false, 'info' => "账户信息缺失!");
        }

        $wxuser = $entity['_wxuser'];
        if (!isset($wxuser['wxaccount_id']) || !isset($wxuser['openid'])) {
            return array('status' => false, 'info' => "账户信息缺失!");
        }

        $empty_check = array('nickname','avatar','province','country','city');
        foreach($empty_check as $vo){
            if(!isset($wxuser[$vo])){
                $wxuser[$vo] = '';
            }
        }
        if(!isset($wxuser['referrer'])) $wxuser['referrer'] = 0;
        if(!isset($wxuser['sex'])) $wxuser['sex'] = 0;
        if(!isset($wxuser['subscribed'])) $wxuser['subscribed'] = 0;
        if(!isset($wxuser['subscribe_time'])) $wxuser['subscribe_time'] = time();

        $username = $entity['username'];
        $password = $entity['password'];
        $email = $entity['email'];
        $mobile = $entity['mobile'];
        $from = $entity['from'];

        $trans = M();
        $trans->startTrans();
        $error = "";
        $flag = false;
        $result = apiCall(UserApi::REGISTER, array($username, $password, $email, $mobile, $from));
        $uid = 0;
        if ($result['status']) {
            $uid = $result['info'];

            $member = array(
                'uid' => $uid,
                'realname' =>  $wxuser['nickname'],
                'nickname' => $wxuser['nickname'],
                'idnumber' => '',
                'sex' =>  $wxuser['sex'],
                'birthday' => time(),
                'qq' => '',
                'score' => 0,
                'login' => 0,
            );

            $result = apiCall(MemberApi::ADD, array($member));
            if (!$result['status']) {
                $flag = true;
                $error = $result['info'];
            }
            if(!$flag){
                //创建关系表
                $result = apiCall(WxuserFamilyApi::CREATE_ONE_IF_NONE,array($uid,$wxuser['referrer']));
                if (!$result['status']) {
                    $flag = true;
                    $error = $result['info'];
                }
            }

            if(!$flag){
                $wxuser['uid'] = $uid;
                $wxuser['groupid'] = WxuserGroupModel::DEFAULT_GROUP ;//
                //继续注册wxuser表
                $result = apiCall(WxuserApi::REGISTER,array($wxuser));
//                dump($result);
                if (!$result['status']) {
                    $flag = true;
                    $error = $result['info'];
                }
            }
        } else {
            $flag = true;
            $error = $result['info'];
        }


        if ($flag) {
            apiCall(UserApi::DELETE_BY_ID, array($uid));
            $trans->rollback();
            return array('status' => false, 'info' => $error);
        } else {
            $trans->commit();
            return array('status' => true, 'info' => $uid);
        }

    }
}