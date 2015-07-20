<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Tool\Controller;
use Think\Controller;
use Shop\Api\OrderStatusApi;
use Admin\Api\ConfigApi;
/**
 * 任务运行
 */
class TaskController extends Controller{
	
	protected function _initialize(){
//		$key = I('get.key','');
		//20分钟以内的请求只处理一次
		$prev_pro_time = S('TASK_PROCESS_TIME');
		if($prev_pro_time === false){
			S('TASK_PROCESS_TIME',time(),20*60);
			//S('TASK_PROCESS_TIME',time(),60);
		}else{
			echo "Cached-Time: ". date("Y-m-d H:i:s",$prev_pro_time);
			//缓冲处理
			exit();
		}
		
		$this->getConfig();
	}
	
	/**
	 * 任务自动处理\异步
	 */
	public function index(){
		
		$url = C('SITE_URL').'/index.php/Tool/Task/aysnc';
//		echo $url;
		$result = fsockopenRequest($url,array('user'=>'www.itboye.com'),"POST");
		echo "Accept Request!";
		echo $result;
	}
	
	/**
	 * 任务处理区域
	 */
	public function aysnc(){
		$user = I('post.user','');
		if($user != "www.itboye.com"){
			addWeixinLog(get_client_ip(0,true),"非法用户访问");
			return ;
		}
		
		addWeixinLog(get_client_ip(0,true),"合法用户");
		ignore_user_abort(true); // 后台运行
		set_time_limit(0); // 取消脚本运行时间的超时上限
//	
			
		$this->toRecieved();
		
		$this->toAutoEvaluation();
		
		$this->toCompleted();
		
		$this->toCancel();
		
	}
	
	
	/**
	 * 
	 * 1. 订单[取消]－》检测 time() - updatetime > 指定时间，暂定1小时 满足条件变更为订单[取消]
	 */
	private function toCancel(){
		
		$interval =C('INTERVAL_CANCEL');//1小时
		
		$result = apiCall(OrderStatusApi::ORDER_STATUS_TO_CANCEL,array($interval));
		if(!$result['status']){
			LogRecord($result['info'], __FILE__.__LINE__);
		}else{
			if($result['info'] > 0){
				addWeixinLog("更新订单为取消影响记录数：".$result['info'],'0');
			}
		}
	}
	
	
	/**
	 * 
	 * 1. 订单[已发货]－》检测 time() - updatetime > 指定时间，暂定30天 满足条件变更为订单[已发货]
	 */
	private function toRecieved(){
		$interval =C('INTERVAL_RECIEVED');//30天
		//$interval = 60;//30天
		
		$result = apiCall(OrderStatusApi::ORDER_STATUS_TO_RECIEVED,array($interval));
		if(!$result['status']){
			LogRecord($result['info'], __FILE__.__LINE__);
		}else{
			if($result['info'] > 0){
				addWeixinLog("更新订单为已收货影响记录数：".$result['info'],'0');
			}
		}
	}
	
	/**
	 * 
	 * 1. 订单[已收货]－》检测 time() - updatetime > 指定时间，暂定15天 满足条件变更为订单[已收货]
	 */
	private function toCompleted(){
		$interval =C('INTERVAL_COMPLETED');//15天
		//$interval = 60;//1分钟前
		$result = apiCall(OrderStatusApi::ORDER_STATUS_TO_COMPLETED,array($interval));
		if(!$result['status']){
			LogRecord($result['info'], __FILE__.__LINE__);
		}else{
			if($result['info'] > 0){
				addWeixinLog("更新订单为已完成影响记录数：".$result['info'],'3');
			}
		}
	}
	
	
	/**
	 * 
	 * 自动评价
	 */
	private function toAutoEvaluation(){
		
		$interval =C('INTERVAL_AUTO_EVALUATION');//15天
		
		$result = apiCall(OrderStatusApi::ORDER_STATUS_TO_AUTO_EVALUATION,array($interval));
		if(!$result['status']){
			LogRecord($result['info'], __FILE__.__LINE__);
		}else{
			if($result['info'] > 0){
				addWeixinLog("更新订单为自动评价影响记录数：".$result['info'],'0');
			}
		}
	}
	
	
	
	
	
	/**
	 * 从数据库中取得配置信息
	 */
	protected function getConfig() {
		$config = S('global_tool_config');

		if ($config === false) {
			$map = array();
			$fields = 'type,name,value';
			$result = apiCall(ConfigApi::QUERY_NO_PAGING, array($map, false, $fields));
			if ($result['status']) {
				$config = array();
				if (is_array($result['info'])) {
					foreach ($result['info'] as $value) {
						$config[$value['name']] = $this -> parse($value['type'], $value['value']);
					}
				}
				//缓存配置300秒
				S("global_tool_config", $config, 2400*1);
			} else {
				LogRecord('INFO:' . $result['info'], '[FILE] ' . __FILE__ . ' [LINE] ' . __LINE__);
				$this -> error($result['info']);
			}
		}
		C($config);
	}

    /**
     * 根据配置类型解析配置
     * @param  integer $type 配置类型
     * @param  string $value 配置值
     * @return array|string
     */
	private static function parse($type, $value) {
		switch ($type) {
			case 3 :
				//解析数组
				$array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
				if (strpos($value, ':')) {
					$value = array();
					foreach ($array as $val) {
						list($k, $v) = explode(':', $val);
						$value[$k] = $v;
					}
				} else {
					$value = $array;
				}
				break;
		}
		return $value;
	}
	
	

	
	
}
