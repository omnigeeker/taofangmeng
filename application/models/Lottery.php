<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-10
 * Time: 下午5:53
 * To change this template use File | Settings | File Templates.
 */

class Lottery{

//    $lotteries = array(
//        "name" => "买双颜球彩票",
//        "describe" => "双颜球彩票是天朝最流行的彩票之一，只需要[first]元买50注，就有机会获得[total]元的\n".
//        "[梦梦提示这种中奖几率几乎可以忽悠不计]",
//        "first" => 100,
//        "award" => 100000000,
//        "rate" => 2000000
//    )

    public static function GetRandomLottery()
    {
        $lotteries = Config::get("lotteries");
        $random = rand(0, count($lotteries)-1);
        $lottery = $lotteries[$random];
        return new Lottery($lottery);
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
        $describe = str_replace('[first]', $me['first'], $describe);
        $describe = str_replace('[award]', $me['award'], $describe);

        $ret = "".$me["name"]."\n".
            $describe."\n".
            "成本:".$me["first"]."元\n".
            "奖品:".$me["award"]."元\n";

        return $ret;
    }
}