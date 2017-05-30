<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-12
 * Time: 上午11:08
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;

class Licai {

//    array(
//        "name" => "理财产品A",
//        "describe" => "你的好友向你推荐了一个很好的理财产品，风险还好\n",
//        "first" => 30000,
//        "rate" => 5,
//        "good_rate" => 5,
//        "months" => 6,
//        "is_save" => true,      // 保本
//        "succeed_probability" => 80,    // 成功概率
//        "good_probability" => 20,         // 非常好概率，超过rate
//        "failed_probability" => 20,     // 失败概率，没有保本的概率
//    ),



    public static function GetRandomLicai()
    {
        $licais = Config::get("licais");
        $random = rand(0, count($licais)-1);
        $licai = $licais[$random];
        return new Licai($licai);
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

        $describe = $me["describe"];
        $describe = strtr($describe, '[first]', $me['first']);

        $ret = $me["name"]."\n";
        $ret .= $describe."\n";
        $ret .= "起投金额:".$me["first"]."\n";
        $ret .= "年均利率:".$me["rate"]."%\n";
        $ret .= "最高年利率:".$me["good_rate"]."%\n";
        $ret .= "承诺保本:".($me["is_save"]?"是":"否")."\n";
        $ret .= "理财时间:".$me["months"]."个月\n";
        if ($me["good_rate"] > $me["rate"])
            $ret .= "理财产品不保本，有赚就有赔；投资有风险，理财需谨慎。";
        return $ret;
    }

}