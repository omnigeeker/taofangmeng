<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午11:10
 * To change this template use File | Settings | File Templates.
 */

class RealEstateProfile extends AssetProfile
{
    public function CreateNew(&$focusRealEstate, $step)
    {
        $realEstate = &$focusRealEstate;
        $this->me = array(
            "type" => $focusRealEstate["type"],
            "name" => $focusRealEstate["name"],
            "describe" => $focusRealEstate["describe"],
            "step" => $step,        // 买入时间
            //"is_self" => $isSelf,
            "grade" => $focusRealEstate["grade"],

            "buy" => array(
                "price" => $realEstate["price"],
                "rent" => $realEstate["rent"],
                "left_month" => $realEstate["ext"]["left_month"],
                "down_payment" => $realEstate["ext"]["down_payment"],
                "loan" =>  $realEstate["ext"]["loan"],
                "month_payment" => $realEstate["ext"]["month_payment"],
                "tax" => $realEstate["ext"]["tax_payment"],  // 购买时交的税
                "loan_rate" => $realEstate["ext"]["loan_rate"],
                "sale_rate" => $realEstate["sale_rate"],
            ),
            "now" => array(
                "price" => $realEstate["price"],
                "rent" => $realEstate["rent"],
                "loan" => $realEstate["ext"]["loan"],
                "left_month" => $realEstate["ext"]["left_month"],
                "month_payment" => $realEstate["ext"]["month_payment"],
                "paid" => $realEstate["ext"]["first"],
                "receive" => 0,
            ),

            "ext" => NULL,
        );
    }

    public function GetName() {
        return $this->me["name"];
    }

    public function GetType() {
        return $this->me["type"];
    }

    public function GetDiscribe() {
        return $this->me["describe"];
    }

    public function GetCashFlow()
    {
        $me = &$this->me;
        return $me["now"]["rent"] - $me["now"]["month_payment"];
    }

    public function GetMonthIncome()
    {
        return  $this->me["now"]["rent"];
    }

    public function GetMonthOverlay()
    {
        return $this->me["now"]["month_payment"];
    }

    public function GetAssetValue()
    {
        return $this->me["now"]["price"];
    }

    public function GetLiabilities()
    {
        return $this->me["now"]["loan"];
    }

    public function GetAllReceived()
    {
        return $this->me["now"]["receive"];
    }

    public function GetAllPaid()
    {
        return $this->me["now"]["paid"];
    }

    public function GetRentSaleRate()
    {
        $me = &$this->me;
        return (int)($me["now"]["price"]/($me["now"]["rent"]*12));
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

    /**
     * @param $nowStep
     * @param $money
     */
    public function RepayAheadOfTime($nowStep, $money)
    {
        //
    }

    /** 获得突然卖掉房子获得的现金
     * @return mixed
     */
    public function GetSellMoney($money)
    {
        $me = &$this->me;
        if (!$money)
            $money = $me["now"]["price"];
        return $money - $me["now"]["loan"];
    }

    public function GetBuyTime()
    {
        $step = $this->me['step'];
        $year = BEGIN_YEAR + (int)($step/12);
        $month = 1 + $this->me['step'] % 12;
        return $year."年".$month."月";
    }

    public function GetRealValue()
    {
        $me = &$this->me;
        $value = $me["now"]["price"];
        $value = 1000 * round($value / 1000.0);
        return $value;
    }

    public function GetSaleValue()
    {
        $me = &$this->me;
        $value = (int)($me["now"]["price"] * $me["buy"]["sale_rate"]);
        $value = 1000 * round($value / 1000.0);
        return $value;
    }

    public function GetFirstPayment()
    {
        $me = &$this->me;
        return (int)($me["buy"]["down_payment"] + $me["buy"]["tax"]);
    }

    public function __toString() {
        $me = &$this->me;
        $ret = $this->GetType().": ".$me["name"]."\n".
            "当前价值: ".$this->GetRealValue()."\n".
            "买入总价格: ".$me["buy"]["price"]."\n".
            "买入总首付：".$this->GetFirstPayment()."\n";
        if ($me["now"]["loan"] > 0)
        {
            $ret .= "剩余贷款： ".$me["now"]["loan"]."\n".
                "月收入: ".$this->GetMonthIncome()."\n".
                "月支出: ".$this->GetMonthOverlay()."\n".
                "月现金流: ".$this->GetCashFlow()."\n";
        } else {
            $ret .= "所有还款已经还完\n".
                "月收入: ".$this->GetMonthIncome()."\n".
                "月现金流: ".$this->GetCashFlow()."\n";
        }
        return $ret;
    }

    public function GetSimpleString() {
        $me = &$this->me;
        $ret = $this->GetType().": ".$this->GetName()."\n".
            "当前价值: ".$this->GetRealValue()."\n".
            "现金流: ".$this->GetCashFlow()."\n".
            "售租比: ".$this->GetRentSaleRate()."：1\n";
        return $ret;
    }

    public function GetDetailString() {
        $me = &$this->me;
        $ret = $this->GetType().": ".$this->GetName()."\n".
            "当前价值: ".$this->GetRealValue()."\n".
            "买入总价格: ".$me["buy"]["price"]."\n".
            "买入时间: ".$this->GetBuyTime()."\n".
            "买入总首付：".$this->GetFirstPayment()."\n";
        if ($me["now"]["loan"] > 0)
        {
            $ret = $ret."剩余贷款： ".$me["now"]["loan"]."\n".
                "月供: ".$me["now"]["month_payment"]."\n".
                "贷款月数:".$me["buy"]["left_month"]."（剩".$me['now']['left_month'].")\n".
                "已总共支付：".($me["now"]["paid"] - $me["now"]["receive"])."\n".
                "投资租金: ".$me["now"]["rent"]."\n".
                "现金流收益: ".$this->GetCashFlow()."\n";
        }
        return $ret;
    }

    public function IsEnd()
    {
        return ($this->me['now']['left_month'] <= 0) || ($this->me['now']['loan'] <= 0);
    }

    public function NextStep($nowStep)
    {
        $me = &$this->me;
        Log::info("NextStep");

        // 贷款已经还完
        if ($this->IsEnd() )
        {   //贷款已经还完
            $me["now"]["receive"] = $me["now"]["receive"] + $me["now"]["rent"];
            return;
        }
        // 已经付款总金额加上月供

        //
        $me["now"]["paid"] += $me["now"]["month_payment"];
        $me["now"]["receive"] += $me["now"]["rent"];
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
}