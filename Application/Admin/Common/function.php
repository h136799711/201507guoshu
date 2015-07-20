<?php

/**
 * 系统非常规MD5加密方法
 * @param  string $str 要加密的字符串
 * @return string
 */
function think_ucenter_md5($str, $key = 'ThinkUCenter') {
	return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 获取链接
 * 传入U方法可接受的参数或以http开头的完整链接地址
 * @return 链接地址
 */
function getURL($str, $param = '') {
	if (trim($str) == '#') {
		return '#';
	}
	if (strpos($str, '?') === false) {
		$str = $str . '?' . $param;
	} else {
		$str = $str . '&' . $param;
	}
	if (strpos($str, "http") === 0) {
		return $str;
	}
	
	return U($str);
}

/**
 * 判断链接是否激活
 * 根据session来检测
 * @return ''|'active'
 */
function isActiveMenuURL($id) {
	$activemenuid = session('activemenuid');
	if ($activemenuid === $id) {
		return 'active';
	}
	return '';
}

/**
 * 判断链接是否激活
 * 根据session来检测
 * @param $id
 * @return string ''|'active'
 */
function isActiveSubMenuURL($id) {
	$activesubmenuid = session('activesubmenuid');
	if ($activesubmenuid === $id) {
		return 'active';
	}
	return '';
}


// 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
	$array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
	if (strpos($string, ':')) {
		$value = array();
		foreach ($array as $val) {
			list($k, $v) = explode(':', $val);
			$value[$k] = $v;
		}
	} else {
		$value = $array;
	}
	return $value;
}
/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}
/* 判断当前时间是否为晚上  */
function isNight(){
	$hour=date('G',time());
	if($hour > 18 || $hour < 6){
		return true;
	}else{
		return false;
	}
}


function getLogInfo($info){
	$loginfo = unserialize($info);
	$str = '';
	if(isset($loginfo['ToUserName'])){
		$str = '[ToUserName]: '.$loginfo['ToUserName'].';';
	}
	
	if(isset($loginfo['FromUserName'])){
		$str .= '[FromUserName]: '.$loginfo['FromUserName'].';';
	}
	if(empty($str)){
		return $info;
	}
	
	if(isset($loginfo['CreateTime'])){		
		$str .= '[CreateTime]: '.date('Y/m/d H:i:s',$loginfo['CreateTime']).';';
	}
	if(isset($loginfo['MsgType'])){		
		$str .= '[MsgType]: '.$loginfo['MsgType'].';';
	}
	if(isset($loginfo['Content'])){		
		$str .= '[Content]: '.$loginfo['Content'].';';
	}
	if(isset($loginfo['MediaId'])){
		$str .= '[MediaId]: '.$loginfo['MediaId'].';';
	}
	if(isset($loginfo['EventKey'])){
		$str .= '[EventKey]: '.$loginfo['EventKey'].';';
	}
	
	return $str;
}
/**
 * 获取当前公众号信息
 */
function getWxaccount(){
	return session("user_"+UID+"_wxaccount");
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

// 获取数据的状态操作
function show_status_op($status) {
    switch ($status){
        case 0  : return    '启用';     break;
        case 1  : return    '禁用';     break;
        case 2  : return    '审核';       break;
        default : return    false;      break;
    }
}

/**
 *
 * @param $list         数据集合
 * @param $field        集合中一个对象的字段
 * @param $status_text  数组字段对应的文字描述
 * @example
 *  $list = array(array('status'=>-2),array('status'=>2),array('status'=>1),array('status'=>-1));
 *  $field = "status";
 *  $status_text = array(-2=>'被删除',2=>'已禁用');
 *  $result = int_to_string($list,$field,$status_text);
 *  //转换后
 *  $result = array(array('status'=>-2,'_status_text'=>'被删除'),array('status'=>2,'_status_text'=>'已禁用'),array('status'=>1),array('status'=>-1));
 * @return 数据集合
 */
function int_to_string($list,$field,$status_text){
    foreach($list as &$vo){
        if(isset($vo[$field])){
            foreach($status_text as $key=>$text){
                if($key == $vo[$field]){
                    $vo['_'.$field.'_text'] = $text;
                }
            }

            //无法识别，设置为Unknown
            if(!isset($vo['_'.$field.'_text'])){
                $vo['_'.$field.'_text'] = "Unknown";
            }
        }
    }
    return $list;
}