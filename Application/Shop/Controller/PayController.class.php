<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;
use Common\Api\Wxpay\WxPayApi;
use Distributor\Api\CommissionCountApi;
use Shop\Api\OrdersInfoViewApi;
use Shop\Api\OrdersItemApi;
use Shop\Api\OrderStatusApi;
use Think\Controller;
use Common\Api;

/**
 * 支付控制器
 * Class PayController
 * @package Shop\Controller
 */
class PayController extends ShopController {

    protected function _initialize(){
        parent::_initialize();

    }

    /**
     * 更改订单为货到付款
     */
    public function cashOndelivery() {

        $ids = I('post.id', 0);
        $ids = rtrim($ids, "-");
        $ids = explode("-", $ids);
        $result = apiCall(OrderStatusApi::CASH_ON_DELIVERY, array($ids,false,$this->userinfo['id']));

        if (!$result['status']) {
        	dump($result['info']);
            $this -> error($result['info']);
        }

        $commission = new CommissionCountApi();

        $commission->add($ids);

        //TODO: 转移到插件中
       // tag("send_to_msg_user",array($id, $text));
//        $wxuserid = $this->userinfo['id'];
//
//        $text = "用户ID:$wxuserid,时间:" . date("Y-m-d H:i:s",time()) . ",订单ID:" . rtrim(I('post.id', 0),"-") . ",选择了货到付款,请登录后台查看订单。";
//        $id = C('STORE_ID');
//
//        $this->sendToWxaccount($id, $text);

        $this -> success("操作成功！");
    }

    /**
     * @param $token
     * @param $text
     */
//    private function sendToWxaccount($token, $text) {
//        $result = apiCall(WxaccountApi::GET_INFO, array(array('token' => $token)));
//        if ($result['status']) {
//            $api = new Api\WeixinApi($result['info']['appid'], $result['info']['appsecret']);
//            $map = array('name' => "WXPAY_OPENID");
//
//            $result = apiCall(ConfigApi::GET_INFO , array($map));
//
//            addWeixinLog($result, "接收订单支付成功的OPENID");
//            if ($result['status']) {
//                //TODO: 等待测试，是否正确
//
//                $openid_array = explode(",", $result['info']['value']);
//
//                if (!empty($openid_array)) {
//                    foreach ($openid_array as $openid) {
//                        $api -> sendTextToFans($openid, $text);
//                    }
//                }
//            }
//        } else {
//            LogRecord($result['info'], __FILE__ . __LINE__ . "货到付款成功消息失败");
//        }
//    }
    /**
     * 微信支付页面
     */
    public function pay() {
        //订单ID
        
        
        $ids = I('get.id', 0);
		//
        $ids = rtrim($ids, "-");
        $ids_arr = explode("-", $ids);
        if (count($ids_arr) == 0) {
            $this -> error("参数错误！");
        }
		
        $map = array();
        $map['id'] = array('in', $ids_arr);
        $result = apiCall(OrdersInfoViewApi::QUERY_NO_PAGING, array($map));
        //TODO: 判断订单状态是否为待支付
       // dump($result['status']);
        if ($result['status']) {

            $order_list = $result['info'];

            $payConfig = C('WXPAY_CONFIG');
            $payConfig['jsapicallurl'] = getCurrentURL();
			//dump($payConfig);
            addWeixinLog($payConfig,"配置信息");
			
            $items = array();
            $total_fee = 0;
            $total_express = 0.0;
            $body = "";
            $attach = "";

            foreach ($order_list as $order) {
                $trade_no = $order['orderid'];
                $total_fee +=($order['price']);

                $products = $this -> getProducts($order[id]);
                foreach ($products as $vo) {
                    $total_express += $vo['post_price'];
                    if(empty($body)){
                        $body = $vo['name'];
                    }
                }
                $attach .= $order['id'].'_';
                array_push($items, $item);
            }
			
            $total_fee = $total_fee + $total_express;
            if ($total_fee <= 0) {
                $this -> error("支付金额不能小于0！");
            }

			$total_fee = 1;
			
            //测试时
           $this -> setWxpayConfig($payConfig, $trade_no, $body, $total_fee,$attach);
			
			//dump($payConfig);
            $this -> assign("total_express", $total_express);
            $this -> assign("ids", I('get.id', 0));
            $this -> assign("total_fee", ($total_fee + $total_express));
            $this -> theme($this->themeType) -> display();

        } else {
            $this -> error("支付失败！");
        }

    }

    //====================PRIVATE===

    /**
     * 获取订单的商品信息
     * @param $orders_id 订单ID
     */
    private function getProducts($orders_id) {
        $map = array('orders_id' => $orders_id);
        $result = apiCall(OrdersItemApi::QUERY_NO_PAGING , array($map));
        if (!$result['status']) {
            LogRecord($result['info'], __FILE__ . __LINE__);
            $this -> error($result['info']);
        }

        return $result['info'];
    }

    /**
     *
     * @param 微信支付配置 $config
     * @param 订单ID $trade_no
     * @param $body
     * @param 总价格 $total_fee
     * @param string $attach
     * @throws Api\Wxpay\WxPayException
     * @internal param 微信支付配置 $config
     * @internal param 订单ID $trade_no
     * @internal param 商品描述 $itemdesc
     * @internal param 总价格 $total_fee
     */
    private function setWxpayConfig($config, $trade_no, $body, $total_fee, $attach='') {
        try {

            $jsApiParameters = "";
            //①、获取用户openid
            $tools = new Api\Wxpay\JsApi($config);

//			$openId = $tools -> GetOpenid();
            $openId = $this->openid;
            //②、统一下单
            $input = new Api\Wxpay\WxPayUnifiedOrder();
            $input -> setConfig($config);
            $input -> SetBody($body);//string(32)
            $input -> SetAttach($attach);//
            $input -> SetOut_trade_no($trade_no);
            $input -> SetTotal_fee($total_fee);
            $input -> SetTime_start(date("YmdHis"));
            $input -> SetTime_expire(date("YmdHis", time() + 60*30));
//			$input -> SetGoods_tag("test");
            $input -> SetNotify_url($config['NOTIFYURL']);
            $input -> SetTrade_type("JSAPI");
            $input -> SetOpenid($openId);
            WxPayApi::setConfig($config);
            $order = WxPayApi::unifiedOrder($input);

            if(isset($order['return_code']) && $order['result_code'] == 'FAIL'){
                $this->error($order['err_code_des']);
            }
//            dump($order);
            addWeixinLog($order,"GETJsApiParameters");
            $jsApiParameters = $tools -> GetJsApiParameters($order);
            $this -> assign("jsApiParameters", $jsApiParameters);

        } catch(WxPayException $sdkexcep) {
            $error = $sdkexcep -> errorMessage();
            $this -> assign("error", $error);
        }

    }

}
