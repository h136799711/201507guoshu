<?php

namespace Addons\WeixinPromotion;

use Admin\Api\GroupAccessApi;
use Common\Api\PromotioncodeApi;
use Common\Api\WeixinApi;
use Common\Controller\Addon;

defined("PROJECT_NAME") || die('DENY ACCESS');


/**
 * 微信接口二维码生成插件
 * @author hebidu
 */
class WeixinPromotionAddon extends Addon
{

    private $wxapi;
    private $config;
    public $info = array(
        'name' => 'WeixinPromotion',
        'title' => '微信接口二维码生成',
        'description' => '生成微信二维码',
        'status' => 1,
        'author' => 'hebidu',
        'version' => '0.1'
    );

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    //实现的admin_index钩子方法
    public function WeixinInnerProcess(&$param)
    {
        addWeixinLog($param,"插件二维码参数!");
        //固定，只处理这个关键词
        if($param['keyword'] === "_plugin_promotion"){
        }else{

            return ;
        }
        addWeixinLog("插件二维码处理","WeixinInnerProcess");
        $data = $param['data'];

        $this->config = $this->getConfig();

        if(empty($data['fans']) ){
            LogRecord("fans参数为empty", "[PromotioncodePlugin]".__LINE__);
            $param['result'] = array("缺失粉丝信息-二维码推广插件[调用失败]","text");
            return ;
        }

        if(empty($data['wxaccount']) ){
            LogRecord("wxaccount参数为empty", "[PromotioncodePlugin]".__LINE__);
            $param['result'] = array("缺失公众号信息-二维码推广插件[调用失败]","text");
            return ;
        }

        $result = $this->process($data['wxaccount']['appid'], $data['wxaccount']['appsecret'],$data['fans']);

        addWeixinLog($result,"二维码插件处理结果");
        if($result['status']){
            $param['result'] = array($result['info'],"image");
        }else{
            $param['result'] = array($result['info'],"text");
        }
    }



    /**
     * @param $appid
     * @param $appsecret
     * @param $fans
     * @return 返回 Wechat可处理的数组
     * @internal param 通常包含是微信服务器返回来的信息 $data
     */
    function process($appid,$appsecret,$fans){

        //检测是否有权限生成二维码
        if(!$this->hasAuthorized($fans)){
            return array('status'=>false,'info'=>$this->config['noAuthorizedMsg']);
        }

        addWeixinLog("有权限生成二维码","WeixinInnerProcess");
        $this -> wxapi = new WeixinApi($appid, $appsecret);

        $relativefile = $this->getQrcode($fans['id']);

        if(!file_exists(realpath($relativefile))){
            return array('status'=>false,'info'=>'微信永久二维码生成失败，请重试！');
        }
        //
        $realfile = $this->getPublicityPicture($fans,$relativefile);

        $media_id = S("PromotioncodePlugin_".$fans['id']);
        if(empty($media_id)){
            $media_id = $this -> wxapi->uploadMaterial($realfile);
            if($media_id['status']){
                $media_id = $media_id['msg']->media_id;
                S("PromotioncodePlugin_".$fans['id'],$media_id,3600);
                return array('status'=>true,'info'=>$media_id);
            }else{
                return array('status'=>false,'info'=>'微信接口上传二维码失败，请重试！');
            }
        }
        return array('status'=>true,'info'=>$media_id);
    }

    /**
     * 生成更完善效果的带推广二维码的宣传图片
     * TODO:生成宣传图片
     */
    private function getPublicityPicture($fans,$relativefile){
        $nickname = $fans['nickname'];//昵称
        $avatar = $fans['avatar'];
        $brandName = C('BRAND_NAME');//品牌名称
        //作为背景图片

        $bgpath = realpath($this->config['bgImg']);
        $tmppath = realpath($this->config['tmpFolder']) . '/';

        $savefilename = $this->config['mergeFolder'] .'/qrcode_uid'.$fans['id'] . ".jpg";
        //TODO: 判断是否已生成过，是则返回
        //需要合成的图片
        $arr = array(
            array("resource" => $avatar,
                "isremote" => true, "x" => 180, "y" => 70, "w" => 70, "h" => 70, 'type' => 'image'),
            array("resource" => $relativefile,
                "isremote" => false, "x" => 205, "y" => 545, "w" => 240, "h" => 240, 'type' => 'image'),
            array("resource" => $avatar,
                "isremote" => true, "x" => 300, "y" => 640, "w" => 45, "h" => 45, 'type' => 'image'),
            array("resource" => $nickname,
                "x" => 322,
                "y" => 94,
                'type' => 'text',
                'font'=>realpath('./Public/cdn/fonts/daheiti.ttf'),
                'size'=>15,
                'angle'=>0,
                'color'=>array(255,255,155),
            ),
            array("resource" => $brandName,
                "x" => 325,
                "y" => 125,
                'type' => 'text',
                'font'=>realpath('./Public/cdn/fonts/daheiti.ttf'),
                'size'=>14,
                'angle'=>0,
                'color'=>array(255,255,155),
            ),
        );

        $this -> mergeImage($bgpath, $arr, $tmppath, $savefilename);

        return realpath($savefilename);
    }

