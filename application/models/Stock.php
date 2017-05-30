<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-7-13
 * Time: 下午10:02
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;

class Stock
{
    //array(
    //        "name" => "NTES",
    //        "type" => "米股",
    //        "price" => 64.55,
    //        "last_price" => 64.55,
    //        "lower_rate" => 40,     //每月跌幅最大比分，涨幅与之相反
    //        "upper_rate" => 66.5,
    //        "max" => 0,
    //        "min" => 0,
    //        "describe" => "xxx",
    //        ),


    public static function GetRandomStock($u)
    {
        $config_stocks = Config::get("stocks");
        $stocks = $u->stocks_market;
        $count = count($config_stocks);
        $no = rand(0, $count-1);
        $stock = $stocks[$config_stocks[$no]["name"]];

        $describes = array(
            "你很好的朋友悄悄地告诉你一个内幕消息，你现在买了肯定赚",
            "你看了天朝财经频道的特约股评家的报道，他推荐了一个股票基金 ，他說这个股票基金肯定会涨",
            "你看了报纸上的介绍，它上面说推荐一个潜在的黑马，你买了肯定不后悔",
        );

        $stock["describe"] = $describes[rand(0,count($describes)-1)];

        return new Stock($stock);
    }

    protected $me = NULL;

    public function __construct($me)
    {
        $this->me = $me;
    }

    public function Get()
    {
        return $this->me;
    }

    public function __get($varName)
    {
        if (!$this->me)
            return NULL;

        if (!isset($this->me[$varName]))
            return NULL;

        return $this->me[$varName];
    }

    public function __toString()
    {
        $me = &$this->me;

        $ret = $me["type"]." ".$me["name"]."\n";
        $ret .= $me["describe"]."\n";
        $ret .= "每股价格:".$me["price"]."\n";
        $ret .= "上月价格:".$me["last_price"]."\n";
        $amount = ($me["price"] - $me["last_price"]) * 100 / $me["last_price"];
        $ret .= "变化幅度:".number_format($amount,2).'%'."\n";
        $ret .= "历史最低:".$me["min"]."\n";
        $ret .= "历史最高:".$me["max"]."\n";
        return $ret;
    }

    public function GetMode() {
        $me = &$this->me;
        return $me["mode"];
    }

    public function GetName() {
        $me = &$this->me;
        return $me["name"];
    }

}