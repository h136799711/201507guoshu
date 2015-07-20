<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
//        $this->ajaxReturn(array('error'=>'DENY ACCESS!'),"xml");
        redirect("http://shuiguo.itboye.com/index.php/Admin/Public/login");
    }
    public function qrcode(){

        vendor("phpqrcode.phpqrcode");
        $data = I('get.text','http://www.gooraye.net','urldecode') ;

        // 纠错级别：L、M、Q、H
        $level = 'L';
        // 点的大小：1到10,用于手机端4就可以了
        $size = 4;
        // 生成的文件名
        $fileName = RUNTIME_PATH.'phpqrcode/'.time().'.png';
        \QRcode::png($data,$fileName,$level,$size,true);

    }
}