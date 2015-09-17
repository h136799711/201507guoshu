<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Controller;

use Addons\WeixinMsgSend\WeixinMsgSendAddon;
use Admin\Api\DatatreeApi;
use Shop\Api\BannersApi;
use Shop\Api\ProductApi;
use Shop\Model\ProductModel;
use Weixin\Api\WxuserApi;
use Ucenter\Api\MemberApi;
use Ucenter\Api\UcenterMemberApi;
use Distributor\Api\DistributorInfoApi;
use Shop\Api\ProductGroupApi;


class IndexController extends ShopController{

    public function distributor(){
		
    	if(IS_GET){
    		//phpinfo();
    		$referrer=I('referrer');
			$map=array(
				'id'=>$referrer
			);
			$result=apiCall(WxuserApi::QUERY_NO_PAGING ,array($map));
			$this->assign('referrer',$result[info][0]);
       	 	$this->theme($this->themeType)->display();
		}else if(IS_AJAX){
			
			$map=array(
				'id'=>18,
			);
			$userInfo= apiCall(WxuserApi::QUERY_NO_PAGING,array($map));
			$userInfo['info']['groupid']=11;
			//dump($userInfo['info']);
			$result=apiCall(WxuserApi::SAVE_BY_ID,array(18,$userInfo['info']));
			
			$entity=array(
				'name'=>I("name"),
				'phone'=>I("phone"),
				'address'=>I("address"),
				'create_time'=>time(),
				'uid'=>$userInfo['info'][0]['uid'],
				'wxaccount_id'=>1
			);
			$result=apiCall(DistributorInfoApi::ADD,array($entity));
			$this->success('操作成功',U('Shop/Index/index'));
		}
    }


	protected function _initialize(){
		parent::_initialize();
		$showStartPage = true;
		$last_entry_time = cookie("last_entry_time");
		if(empty($last_entry_time)){
			//一小时过期
			cookie("last_entry_time",time(),3600);
			$last_entry_time = time();			
		}elseif(time() - $last_entry_time < 20*60){
			$showStartPage = false;
		}else{
			//一小时过期
			cookie("last_entry_time",time(),3600);
		}
		
		$this->assign("showstartpage",$showStartPage);
		
	}
	
	/**
	 * 首页
	 */
	public function index(){

		$map= array(
			'uid'=>$this->wxaccount['uid'],
			'storeid'=>-1,
			'position'=>getDatatree("SHOP_INDEX_BANNERS")
		);
		
		$page = array(
			'curpage'=>0,
			'size'=>8
		);
		$order = "createtime desc";
		$params = false;
		
		$result = apiCall(BannersApi::QUERY,array($map,$page,$order,$params));
//		dump($result);
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		$this->assign("banners",$result['info']['list']);
		
		$map= array('parentid'=>C("DATATREE.STORE_TYPE"));
		$result = apiCall(DatatreeApi::QUERY,array($map,$page,$order,$params));
		
		if(!$result['status']){
			$this->error($result['info']);
		}

		$this->assign("store_types",$result['info']['list']);
		
		// 获取推荐商品
		$result = $this->getProducts();
		if($result['status'] && is_array($result['info'])){
			$this->assign("recommend_products",$result['info']['list']);
		}
		//dump($result);
		$ads  = $this->getAds();
		
		$this->assign("ads",$ads['info']['list']);
		
		//获取推荐店铺
		$result = $this->getRecommendStore();
		
		$this->assign("rec_stores",$result['info']['list']);
		
		//获取首页4格活动
		$result = $this->getFourGrid();
        $this->assign("meta_title",$this->getStoreName());
	  	
	  	//获取商品类目
		$this->assign("fourgrid",$result['info']['list']);
        $this->assign("isDistributor",$this->isDistributor());
		$page = array(
			'curpage'=>0,
			'size'=>4
		);
		//$order = "createtime asc";
	  	$map= array(
	  		'parentid'=>C("DATATREE.WXPRODUCTGROUP"),
	  		'hidden_value'=>'fruitType',
		);
	  	$result=apiCall(DatatreeApi::QUERY,array($map,$page,$order));
		//dump($result);
		$this->assign("fruitsType",$result['info']['list']);
		
		
		$map=array(
			'parentid'=>40,
		);
		$result=apiCall(DatatreeApi::QUERY_NO_PAGING,array($map));
		$this->assign("flashSale",$result['info']);
		$order=" end_time asc";

		$map=array(
			'g_id'=>40,
			'start_time'=>array(
				'LT',strtotime(date("Y-M-d",time()))
			),
			'end_time'=>array(
				'GT',(strtotime(date("Y-M-d",time()))+24*3600)
			),
		);
		$result=apiCall(ProductGroupApi::QUERY_NO_PAGING,array($map,$order));
		if($result['info']==null){
			$this->assign("flashSaleTime",'-1');
		}else{
			$this->assign("flashSaleTime",$result['info'][0]['end_time']);
		}
		$map=array(
			'g_id'=>40,
//			'start_time'=>array(
//				'BETWEEN',array(strtotime(date("y-m-d",time()))+3600*24,strtotime(date("y-m-d",time()))+3600*24*7),
//			),

            'start_time'=>array(
                'LT',time()
            ),
            'end_time'=>array(
                'GT',time()
            ),
		);
		$result=apiCall(ProductGroupApi::QUERY_NO_PAGING,array($map,$order));
		if($result['info']==null){
			$this->assign("flashSaleWeekTime",'-1');
		}else{
			$this->assign("flashSaleWeekTime",$result['info'][0]['start_time']);
		}
		
		$this->theme($this->themeType)->display();
	}



	
	

