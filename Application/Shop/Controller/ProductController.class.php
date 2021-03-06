<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------

namespace Shop\Controller;

use Shop\Api\CategoryPropApi;
use Shop\Api\CategoryPropvalueApi;
use Shop\Api\OrdersApi;
use Shop\Api\OrdersItemApi;
use Shop\Api\ProductApi;
use Shop\Api\ProductGroupApi;
use Shop\Api\ProductSkuApi;
use Shop\Api\SkuApi;
use Shop\Api\SkuvalueApi;
use Shop\Api\StoreApi;
use Shop\Model\ProductModel;
use Admin\Api\DatatreeApi;

class ProductController extends ShopController {

    /**
     * 插入排序
     * @param $arr
     * @return mixed
     */
    function insert_sort($arr) {
        for($i=1, $len=count($arr); $i<$len; $i++) {
            //获得当前需要比较的元素值。
            $tmp = $arr[$i];
            //内层循环控制 比较 并 插入
            for($j=$i-1;$j>=0;$j--) {
                //$arr[$i];//需要插入的元素; $arr[$j];//需要比较的元素
                if($tmp['_sales'] > $arr[$j]['_sales']) {
                    //发现插入的元素要小，交换位置
                    //将后边的元素与前面的元素互换
                    $arr[$j+1] = $arr[$j];
                    //将前面的数设置为 当前需要交换的数
                    $arr[$j] = $tmp;
                } else {
                    break;
                }
            }
        }
        //返回
        return $arr;
    }

	public function quickSort($left, $right, $arr) {
		$l = $left;
		$r = $right;
		$pivot = $arr[intval(floor(($left + $right) / 2))];
		$temp = array();

		while ($l < $r) {

			while (($arr[$l]['_sales']) > ($pivot['_sales'])) {
				$l++;
			}
			while (($arr[$r]['_sales']) < ($pivot['_sales'])) {
				$r--;
			}

			if ($l >= $r)
				break;
			$temp = $arr[$l];
			$arr[$l] = $arr[$r];
			$arr[$r] = $temp;
			
			if ($arr[$l]['_sales'] == $pivot['_sales'])
				--$r;
			if ($arr[$r]['_sales'] == $pivot['_sales'])
				++$l;
		}
		
		if ($l == $r) {
			$l++;
			$r--;
		}

		if ($left < $r) {
			return $this->quickSort($left, $r, $arr);
		} elseif ($right > $l) {
			return $this->quickSort($l, $right, $arr);
		} else {
			return $arr;
		}
	}

	/**
	 * 发现
	 */
	public function search() {
		//排序： s 综合 ，d 销量 ,p 价格 从小到大, pd 价格 从大到小
		$sort = I('sort', 's');
		$type = I('type', '1');
		$layout = I('get.layout', 'list');
		$gid=I('get.g_id','');
		//dump($gid);
		
		$map = array(
            'type'=>$type,
            'g_id'=>$gid
        );
		$q = I('q','');
		$page = array('curpage' => I('post.p', 0,'intval'), 'size' => 10);
		$order = " id desc ";
		if ($sort == 's') {
			$order = " price desc";
		}
		if ($sort == 'p') {
			$order = " price desc";
		}
		if ($sort == 'pd') {
			$order = " price asc";
		}
		$map1=array(
			'hidden_value'=>'fruitType',
		);
		
		$result1=apiCall(DatatreeApi::QUERY_NO_PAGING,array($map1));
		$this->assign("fruitTypes",$result1['info']);
		$this -> assign("g_id",$gid);
		$params = false;
		$result = apiCall(ProductApi::QUERY_WITH_STORE, array($q,$map, $page, $order, $params));
		if (!$result['status']) {
			$this -> error($result['info']);
		}
		$list = $result['info']['list'];
		if(!is_null($list)){
			$list = $this -> queryMonthlySales($list);
//			dump($list);
			if ($sort == 'd') {
				//对销量进行排序
				$list = ($this->quickSort(0, count($list)-1,  $list));
				
			}
		}
		
		if(IS_POST){
//			echo json_encode($list);
			$this->success($list);
		}else{
			$this -> assign("q", $q);
			$this -> assign("layout", $layout);
			$this -> assign("sort", $sort);
			$this -> assign("curpage", $page['curpage']);
			$this -> assign("show", $result['info']['show']);
			$this -> assign("list", $list);
			$this ->theme($this->themeType)->display();
		}
	}

