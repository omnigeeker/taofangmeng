<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-4
 * Time: 下午11:13
 * To change this template use File | Settings | File Templates.
 */

return array(
    //  mode-
    //    normal 一般罚现金，
    //    credit_card 可用信用卡支付的罚金
    //    cashflow 现金流型，
    //

    array(
        "name" => "买苹果手机",
        "mode" => "credit_card",
        "description" => "买iphone手机",
        "first" => 5000,
        "frequence" => 1,
        "condition_func" => function($u) {
            if ($u->IsUnemployed()) return false;
            return true;
        },
    ),
    array(
        "name" => "买平板手机",
        "mode" => "credit_card",
        "description" => "你购买了微软的平板电脑Surface",
        "first" => 7000,
        "frequence" => 1,
        "condition_func" => function($u) {
            $me = $u->Get();
            if ($u->IsUnemployed()) return false;
            return $me["cash"] > 10000;
        },
    ),
    array(
        "name" => "买笔记本电脑",
        "mode" => "credit_card",
        "description" => "买MacBookPro港版",
        "first" => 9000,
        "frequence" => 1,
        "condition_func" => function($u) {
            $me = $u->Get();
            if ($u->IsUnemployed()) return false;
            return $me["cash"] > 10000;
        },
    ),
    array(
        "name" => "出差不给报",
        "mode" => "normal",
        "description" => "你在公司出差，由于发票搞掉，你的坑爹公司不给报销",
        "first" => 1500,
        "frequence" => 1,
        "condition_func" => function($u) {
            if ($u->IsUnemployed()) return false;
            return true;
        }
    ),
    array(
        "name" => "去米国旅游",
        "mode" => "credit_card",
        "description" => "带着家庭出国旅游",
        "first" => 40000,
        "frequence" => 1,
        "condition_func" => function($u) {
            $me = $u->Get();
            if ($u->IsUnemployed()) return false;
            return $me["cash"] > 50000;
        },
    ),
    array(
        "name" => "去西方旅游",
        "mode" => "credit_card",
        "description" => "带着家庭出国旅游",
        "first" => 50000,
        "frequence" => 1,
        "condition_func" => function($u) {
            $me = $u->Get();
            if ($u->IsUnemployed()) return false;
            return $me["cash"] > 50000;
        },
    ),
    array(
        "name" => "去T国旅游",
        "mode" => "credit_card",
        "description" => "带着家庭出国旅游",
        "frequence" => 1,
        "first" => 20000,
        "condition_func" => function($u) {
            $me = $u->Get();
            if ($u->IsUnemployed()) return false;
            return $me["cash"] > 20000;
        },
    ),
    array(
        "name" => "去国内四亚旅游",
        "mode" => "credit_card",
        "description" => "带着家庭出国旅游，但是当地宰客，支出大于预期",
        "first" => 35000,
        "frequence" => 1,
        "condition_func" => function($u) {
            $me = $u->Get();
            if ($u->IsUnemployed()) return false;
            return $me["cash"] > 40000;
        },
    ),
    array(
        "name" => "去国内西川旅游",
        "mode" => "credit_card",
        "description" => "带着家庭出国旅游，西川朴实，花费不多",
        "first" => 15000,
        "frequence" => 1,
        "condition_func" => function($u) {
            $me = $u->Get();
            if ($u->IsUnemployed()) return false;
            return $me["cash"] > 10000;
        },
    ),
    array(
        "name" => "买奢侈品",
        "mode" => "credit_card",
        "description" => "路上路过奢侈品店，看到奢侈品，忍不住了，购买了送老婆/老公",
        "first" => 8000,
        "frequence" => 1,
        "condition_func" => function($u) {
            $me = $u->Get();
            if ($u->IsUnemployed()) return false;
            return $me["cash"] > 10000;
        },
    ),
);