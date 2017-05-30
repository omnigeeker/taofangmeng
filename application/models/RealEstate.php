<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 上午9:40
 * To change this template use File | Settings | File Templates.
 */

class RealEstate extends Card {

//    public static $house = array(
//        "name" => "阳光花城(1室1厅)",
//        "type" => "房产",
//        "describe" => "阳光花城 4楼房子，户型潮南，阳光很好，有电梯，满五年且唯一",
//        "area" => 40,
//        "price" => 800000,
//        "rent" => 1500,
//        "tax_rate" => 4,             // 税率
//        "sale_rate" => 1.1,    // 正常出售价格
//        "grade" => 1,
//    );

    public function __construct($me)
    {
        parent::__construct($me);
    }

    public function GetName()
    {
        return $this->me["name"];
    }

    public function GetType()
    {
        return "不动产";
    }

    public function GetDescribe()
    {
        return $this->me["describe"];
    }

    /** 生成扩展信息
     * @param $down 首付比例
     * @param $rate 贷款利率
     * @param $years 贷款年限
     */
    public function GenerateExtInfo($down, $rate, $years)
    {
        $me = &$this->me;

        $months = $years*12;
        $downPayment = (int)($me["price"] * $down);
        $loan = (int)($me["price"] - $downPayment);
        $monthRate = $rate / 12.0;
        //月供=[本金*月利率*(1+月利率)^贷款月数]/[(1+月利率)^贷款月数-1]
        $temp = pow(1+$monthRate, $months);
        $monthPayment = (int)(($monthRate*$loan*$temp)/($temp-1));
        $tax_ratePayment = (int)($me["price"]*$me["tax_rate"]*0.01);
        $first = (int)($downPayment + $tax_ratePayment);
        $me["ext"] = array(
            "down_payment" => $downPayment,
            "left_month" => $months,
            "loan" => $loan,
            "month_payment" => $monthPayment,
            "tax_payment" => $tax_ratePayment,
            "first" => $first,
            "loan_rate" => $rate,
        );
    }

    public function IsExt()
    {
        return (!$this->me["ext"]) ? false : true;
    }

    public function GetFirstPayment()
    {
        if (!$this->IsExt())
            return 0;
        return $this->me["ext"]["first"];
    }

    public function GetRentSaleRate()
    {
        $me = &$this->me;
        return (int)($me["price"]/($me["rent"]*12));
    }

    public function GetCashFlow()
    {
        $me = &$this->me;
        return $me["rent"] - $me["ext"]["month_payment"];
    }

    public function __toString()
    {
        $me = &$this->me;
        $unitPrice = (int)($me["price"] / $me["area"]);
        $salePrice = (int)($me["price"] * 1.1);

        $ret = $this->GetType().": ".$this->GetName()."\n".
            "描述：".$this->GetDescribe()."\n".
            "总价: ".($me["price"]/10000)."万\n".
            "面积：".$me["area"]."平米\n".
            "单价：".$unitPrice."元/平米\n".
            "市场价格：".($salePrice/10000)."万\n";
        if ($this->IsExt())
        {
            $ext = &$me["ext"];
            $ret = $ret.
                Config::get("info.common.split0")."\n".
                "首期支付: ".$ext["down_payment"]."\n".
                "贷款月份: ".$ext["left_month"]."月\n".
                "抵押贷款: ".$ext["loan"]."\n".
                "税费交易费：".$ext["tax_payment"]."\n".
                "月供：".$ext["month_payment"]."\n".
                "每月能租：".$me["rent"]."\n".
                "售租比: ".$this->GetRentSaleRate()."：1\n".
                Config::get("info.common.split0")."\n".
                "最终首付: ".$ext["first"]."\n".
                "月现金流：".$this->GetCashFlow()."\n";
        }
        return $ret;
    }

    /**
     * @param $increment=array(
     *              "price" => 1.2
     *              "rent" => 1.4
     *          )
     */
    public function AdjustPrice($increment, $city)
    {
        $me = &$this->me;

        $price = (int)($me["price"] * $increment["price"] * $city["rate"]);
        $rent = (int)($me["rent"] * $increment["rent"] * $city["rate"]);
        $price = 1000 * round($price / 1000.0);
        $rent = 100 * round($rent / 100.0);
        if ($rent == 0) $rent = 100;
        $me["price"] = $price;
        $me["rent"] = $rent;
    }
}