	/**
	 * 商品分组页面
	 */
	public function group() {
		if(IS_GET){
			$groupid = I('get.id',0);
			$this->assign("groupid",$groupid);
			$this ->theme($this->themeType)->display();
			
		}elseif(IS_AJAX){
			$p = I('post.p',0);
			
			$page = array('curpage'=>$p,'size'=>10);
			$order = " updatetime desc";
			$map = array('onshelf'=>ProductModel::STATUS_ONSHELF);
			$group_id = I('post.groupid',0);
			
			$result = apiCall(ProductApi::QUERY_BY_GROUP, array($group_id,$map,$page));
			if(!$result['status']){
				LogRecord($result['info'], __FILE__.__LINE__);
				$this->error($result['info']);	
			}
			
			$products = $result['info']['list'];
			
			foreach($products as &$vo){
				
				$vo['_zk_percent'] =  number_format(($vo['price']/$vo['ori_price'])*10.0,2);
				$vo['price'] =  number_format($vo['price']/100.0,2);
				$vo['ori_price'] =  number_format($vo['ori_price']/100.0,2);
				
			}
			
			$this->success($products);
			
		}
	}

	/**
	 * 商品详情查看
	 */
	public function detail() {
		if (IS_GET) {
			$id = I('get.id', 0);
			$result = apiCall(ProductApi::GET_INFO, array( array('id' => $id)));

			if (!$result['status']) {
				$this -> error($result['info']);
			}
			$banners = $this -> getBanners($result['info']);
			if ($result['info']['attrext_ispostfree'] == '0') {
				$uni_express = json_decode($result['info']['express']);
				//				{"id":10000027,"price":1000},{"id":10000028,"price":1000},{"id":10000029,"price":1000}]

				$express_str = "";
				foreach ($uni_express as $vo) {
					if (($vo -> id == 10000027)) {
						$express_str .= "平邮: " . number_format($vo -> price / 100.0, 1);
					} elseif ($vo -> id == 10000028) {
						$express_str .= "快递: " . number_format($vo -> price / 100.0, 1);
					} elseif ($vo -> id == 10000029) {
						$express_str .= "EMS: " . number_format($vo -> price / 100.0, 1);
					}
				}
				//				if($uni_expr)

				$this -> assign("express_str", $express_str);
			}

			if ($result['info']['has_sku'] == '1') {
				$skulist = $this -> getSkuList($result['info']);
				//				dump($skulist);
				//				exit();
				$this -> assign("sku_arr", $skulist['sku_arr']);
				$this -> assign("sku_list", json_encode($skulist['sku_list']));
			} else {
				$this -> assign("sku_list", json_encode(array()));
			}
			$this -> assign("properties", $this -> getProperties($result['info']['properties']));
			$details = htmlspecialchars_decode($result['info']['detail']);
			$this -> assign("details", json_decode($details));
			$this -> assign("banners", $banners);
			$this -> assign("product", $result['info']);
			//dump($result['info']);
			$result = apiCall(StoreApi::GET_INFO, array( array('id' => $result['info']['storeid'])));
			
			if ($result['status']) {
				$this -> assign("wxstore", $result['info']);
			}
			
			$monthlySales = $this -> getMonthlySales($id);
			//获取月销量
			$this -> assign("monthlySales", $monthlySales);
			
			$this -> theme($this->themeType)->display();

		}

	}

