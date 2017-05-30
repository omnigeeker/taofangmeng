<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午10:23
 * To change this template use File | Settings | File Templates.
 */

abstract class AssetProfile {

    protected $me = NULL;

    public function CreateFromValue(&$value)
    {
        $this->me = &$value;
    }

    abstract public function GetType();
    abstract public function GetName();
    abstract public function GetDiscribe();

    abstract public function GetCashFlow();
    abstract public function GetMonthIncome();
    abstract public function GetMonthOverlay();

    abstract public function GetAssetValue();
    abstract public function GetRealValue();
    /** 获得突然卖掉房子获得的现金 */
    abstract public function GetSellMoney($money);

    abstract public function GetFirstPayment();
    abstract public function GetLiabilities();

    abstract public function GetAllReceived();
    abstract public function GetAllPaid();

    abstract public function AdjustPriceByRate($ratePrice, $rateRent);
    abstract public function AdjustPriceByMonth();

    abstract public function NextStep($nowStep);
    abstract public function RepayAheadOfTime($nowStep, $money);

    /** 获得毛利 */
    abstract public function GetBuyTime();
    abstract public function GetSimpleString();
    abstract public function GetDetailString();

    public function Get()
    {
        return $this->me;
    }

    public function EstimateSale($money)
    {
        if (!$money)
            $money = $this->GetAssetValue();

        $restMoney = $money - $this->GetLiabilities();
        $allReceive = $restMoney + $this->GetAllReceived();
        $allPaid = $this->GetAllPaid();
        $delta = $allReceive - $allPaid;
        $first = $this->GetFirstPayment();
        $rateOfReturn = (int)(100 * $delta / $first);
        return array(
            "rest_money" => $restMoney,
            "all_receive" => $allReceive,
            "all_paid" => $allPaid,
            "delta" => $delta,
            "first" => $first,
            "rate_of_return" => $rateOfReturn,
        );
    }

    public function GetEstimateSaleString($money)
    {
        $result = $this->EstimateSale($money);
        $ret = $this->GetType().": ".$this->GetName()."\n".
            "出售后:\n".
            "一次进帐：".$result["rest_money"]."\n".
            "总收益：".$result["delta"]."\n".
            "总首付：".$result["first"]."\n".
            "投资回报率:".$result["rate_of_return"]."%\n";
        return $ret;
    }

    public function GetSaleRestMoney($money)
    {
        if (!$money)
            $money = $this->GetAssetValue();
        $restMoney = $money - $this->GetLiabilities();
        return $restMoney();
    }

    public function GetGrossProfit($money)
    {
        if (!$money)
            $money = $this->GetAssetValue();
        $ret = $money - $this->GetLiabilities()
            + $this->GetAllReceived() - $this->GetAllPaid();
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