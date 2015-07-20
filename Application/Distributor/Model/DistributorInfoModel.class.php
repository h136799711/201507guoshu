<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/4
 * Time: 16:54
 */

namespace Distributor\Model;


use Think\Model;

class DistributorInfoModel extends  Model{

    protected  $_auto = array(
        array('create_time','time',self::MODEL_INSERT,"function"),
    );
}