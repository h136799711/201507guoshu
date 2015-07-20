<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/2
 * Time: 14:12
 */

namespace Shop\Model;


use Think\Model;

class WalletModel extends Model{

    protected $_auto = array(
        array('update_time','time',self::MODEL_BOTH,'function'),
    );

}