<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-4
 * Time: 下午7:23
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;

class Sale
{
//    protected static $sale =
//        array(
//            "name" => "某温州人买房",
//            "describe" => "",
//            "type => "房产"
//            "grade" => 1,
//            "keyword" => "",
//            "factor" => 1.0,
//            “temp" => array(
//                "real_estate_no" => 0,
//            ),
//        );

    public static function GetRandomSale()
    {
        $sales = Config::get("sales");
        $random = rand(0, count($sales)-1);
        $me = $sales[$random];
        $me["temp"] = array(
            "real_estate_no" => 0,
        );
        return new Sale($me);
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
        $ret = "".$me["name"]."\n".
            $me["describe"]."\n";
        if ($me["type"] == "房产") {
            switch ($me["grade"])
            {
                case 1: $ret = $ret."不过他只买小房子\n" ; break;
                case 2: $ret = $ret."不过他只买大房子\n" ; break;
                case 3: $ret = $ret."不过他只买豪宅\n" ; break;
                case 4: $ret = $ret."不过他只买别墅\n" ; break;
                default:
                    break;
            }
        } else if ($me["type"] == "商铺") {
            switch ($me["grade"])
            {
                case 1: $ret = $ret."不过他只买小商铺\n" ; break;
                case 2: $ret = $ret."不过他只买中商铺\n" ; break;
                case 3: $ret = $ret."不过他只买大商铺\n" ; break;
                default:
                    break;
            }
        }
        if ($me["keyword"] && $me["keyword"] != "" )
            $ret = $ret."而且他只考虑买 ".$me["keyword"];
        return $ret;
    }

    public function SetRealEstateNo($num) {
        $this->me["temp"]["real_estate_no"] = $num;
    }

    public function GetRealEstateNo() {
        return $this->me["temp"]["real_estate_no"];
    }
}