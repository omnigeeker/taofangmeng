<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-12
 * Time: 上午11:17
 * To change this template use File | Settings | File Templates.
 */


return array(
    // 小房子
    //  mode- normal 一般罚现金，cashflow 现金流型，house 房子
    array(
        "name" => "理财产品A",
        "describe" => "你的好友向你推荐了一个很好的理财产品，风险还好\n",
        "first" => 30000,
        "rate" => 5,
        "good_rate" => 5,
        "months" => 6,
        "is_save" => true,      // 保本
        "succeed_probability" => 80,    // 成功概率
        "good_probability" => 20,         // 非常好概率，超过rate
        "failed_probability" => 20,     // 失败概率，没有保本的概率
    ),

    array(
        "name" => "理财产品B",
        "describe" => "你的好友向你推荐了一个很好的理财产品，风险还好",
        "first" => 10000,
        "rate" => 3,
        "good_rate" => 5,
        "months" => 3,
        "is_save" => true,      // 保本
        "succeed_probability" => 90,    // 成功概率
        "good_probability" => 20,         // 非常好概率，超过rate
        "failed_probability" => 20,     // 失败概率，没有保本的概率
    ),

    array(
        "name" => "理财产品C",
        "describe" => "你的好友向你推荐了一个很好的理财产品，风险是有的",
        "first" => 50000,
        "rate" => 10,
        "good_rate" => 10,
        "months" => 12,
        "is_save" => true,      // 保本
        "succeed_probability" => 60,    // 成功概率
        "good_probability" => 0,         // 非常好概率，超过rate
        "failed_probability" => 20,     // 失败概率，没有保本的概率
    ),

    array(
        "name" => "国外理财产品X",
        "describe" => "你的好友向你推荐了一个他认为很好的理财产品，风险有一些",
        "first" => 20000,
        "rate" => 20,
        "good_rate" => 50,
        "months" => 6,
        "is_save" => false,      // 保本
        "succeed_probability" => 60,    // 成功概率
        "good_probability" => 20,         // 非常好概率，超过rate
        "failed_probability" => 30,     // 失败概率，没有保本的概率
    ),

    array(
        "name" => "国外理财产品Y",
        "describe" => "你的好友向你推荐了一个他认为很好的理财产品，风险有一些",
        "first" => 40000,
        "rate" => 20,
        "good_rate" => 50,
        "months" => 6,
        "is_save" => false,      // 保本
        "succeed_probability" => 60,    // 成功概率
        "good_probability" => 20,         // 非常好概率，超过rate
        "failed_probability" => 30,     // 失败概率，没有保本的概率
    ),

    array(
        "name" => "国外理财产品Z",
        "describe" => "你的好友向你推荐了一个他认为很好的理财产品，风险有一些",
        "first" => 60000,
        "rate" => 20,
        "good_rate" => 50,
        "months" => 6,
        "is_save" => false,      // 保本
        "succeed_probability" => 60,    // 成功概率
        "good_probability" => 20,         // 非常好概率，超过rate
        "failed_probability" => 30,     // 失败概率，没有保本的概率
    ),
);