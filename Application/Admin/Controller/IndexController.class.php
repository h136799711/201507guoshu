<?php
/**
 * (c) Copyright 2014 hebidu. All Rights Reserved. 
 */

namespace Admin\Controller;

class IndexController extends AdminController {

	//首页
    public function index(){
    		
        $this->display();
    }

    /**
     * 交易首页
     */
    public function tradeIndex(){
        $this->display();
    }

    /**
     * 分销首页
     */
    public function distribution(){
       $this->display();
    }
}