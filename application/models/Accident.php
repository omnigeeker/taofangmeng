<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-4
 * Time: 下午7:00
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;

class Accident
{
//    protected static $accident =
//        array(
//            "name" => "钱被偷了",
//            "mode" => "normal",
//            "describe" => "菜市场买才钱被偷了",
//            "cash" => 100,
//        );

    public static function GetAccidents()
    {
        $accidents = CSVReader::GetArrayFromCSV("accidents.csv");
        $new_accidents = Config::get("accidents");
        //return Config::get("test.accidents");
        $all = array_merge($accidents, $new_accidents);
        return $all;
    }

    public static function GetRandomAccident($u)
    {
        $accidents = Accident::GetAccidents();
        while(true) {
            $random = rand(0, count($accidents)-1);
            $accident = $accidents[$random];

            $name = $accident["name"];
            $frequence = $accident["frequence"];
            $recordAccidents = $u->accidents;
            $step = $u->step;
            if (isset($recordAccidents[$name])) {
                $lastStep = $recordAccidents[$name];
                if ($step - $lastStep < $frequence)
                    continue;
            }


            if (isset($accident["condition_func"])) {
                if ($accident["condition_func"]($u)) {
                    return new Accident($accident);
                }
            } else {
                if (isset($accident["arg1"])) {
                    if (!$u->CheckByAccident(
                        $accident["arg1"],$accident["op1"],$accident["value1"])) {
                        continue;
                    }
                }
                if (isset($accident["arg2"])) {
                    if (!$u->CheckByAccident(
                        $accident["arg2"],$accident["op2"],$accident["value2"])) {
                        continue;
                    }
                }
                if (isset($accident["arg3"])) {
                    if (!$u->CheckByAccident(
                        $accident["arg3"],$accident["op3"],$accident["value3"])) {
                        continue;
                    }
                }
                if (isset($accident["arg4"])) {
                    if (!$u->CheckByAccident(
                        $accident["arg4"],$accident["op4"],$accident["value4"])) {
                        continue;
                    }
                }
                return new Accident($accident);
            }
        }
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

        if (!isset($this->me[$varName])) {
            if ($varName == "pay_type")
                return "pay_other";
            return NULL;
        }

        return $this->me[$varName];
    }

    public function GetOut() {
        $me = &$this->me;
        if (empty($me["out"]))
            return "pay_other";
        return $me["out"];
    }

    public function __toString()
    {
        $me = &$this->me;

        $ret = "".$me["name"]."\n".$me["description"]."\n";
        switch ($me["mode"])
        {
            case "normal":
                $total = $me["first"];
                $ret .= "你的现金将减少 $total\n";
                break;
            case "credit_card":
                $total = $me["first"];
                $ret .= "这个消费可以使用信用卡分期支付\n";
                $ret .= "如果一次性支付：\n";
                $ret .= "  现金将减少 $total\n";
                $monthPrement = LoanProfile::CaclMonthPayment($total, CREDIT_RATE, CREDIT_MONTHS);
                $ret .= "如果信用卡支付:\n";
                $ret .= "  分期:".CREDIT_MONTHS."个月\n";
                $ret .= "  每月还款：".$monthPrement."\n";
                break;
            case "cashflow":
                $ret .= "你第一次支付 ".$me["first"]."\n";
                $ret .= "你之后每月支付 ".$me["month"]."\n";
                break;
            case "result":
                $ret .= "你第一次支付 ".$me["first"]."\n";
                $ret .= "你之后按照以上描诉的情况支付更多费用\n";
                break;
            default:
                break;
        }
        return $ret;
    }

    public function GetMode() {
        $me = &$this->me;
        return trim($me["mode"]);
    }

    public function CanCreditCard() {
        $me = &$this->me;
        if (empty($me["credit_card"]) || $me["credit_card"] == false )
            return false;
        return true;
    }

    public function GetFirst() {
        $me = &$this->me;
        return $me["first"];
    }

    /**
     * @param $increment=array(
     *              "accident" => 1.0
     *          )
     */
    public function AdjustPrice($increment)
    {
        $me = &$this->me;
        if ($me["mode"] === "normal" ||
            $me["mode"] === "credit_card" ||
            $me["mode"] === "result")
        {
            $me["first"] = (int)($me["first"] * $increment["accident"]);
            $me["first"] = 100 * (int)($me["first"] / 100);
        }
        if ($me["mode"] === "cashflow")
        {
            $me["first"] = (int)($me["first"] * $increment["accident"]);
            $me["first"] = 100 * (int)($me["first"] / 100);
            $me["month"] = (int)($me["month"] * $increment["accident"]);
            $me["month"] = 100 * (int)($me["month"] / 100);
        }
    }
}