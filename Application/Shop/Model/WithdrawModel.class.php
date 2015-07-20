<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/2
 * Time: 14:10
 */

namespace Shop\Model;


use Think\Model;

class WithdrawModel extends Model{

    /**
     *  等待审核
     */
    const WAIT_VERIFY = 0;

    /**
     * 审核通过
     */
    const PASS = 1;

    /**
     * 审核失败
     */
    const DENY = 2;

    protected $_auto = array(
        array('create_time','time',self::MODEL_INSERT,"function"),
        array('update_time','time',self::MODEL_BOTH,"function"),
    );

}