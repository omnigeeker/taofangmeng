<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-5
 * Time: 上午11:10
 * To change this template use File | Settings | File Templates.
 */

return array(
    "house" => array(
        "price" => 10000,	// 每平方米价格基数
        "rent" =>  30,		// 每月每平方米租金
        "tax_min" => 3,     // 交易税最低百分比
        "tax_max" => 10,    // 交易税最高百分比
    ),
    "shop" => array(
        "price" => 20000,	// 每平方米价格基数
        "rent" =>  100,		// 每月每平方米租金
        "tax_min" => 5,     // 交易税最低百分比
        "tax_max" => 20,    // 交易税最高百分比
    ),


    "accident" => array(
        "rate" => 4,        // 多少分之一的几率发生意外
    ),

    "sale" => array(
        "rate" => 4,        // 多少分之一的几率可以卖房子
    ),

    "certificate" => array(
        "max_count" => 5,   // 每个人挂证收入的最多个数
    ),

    // 失业相关参数
    "unemployed" => array(
        "rate" => 36,           // 多少分之一的几率会产生失业
        "first_safe_months" => 24,  // 第一次多少月内不会产生失业
        "safe_months" => 36,   // 多少月内不会产生失业
        "max_count" => 3,      // 失业最多的次数
    )
);