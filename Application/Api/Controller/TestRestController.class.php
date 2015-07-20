<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/3
 * Time: 20:22
 */

namespace Api\Controller;

class TestRestController extends ApiController{

    protected  $allowType = array("json","xml","rss","html");


    public function index_get_json($id){
        $id = $this->param_get("id");
        dump($id);

        $data = array("status"=>false,'info'=>$id);
        $this->response($data,'json');
    }

//    public function index_get_html($id){
//        $id = $this->param_get("id");
//
//        $this->response($id,'html');
//    }


    public function index_get_rss($id){
        $id = $this->param_get("id");

        $this->response(array('item'=>array(array('status'=>'3','p'=>'3'),array('status'=>'3','p'=>'3'),array('status'=>'3','p'=>'3'))),'xml');
    }

    function getSupportMethod()
    {
        return array(
            'index_get'=>array(
                'author'=>'hebidu [hebiduhebi@163.com]',
                'version'=>'1.0.0',
                'description'=>'GET: TestRest/index/[X].json',
                'demo_url'=>'http://www.baidu.com',
            ),
            'index_post'=>array(
                'author'=>'hebidu [hebiduhebi@163.com]',
                'version'=>'1.0.0',
                'description'=>'GET: TestRest/index/[X].json',
                'demo_url'=>'http://www.baidu.com',
            ),
        );
    }
}