    private function getStoreName(){
        //默认当前公众号名
        $name = $this->wxaccount['wxname'];

        // 如果是分销商，显示分销商名称
//        if($this->isDistributor()){
//            $name = $this->userinfo['nickname'].'的店铺';
//        }

        return $name;
    }

    /**
     * 判断是否为分销商
     * @return bool
     */
    private function isDistributor(){
        //TODO: 当前仅判断大于1 时都为分销商
        if($this->userinfo['groupid'] > 1){
            return true;
        }

        return false;
    }


	/**
	 * 获取首页4格活动
	 * 
	 */
	 private function getFourGrid(){
		$page = array('curpage'=>0,'size'=>4);
	 	$map = array('position'=>getDatatree("INDEX_4_ACTIVTIY"));
		$order = " id desc";
		$result = apiCall(BannersApi::QUERY, array($map,$page,$order));

		if(!$result['status']){
			$this->error($result['info']);
		}

		return $result;
	 }
	
	/**
	 * 广告
	 */
	private function getAds(){
		
		$page = array('curpage'=>0,'size'=>2);
		$map = array('position'=>getDatatree("SHOP_INDEX_ADVERT"));
		$result = apiCall(BannersApi::QUERY, array($map,$page));
		if(!$result['status']){
			$this->error($result['info']);
		}
		return $result;
	}
	/**
	 * 推荐店铺
	 */
	private function getRecommendStore(){
		
		$page = array('curpage'=>0,'size'=>4);
		$map = array('position'=>getDatatree("SHOP_INDEX_RECOMMEND_STORE"));
		$result = apiCall(DatatreeApi::QUERY, array($map));
		if(!$result['status']){
			$this->error($result['info']);
		}
		
		return $result;
	}
	
	/** 
	 *  
	 */ 
	public function getProducts(){
		
		$page = array('curpage'=>0,'size'=>10);
		$order = "updatetime desc";
		$map = array('onshelf'=>ProductModel::STATUS_ONSHELF);
		$group_id = getDatatree("WXPRODUCTGROUP_RECOMMEND");
		
		$result = apiCall(ProductApi::QUERY_BY_GROUP, array($group_id,$map));
		if(!$result['status']){
			LogRecord($result['info'], __FILE__.__LINE__);	
		}
		
		return $result;
	}
}

