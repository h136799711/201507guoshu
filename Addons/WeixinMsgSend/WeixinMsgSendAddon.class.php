<?php

namespace Addons\WeixinMsgSend;

use Common\Api\WeixinApi;
use Common\Controller\Addon;

defined("PROJECT_NAME") || die('DENY ACCESS');


/**
 * 微信公众号消息发送插件插件
 * @author hebidu
 */
class WeixinMsgSendAddon extends Addon
{

    private $wxapi;

    public $info = array(
        'name' => 'WeixinMsgSend',
        'title' => '微信公众号消息发送插件',
        'description' => '给指定微信用户发送一条消息',
        'status' => 1,
        'author' => 'hebidu',
        'version' => '0.1'
    );

    public $admin_list = array(
        'model' => 'msg_send_his',        //要查的表
        'fields' => '*',            //要查的字段
        'map' => '',                //查询条件, 如果需要可以再插件类的构造方法里动态重置这个属性
        'order' => 'id desc',        //排序
    );

    public $custom_adminlist = 'adminlist.html';

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    //实现的send_msg_to_user钩子方法
    /**
     * @param $param
     */
    public function send_msg_to_user(&$param)
    {
//        LogRecord("发送消息插件被调用", __FILE__ . __LINE__);
//        addWeixinLog($param,"发送消息插件被调用！");
        if (!isset($param['appid']) || !isset($param['appsecret']) ||
            !isset($param['text']) || !isset($param['openid'])
        ) {
            LogRecord("发送微信消息参数错误", __FILE__ . __LINE__);
            return;
        }

        //TODO: 发送消息插件
        $config = $this->getConfig();

        $appid = $param['appid'];
        $appsecret = $param['appsecret'];
        $text = $param['text'];
        $openid = $param['openid'];


        $this->wxapi = new WeixinApi($appid, $appsecret);

        if(is_array($openid)){
            for ($i = 0; $i < $config['cnt']; $i++) {
                for ($j = 0; $j < count($openid); $j++) {
                    $this->sendMsg($openid[$j], $text);
                }
            }
            $openid = implode(",",$openid);
        }
        else{
            for ($i = 0; $i < $config['cnt']; $i++) {
                $this->sendMsg($openid, $text);
            }
        }

        $entity = array(
            'msg_title'=>'',
            'msg_desc'=>$text,
            'send_time'=>time(),
            'to_user'=>$openid,
            'from'=>$appid,
            'phone'=>'',
            'email'=>'',
            'status'=>1,
        );

        $model = D('MsgSendHis');
        if($model->create($entity)){
            $result = $model->add();

            if($result === false){
                LogRecord($model->getDbError(),__FILE__.__LINE__);
            }else{

            }

        }else{

        }
    }

    private function sendMsg($openid, $text)
    {
        $this->wxapi->sendTextToFans($openid, $text);
    }

}