	/**
	 * 获取多个商品的月销量
	 */
	private function queryMonthlySales($list) {
		$tmp_arr = array();
		foreach ($list as $vo) {
			array_push($tmp_arr, $vo['id']);
		}
		$result = apiCall(OrdersApi::MONTHLY_SALES, array($tmp_arr));
		
		if (!$result['status']) {
			$this -> error($result['info']);
		}

		$tmp_arr = null;
		$tmp_arr = array();
		$sales = $result['info'];
		foreach ($sales as $vo) {
			$tmp_arr[$vo['p_id']] = intval($vo['sales']);
		}
		
		foreach ($list as &$vo) {
			$id = intval($vo['id']);
			if (isset($tmp_arr[$id])) {
				$vo['_sales'] = $tmp_arr[$vo['id']];
			} else {
				$vo['_sales'] = 0;
			}
		}

		return $list;
	}

	/**
	 * 获取单个商品月销量
	 */
	private function getMonthlySales($p_id) {
//		dump($p_id);
		//统计订单数 ， 满足条件小于当前时间，大于当前时间－30天
		$result = apiCall(OrdersItemApi::MONTHLY_SALES, array($p_id));
		if (!$result['status']) {
			$this -> error($result['info']);
		}

		return $result['info'];
	}

	private function getSkuList($product) {
		$skuinfo = json_decode($product['sku_info']);
		$sku_ids = array('-1');
		$sku_value_ids = array('-1');
		foreach ($skuinfo as $vo) {
			array_push($sku_ids, $vo -> id);
			foreach ($vo->vid as $vid) {
				array_push($sku_value_ids, $vid);
			}
		}

		$map = array();
		$map['id'] = array('in', $sku_ids);

		$result = apiCall(SkuApi::QUERY_NO_PAGING, array($map));
		if (!$result['status']) {
			$this -> error($result['info']);
		}
		$sku_result = $result['info'];

		$map = array();
		$map['id'] = array('in', $sku_value_ids);

		$result = apiCall(SkuvalueApi::QUERY_NO_PAGING, array($map));
		if (!$result['status']) {
			$this -> error($result['info']);
		}
		$sku_value_result = $result['info'];
		//上述代码获取SKU以及SKU值的名称

		$sku_arr = array();
		foreach ($sku_result as $_sku) {
			$key = $_sku['id'] . ':';
			foreach ($sku_value_result as $_sku_value) {
				if ($_sku_value['sku_id'] == $_sku['id']) {

                    if (!isset($sku_arr[$_sku['id']])) {
						$sku_arr[$_sku['id']] = array('id' => $_sku['id'], 'sku_name' => $_sku['name'], 'sku_value_list' => array());
					}

					array_push($sku_arr[$_sku['id']]['sku_value_list'], array('id' => $_sku_value['id'], 'name' => $_sku_value['name']));

				}
			}
		}

		$result = apiCall(ProductSkuApi::QUERY_NO_PAGING, array( array('product_id' => $product['id'])));
		if (!$result['status']) {
			$this -> error($result['info']);
		}

		$formatSku = array();
		foreach ($result['info'] as &$vo) {
			$formatSku[$vo['sku_id']] = array('icon_url' => $vo['icon_url'], 'ori_price' => $vo['ori_price'], 'price' => $vo['price'], 'product_code' => $vo['product_code'], 'product_id' => $vo['product_id'], 'quantity' => $vo['quantity'], );
		}
		return array('sku_list' => $formatSku, 'sku_arr' => $sku_arr);
	}

	/**
	 * 获取属性对应的文字描述
	 */
	private function getProperties($prop) {
		if (empty($prop)) {
			return array();
		}
		$prop_arr = explode(";", $prop);
		$prop_ids = array();
		$propvalue_ids = array();
		$result = array();
		foreach ($prop_arr as $vo) {
			if ($vo) {
				$prop_value = explode(",", $vo);
				array_push($prop_ids, $prop_value[0]);
				array_push($propvalue_ids, $prop_value[1]);
			}
		}

		$map = array();
		$map['id'] = array("in", $prop_ids);
		$order =  " id asc ";
		$prop_result = apiCall(CategoryPropApi::QUERY_NO_PAGING, array($map,$order));
		if (!$prop_result) {
			$this -> error($prop_result['info']);
		}
		
		$prop_result = $prop_result['info'];
		
		$map = array();
		$map['id'] = array("in", $propvalue_ids);
		$order =  " prop_id asc ";
		$propvalue_result = apiCall(CategoryPropvalueApi::QUERY_NO_PAGING, array($map,$order));
		if (!$propvalue_result) {
			$this -> error($propvalue_result['info']);
		}
		
		$propvalue_result = $propvalue_result['info'];
		for ($i = 0; $i < count($prop_result); $i++) {
			$p = $prop_result[$i];
			$pv = $propvalue_result[$i];

			array_push($result, array('name' => $p['propname'], 'value' => $pv['valuename']));

		}
		return $result;
	}

