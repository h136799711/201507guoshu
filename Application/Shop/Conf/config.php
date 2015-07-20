<?php
return array(
	//'配置项'=>'配置值',
	'TMPL_PARSE_STRING'  =>array(
     	'__CDN__' => __ROOT__.'/Public/cdn', // 更改默认的/Public 替换规则
		'__JS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/js', // 增加新的JS类库路径替换规则
     	'__CSS__'     => __ROOT__.'/Public/'.MODULE_NAME.'/css', // 增加新的JS类库路径替换规则
     	'__IMG__'     => __ROOT__.'/Public/'.MODULE_NAME.'/imgs', // 增加新的JS类库路径替换规则	
     
	),	
	'DEFAULT_THEME'=>'default',
	'SESSION_PREFIX'=>'Shop_',
    
     //TODO: 一套系统对应一个公众号的商城
    'STORE_ID'=>5,
    
	'SHOW_PAGE_TRACE'=>false,

    'WXPAY_CONFIG'=>array(

		'APPID'=>'wx3fe04f32017f50a5',
		'APPSECRET'=>'f7dbb6d7882ecaa984a9f3e900db9a3d',
		'MCHID'=>'1237211302',
		'KEY'=>'c01843987e76de691cc94dbb402fe2aa',//在微信发送的邮件中查看,patenerkey
//
		'NOTIFYURL'=>'http://renren.itboye.com/index.php/Shop/WxpayNotify/index',
		'JSAPICALLURL'=>'http://renren.itboye.com/index.php/Shop/Orders/pay?showwxpaytitle=1',
		'SSLCERTPATH'=>'/alidata/8rawcert/10027619/apiclient_cert.pem',
		'SSLKEYPATH'=>'/alidata/8rawcert/10027619/apiclient_cert.pem',
		'CURL_PROXY_HOST' => "0.0.0.0",
		'CURL_PROXY_PORT' => '0',
		'REPORT_LEVENL' => 1
    ),
);