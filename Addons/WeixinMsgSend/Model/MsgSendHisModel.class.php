<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/4
 * Time: 21:10
 */

namespace Addons\WeixinMsgSend\Model;

use Think\Model;

class MsgSendHisModel extends Model{

    protected $_auto = array(
        array('create_time','time',self::MODEL_INSERT,'function'),
    );

}