<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-8-10
 * Time: 下午12:59
 * To change this template use File | Settings | File Templates.
 */

return array(
    "accidents" => array(
        /* mode-
         *      normal 一般罚现金
         *      credit_card 可用信用卡支付的罚金
         *      result 直接结果
         */

        array(
            "name" => "小孩出生",
            "tag" => "family",
            "level" => "basic",
            "frequence" => 1,
            "mode" => "cashflow",
            "first" => 0,
            "month" => 500,
            "period" => 0,
            "description" => "生了个建设银行，每月小孩支出增加500",
            /* 其中 out 只能是 pay_live, pay_car,
             * pay_kid, pay_old, pay_other
             */
            "out" => "pay_kid",
        ),

        array(
            "name" => "小孩出生2",
            "tag" => "family",
            "level" => "basic",
            "frequence" => 1,
            "mode" => "result",
            "first" => 0,
            "month" => 0,
            "period" => 0,
            "description" => "生了个建设银行,每月小孩支出翻倍",

            /*
             *
             */
            "result_func" => function($u) {
                $me = &$u->me;
                $me["now"]["pay_kid"] *= 2;
            },
        ),
    ),
);