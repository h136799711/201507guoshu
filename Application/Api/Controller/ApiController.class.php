<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/3
 * Time: 20:22
 */

namespace Api\Controller;


use Think\Controller\RestController;

abstract class ApiController extends RestController{

    protected  function param_get($name){
        return $this->param_filter($name,"get");
    }

    protected  function param_post($name){
        return $this->param_filter($name,"post");
    }

    protected function param_filter($name,$type){
        $name = I($type.'.'.$name,'');
        $name = str_replace(".".$this->_type,"",$name);
        return $name;
    }

    public function _empty(){
        $supportMethod = array();
        $supportMethod = $this->getSupportMethod();
        $data = array("status"=>-1,'supportMethod'=>$supportMethod);
        $this->response($data,"xml","404");
    }

    abstract function getSupportMethod();

}