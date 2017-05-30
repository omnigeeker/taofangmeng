<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-3
 * Time: 下午9:31
 * To change this template use File | Settings | File Templates.
 */

class HouseProfile extends RealEstateProfile
{
    public function CreateFromHouse($focus_house, $step)
    {
        parent::CreateNew($focus_house, $step);

        $me = &$this->me;
        $me["ext"] = array(
            'is_self' => false,
        );
    }

    public function AdjustPriceByRate($ratePrice, $rateRent)
    {
        $me = &$this->me;
        $me["now"]["price"] = (int)($me["now"]["price"] * (1.0 + $ratePrice));
        $me["now"]["rent"] = (int)($me["now"]["rent"] * (1.0 + $rateRent));
    }

    public function AdjustPriceByMonth()
    {
    }

    public function SetIsSelf($isSelf)
    {
        $me["ext"]['is_self'] = $isSelf;
    }

    public function IsSelf()
    {
        return $this->me["ext"]['is_self'];
    }

    public function NextStep($nowStep)
    {
        $me = &$this->me;

        if ($this->IsSelf() == false)
        {
            parent::NextStep($nowStep);
            return;
        }

        Log::info("NextStep");
        // 贷款已经还完
        if ($this->IsEnd())
        {   //贷款已经还完
            // 调整月供和剩余贷款为0
            $me['now']['loan'] = 0;
            $me["now"]["month_payment"] = 0;
            //$me["now"]["receive"] += $me["now"]["rent"];
            return;
        }

        // 自住房不收房租
        $me["now"]["paid"] += $me["now"]["month_payment"];
        $me['now']['left_month'] -= 1;

        assert( $nowStep-$me["step"]+$me['now']['left_month'] == $me['buy']['left_month']);
        // 归还部门本金
        // $me['now']['loan'] 减少
        // Ａ×Ｃ×（１＋Ｃ）^(ｎ－１)／（（１＋Ｃ）^Ｂ－１）
        // 每月归还本金 = 总贷款*月利率*（1+月利率)/( (1+月利率)^(总时间) - 1 )
        //
        $A = $me['buy']['loan'];
        $C = $me['buy']['loan_rate'] / 12.0;
        $B = $me['buy']['left_month'];
        $n = $me['buy']['left_month'] - $me['now']['left_month'];

        $thisMonthLoan = round( $A*$C*pow(1+$C, $n-1)/(pow(1+$C, $B) -1) );
        $me['now']['loan'] -= $thisMonthLoan;
    }

    public function GetDetailString() {
        $me = &$this->me;
        $ret = "房产: ".$me["name"]."\n".
            "当前价值: ".$this->GetRealValue()."\n".
            "买入总价格: ".$me["buy"]["price"]."\n".
            "买入时间: ".$this->GetBuyTime()."\n".
            "买入总首付：".$this->GetFirstPayment()."\n";
        if ($me["now"]["loan"] > 0)
        {
            $ret = $ret."剩余贷款： ".$me["now"]["loan"]."\n".
                "月供: ".$me["now"]["month_payment"]."\n".
                "贷款月数:".$me["buy"]["left_month"]."（剩".$me['now']['left_month'].")\n".
                "已总共支付：".($me["now"]["paid"] - $me["now"]["receive"])."\n";
        }

        if ($this->IsSelf() == true)
        {
            $ret = $ret."自住用,无法收取租金\n";
        }
        else
        {
            $ret = $ret."投资租金: ".$me["now"]["rent"]."\n".
                "现金流收益: ".$this->GetCashFlow()."\n";
        }
        return $ret;
    }

    public function Prepayment()
    {

    }

    public function __toString() {
        $me = &$this->me;
        $ret = "房产: ".$me["name"]."\n".
            "当前价值: ".$this->GetRealValue()."\n".
            "买入总价格: ".$me["buy"]["price"]."\n".
            "买入总首付：".$this->GetFirstPayment()."\n";
        if ($me["now"]["loan"] > 0)
        {
            $ret .= "剩余贷款： ".$me["now"]["loan"]."\n";
        }

        if ($this->IsSelf() == true)
        {
            $ret .= "自住用\n";
        }
        else
        {
            $ret .= "月租: ".$this->GetMonthIncome()."\n".
                "月供: ".$this->GetMonthOverlay()."\n".
                "月现金流: ".$this->GetCashFlow()."\n";
        }
        return $ret;
    }
}