    /**
     * 判断当前用户是否有权利生成推广二维码
     */
    private function hasAuthorized($fans){
        if(empty($fans) || !isset($fans['groupid'])){return false;}
        $groupid = $fans['groupid'];
        if($groupid == 1){
            return false;
        }
        $result = apiCall(GroupAccessApi::GET_INFO, array( array('wxuser_group_id'=>$groupid)));
        if($result['status'] && is_array($result['info'])){
            if($result['info']['alloweddistribution'] == 1){
                return true;
            }
        }
        return false;
    }



    private function getQrcode($id){
        //上传获取一张永久二维码
        $filename = "/qrcode_uid$id.jpg";

        if(file_exists(realpath($this->config['downloadFolder']).$filename)){
            return ($this->config['downloadFolder']).$filename;
        }

        $json = $this -> wxapi->getQrcode($this->config['codeprefix'].strval($id));

        if($json['status']){
            //
            $ticket = $json['msg'];
            if(is_object($json['msg'])){
                $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket->ticket);
                $this->download_remote_file($url, realpath($this->config['downloadFolder']).$filename);
                return $this->config['downloadFolder'].$filename;
            }
        }else{
            addWeixinLog($id,"【获取二维码失败】");
        }
    }



    function download_remote_file($file_url,$save_to){
        $content = file_get_contents($file_url);
        file_put_contents($save_to, $content);
    }


    function http_get_data($url, $filename) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();

        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $fp = @fopen($filename, "a");
        //将文件绑定到流
        fwrite($fp, $return_content);
        //写入文件

        return $filename;
    }
    /**
     * $bgpath,$arr,$tmppath
     * 背景图，源合成图片，远程文件下载存放临时文件夹
     */
    private function mergeImage($bgpath, $arr, $tmppath, $savefilename) {

        $bg = imagecreatefromjpeg($bgpath);
        $bgwidth = imagesx($bg);
        $bgheight = imagesy($bg);
        $bgCopy = imagecreatetruecolor($bgwidth, $bgheight);
        if (function_exists("imagecopyresampled")) {
            imagecopyresampled($bgCopy, $bg, 0, 0, 0, 0, $bgwidth, $bgheight, $bgwidth, $bgheight);
        } else {
            imagecopyresized($bgCopy, $bg, 0, 0, 0, 0, $bgwidth, $bgheight, $bgwidth, $bgheight);
        }

        foreach ($arr as $vo) {
            if ($vo['type'] == 'image') {
                if ($vo['isremote']) {
                    $imgpath = $tmppath . md5($vo['resource'])  . ".jpg";
                    if (!file_exists(realpath($imgpath))) {
                        $this -> http_get_data($vo['resource'], $imgpath);
                    }
                } else {
                    $imgpath = $vo['resource'];
                }
                $child = imagecreatefromjpeg(realpath($imgpath));
                $pic_width = imagesx($child);
                $pic_height = imagesy($child);

                if (function_exists("imagecopyresampled")) {
                    $new = imagecreatetruecolor($vo['w'], $vo['h']);
                    imagecopyresampled($new, $child, 0, 0, 0, 0, $vo['w'], $vo['h'], $pic_width, $pic_height);
                } else {
                    $new = imagecreate($vo['w'], $vo['h']);
                    imagecopyresized($new, $child, 0, 0, 0, 0, $vo['w'], $vo['h'], $pic_width, $pic_height);
                }

                //合成图片
                imagecopymerge($bgCopy, $new, $vo['x'], $vo['y'], 0, 0, $vo['w'], $vo['h'], 100);

                imagedestroy($new);
                imagedestroy($child);
            }elseif($vo['type'] == 'text'){

                if(isset($vo['color'])){
                    $color = ImageColorAllocate($bgCopy,$vo['color'][0],$vo['color'][1],$vo['color'][2]);
                }else{
                    $color = ImageColorAllocate($bgCopy,0,0,0);
                }

                $str = mb_convert_encoding($vo['resource'], 'utf-8', 'auto');
                imagettftext($bgCopy,$vo['size'],0,$vo['x'],$vo['y'],$color,$vo['font'],$str);
            }
        }

        imagejpeg($bgCopy, ($savefilename));

        imagedestroy($bgCopy);

        return $savefilename;
    }


}