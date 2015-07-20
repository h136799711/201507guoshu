<?php
// .-----------------------------------------------------------------------------------
// | WE TRY THE BEST WAY 杭州博也网络科技有限公司
// |-----------------------------------------------------------------------------------
// | Author: 贝贝 <hebiduhebi@163.com>
// | Copyright (c) 2013-2016, http://www.itboye.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
namespace Shop\Api;
use Common\Api\Api;
use Shop\Model\BannersModel;
use Think\Page;

class BannersApi extends Api{

    /**
     * 查询数据包含位置名称
     */
    const QUERY_NO_PAING = "Shop/Banners/queryNoPaging";
    /**
     * 查询数据包含位置名称
     */
    const QUERY_WITH_POSITION = "Shop/Banners/queryWithPosition";
    /**
     * 添加
     */
    const ADD = "Shop/Banners/add";
    /**
     * 保存
     */
    const SAVE = "Shop/Banners/save";
    /**
     * 保存根据ID主键
     */
    const SAVE_BY_ID = "Shop/Banners/saveByID";

    /**
     * 删除
     */
    const DELETE = "Shop/Banners/delete";

    /**
     * 查询
     */
    const QUERY = "Shop/Banners/query";
    /**
     * 查询一条数据
     */
    const GET_INFO = "Shop/Banners/getInfo";


    protected function _init(){
		$this->model = new BannersModel();
	}

    /**
     * @param null $map
     * @param array $page
     * @param bool $order
     * @param bool $params
     * @return array
     */
    public function queryWithPosition($map = null, $page = array('curpage'=>0,'size'=>10), $order = false, $params = false){

        $field = 'dt.name as position_name,banner.url,banner.noticetime,banner.endtime,banner.starttime,banner.title,banner.createtime,banner.storeid,banner.id,banner.img,banner.position,banner.notes,banner.uid';

        $query = $this->model->alias(" as banner ")->field($field)->join('LEFT JOIN common_datatree as dt ON dt.id = banner.position');
        if(!is_null($map)){
            $query = $query->where($map);
        }
        if(!($order === false)){
            $query = $query->order($order);
        }

        $list = $query -> page($page['curpage'] . ',' . $page['size']) -> select();


        if ($list === false) {
            $error = $this -> model -> getDbError();
            return $this -> apiReturnErr($error);
        }

        $count = $this -> model -> where($map) -> count();
        // 查询满足要求的总记录数
        $Page = new Page($count, $page['size']);

        //分页跳转的时候保证查询条件
        if ($params !== false) {
            foreach ($params as $key => $val) {
                $Page -> parameter[$key] = urlencode($val);
            }
        }

        // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page -> show();

        return $this -> apiReturnSuc(array("show" => $show, "list" => $list));
    }
}

