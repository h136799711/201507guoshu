<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 15/7/8
 * Time: 14:50
 */

namespace Distributor\Api;


interface ICommissionCountInterface {
    /**
     * @param $uid 佣金得利者ID
     * @param $total_fee 总费用
     */
    function add($total_fee);
}