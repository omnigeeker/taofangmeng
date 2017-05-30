<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-12
 * Time: 下午1:12
 * To change this template use File | Settings | File Templates.
 */

class LicaiProfile {

    protected $me;

    public function CreateNew($licai, $step, $money)
    {
        $l = $licai->Get();
        $this->me = array(
            "name" => $l["name"],
            "describe" => $l["describe"],
            "first" => $l["first"],
            "rate" => $l["rate"],
            "good_rate" => $l["good_rate"],
            "months" => $l["months"],
            "is_save" => $l["is_save"],
            "succeed_probability" => $l["succeed_probability"],
            "good_probability" => $l["good_probability"],
            "failed_probability" => $l["failed_probability"],

            "step" => $step,
            "money" => $money,
            "left_month" => $l["months"],
        );

    }

    public function CreateFromValue(&$value)
    {
        $this->me = &$value;
    }

    public function Get()
    {
        return $this->me;
    }

    public function GetName()
    {
        return $this->me["name"];
    }

    public function IsEnd()
    {
        return $this->me['left_month'] <= 0;
    }

    public function NextStep($nowStep)
    {
        $me = &$this->me;
        // 贷款已经还完
        if ($this->IsEnd() )
        {   //贷款已经还完
            return;
        }
        $me["left_month"] -= 1;
    }

    /**
     * @param $money
     * @return array(
     *      "money" => 100000,
     *      "result" => "成功|非常成功|不成功|失败",
     *      "str" => "描述",
     */
    public function GetResult()
    {
        assert($this->IsEnd());
        $me = &$this->me;

        //看看成功
        $random = rand(1, 100);
        if ($random <= $me["succeed_probability"])
        {   // 成功
            if ($me["good_rate"] > $me["rate"] &&
                $random <= $me["good_probability"])
            {   // 投资非常成功
                $rate = rand($me["rate"], $me["good_rate"]);
                $real_rate =  $rate * $me["months"] / 12;
                $money = $me["money"] * (1 + $real_rate / 100.0);
                $interest = $money - $me["money"];
                $ret = array(
                    "money" => $me["money"] * (1 + $real_rate / 100.0),
                    "result" => "非常成功",
                    "str" => "你投资非常成功，你投资做到了年利率".$rate."%，得到了利息 $interest",
                );
                return $ret;
            }
            else
            {   // 成功
                $rate = $me["rate"];
                $real_rate =  $rate * $me["months"] / 12;
                $money = $me["money"] * (1 + $real_rate / 100.0);
                $interest = $money - $me["money"];
                $ret = array(
                    "money" => $me["money"] * (1 + $real_rate / 100.0),
                    "result" => "成功",
                    "str" => "你投资成功，你得到了利息 $interest",
                );
                return $ret;
            }
        }
        else
        {   // 不成功
            $random = rand(1, 100);
            if ($me["is_save"] || $random > $me["failed_probability"])
            {   // 不成功
                $ret = array(
                    "money" => $me["money"],
                    "result" => "不成功",
                    "str" => "很遗憾，你投资不成功，只拿回了本钱",
                );
                return $ret;
            }
            else
            {   // 失败
                $money = rand(1, $me["money"]);
                $ret = array(
                    "money" => $money,
                    "result" => "失败",
                    "str" => "很遗憾，你投资彻底失败，连本钱都没有保住",
                );
                return $ret;
            }
        }
    }

    public function __toString()
    {
        $me = &$this->me;
        $ret = $me["name"]."\n".
            "投入资金：".$me['money']."\n".
            "剩余时间：".$me['left_month']."个月\n";
        return $ret;
    }

    public function __get($varName)
    {
        if (!$this->me)
            return NULL;

        if (!isset($this->me[$varName]))
            return NULL;

        return $this->me[$varName];
    }
}