<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 下午1:04
 * To change this template use File | Settings | File Templates.
 */

class LoanProfile {

    protected $me;

    public function CreateNew($name, $step, $type,  $total, $rate, $months)
    {
        $this->me = array(
            "name" => $name,
            "type" => $type,
            "step" => $step,

            "first" => array(
                "total" => $total,
                "rate" => $rate,
                "months" => $months,
            ),
            "now" => array(
                "loan" => $total,
                "left_month" => $months,
                "month_payment" => 0,
                "paid" => 0,
            ),
        );

        $this->me["now"]["month_payment"] = LoanProfile::CaclMonthPayment($total,  $rate, $months);
    }

    public static function CaclMonthPayment($loan, $rate, $months)
    {
        $monthRate = $rate / 12.0;
        //月供=[本金*月利率*(1+月利率)^贷款月数]/[(1+月利率)^贷款月数-1]
        $temp = pow(1+$monthRate, $months);
        $monthPayment = (int)(($monthRate*$loan*$temp)/($temp-1));
        return $monthPayment;
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

    public function GetMonthOverlay()
    {
        return $this->me["now"]["month_payment"];
    }

    public function GetTotal()
    {
        return $this->me["first"]["total"];
    }

    public function GetLeftLoan() {
        return $this->me["now"]["loan"];
    }

    public function IsEnd()
    {
        return ($this->me['now']['left_month'] <= 0) || ($this->me['now']['loan'] <= 0);
    }

    public function NextStep($nowStep)
    {
        Log::info("NextStep");
        $me = &$this->me;

        // 贷款已经还完
        if ($this->IsEnd() )
        {   //贷款已经还完
            return;
        }
        // 已经付款总金额加上月供

        $me["now"]["paid"]  += $me["now"]["month_payment"];
        $me['now']['left_month'] -= 1;

        assert( $nowStep-$me["step"]+$me['now']['left_month'] == $me['first']['months']);

        // 归还部门本金
        // $me['now']['loan'] 减少
        // Ａ×Ｃ×（１＋Ｃ）^(ｎ－１)／（（１＋Ｃ）^Ｂ－１）
        // 每月归还本金 = 总贷款*月利率*（1+月利率)/( (1+月利率)^(总时间) - 1 )
        //
        $A = $me['first']['total'];
        $C = $me['first']['rate'] / 12.0;
        $B = $me['first']['months'];
        $n = $me['first']['months'] - $me['now']['left_month'];

        $thisMonthLoan = round( $A*$C*pow(1+$C, $n-1)/(pow(1+$C, $B) -1) );
        $me['now']['loan'] = $me['now']['loan'] - $thisMonthLoan;
    }

    public function Refund($money)
    {
        $me = &$this->me;
        $me["now"]["paid"] = $me["now"]["paid"] + $money;
        if ($money >= $this->GetLeftLoan())
        {
            $me["now"]["loan"] = 0;
            $me["now"]["month_payment"] = 0;
            return;
        }

        $left_loan = $me["now"]["loan"] - $money;

        $me["now"]["month_payment"] = round($me["now"]["month_payment"] * $left_loan / $me["now"]["loan"]);
        $me["now"]["loan"] = $left_loan;
    }

    public function __toString()
    {
        $me = &$this->me;
        $ret = "借款: ".$me["name"]."\n".
            "剩余借款：".$me['now']['loan']."\n".
            "每月支出：".$me['now']['month_payment']."\n";
        return $ret;
    }

    public function GetDetailString()
    {
        $me = &$this->me;
        $ret = "借款: ".$me["name"]."\n".
            "总借款：".$me['first']['total']."\n".
            "剩余月数：".$me['now']['left_month'].'/'.$me['first']['months']."\n".
            "剩余借款：".$me['now']['loan']."\n".
            "每月支出：".$me['now']['month_payment']."\n";
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