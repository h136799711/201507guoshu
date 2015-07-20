<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/1
 * Time: 08:47
 */

namespace Admin\Model;
use Think\Model;

class HooksModel extends  Model{

    protected $tablePrefix = "common_";

    protected $_auto = array(
        array('update_time','time',self::MODEL_BOTH,'function')
    );

}