	/**
	 * 从商品信息中提取图片
	 */
	private function getBanners($product) {

		$imgs = explode(",", $product['img']);
		array_pop($imgs);
		array_push($imgs, $product['main_img']);
		return $imgs;
	}
	
	
	
	
	/**
	 * 限时抢购
	 */
	public function search2() {
		//排序： s 综合 ，d 销量 ,p 价格 从小到大, pd 价格 从大到小
		$sort = I('sort', 's');
		$type = I('type', '1');
		$layout = I('get.layout', 'list');
		$gid=I('get.g_id','');
		if($gid==""){
			$gid=I('post.g_id','');
		}
		
		$map = array();
		$q = I('param.q','');
		$page = array('curpage' => I('p', 0,'intval'), 'size' => 10);
		$order = " id desc ";
		if ($sort == 's') {
			$order = " price desc";
		}
		if ($sort == 'p') {
			$order = " price desc";
		}
		if ($sort == 'pd') {
			$order = " price asc";
		}
		$this -> assign("g_id",$gid);
		$params = false;
        $now = time();
		
		if($gid!=""){
			if($gid == getDatatree("TODAY_PURCHASE")){
				$map=array(
					'g_id'=>getDatatree("FLASH_SALE"),
					'start_time'=>array(
                        'LT',$now
                    ),
                    'end_time'=>array(
                        array('gt',$now),array('lt',$now+24*3600,)
                    )
				);

			}else if($gid == getDatatree("WEEK_PURCHASE")){

				$map=array(
					'g_id'=>getDatatree("FLASH_SALE"),
                    'start_time'=>array(
                        array('lt',$now,)
                    ),
                    'end_time'=>array(
                        array('gt',$now + 24*3600),array('lt',$now + 7*24*3600,)
                    )
				);
			}else{
                $map=array(
                    'g_id'=>$gid,
                );
            }
			$map['onshelf']=1;
            $price_order = '';
            if ($sort == 's') {
                $price_order = "desc";
            }
            if ($sort == 'p') {
                $price_order = "desc";
            }
            if ($sort == 'pd') {
                $price_order = "asc";
            }
            if(!empty($q)){
               // $map['q'] = array('like','%'.$q.'%');
            }
			$result=apiCall(ProductGroupApi::GROUP_WITH_PRODUCT,array($map,$price_order));
		}else{

			$result = apiCall(ProductApi::QUERY_WITH_STORE, array($q,$type, $page, $order, $params));
		}
		if (!$result['status']) {
			$this -> error($result['info']);
		}
		$map1=array(
			'hidden_value'=>'fruitType',
		);
		
		$result1=apiCall(DatatreeApi::QUERY_NO_PAGING,array($map1));
		$this->assign("fruitTypes",$result1['info']);
		
		if($gid!=""){
			$list = $result['info'];
		}else{
			$list = $result['info']['list'];
		}
		if(!is_null($list)){
			$list = $this -> queryMonthlySales($list);
//			dump($list);
			if ($sort == 'd') {
				//对销量进行排序
//				$list = ($this->quickSort(0, count($list)-1,  $list));
				$list = $this->insert_sort($list);
			}
		}
		
		
		//dump($list);
		if(IS_POST){
//			echo json_encode($list);
			$this->success($list);
		}else{
			$this -> assign("q", $q);
			$this -> assign("layout", $layout);
			$this -> assign("sort", $sort);
			$this -> assign("curpage", $page['curpage']);
			$this -> assign("show", $result['info']['show']);
			$this -> assign("list", $list);
			$this ->theme($this->themeType)->display();
		}
	}

}
