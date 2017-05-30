<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-4
 * Time: 上午9:56
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;

class House extends RealEstate
{
//    public static $house = array(
//        "id" => 1001,
//        "name" => "阳光花城(1室1厅)",
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

    public function GetType()
    {
        return "房产";
    }

    // 地段
    protected static $district = array(
        array(
            "text" => "中心区",
            "price" => 1.4,
            "rent" => 1.15,
            "inflation" => 1.0,
        ),
        array(
            "text" => "次中心",
            "price" => 1.1,
            "rent" => 0.7,
            "inflation" => 1.0,
        ),
        array(
            "text" => "城郊结合部",
            "price" => 0.8,
            "rent" => 0.5,
            "inflation" => 1.0,
        ),
        array(
            "text" => "郊区",
            "price" => 0.6,
            "rent" => 0.3,
            "inflation" => 1.0,
        ),
    );

    // 地段
    protected static $around = array(
        array(
            "text" => "地铁房",
            "price" => 1.1,
            "rent" => 1.15,
            "inflation" => 1.0,
        ),
        array(
            "text" => "学区房",
            "price" => 1.1,
            "rent" => 1.05,
            "inflation" => 1.0,
        ),
        array(
            "text" => "景观房",
            "price" => 1.1,
            "rent" => 1.02,
            "inflation" => 1.0,
        ),
        array(
            "text" => "科技园区房",
            "price" => 1.1,
            "rent" => 1.2,
            "inflation" => 1.0,
        ),
        array(
            "text" => "CBD区房",
            "price" => 1.1,
            "rent" => 1.2,
            "inflation" => 1.0,
        ),
        array(
            "text" => "工业区房",
            "price" => 0.8,
            "rent" => 1.05,
            "inflation" => 1.0,
        ),
    );

    //
    protected static $type = array(
        array(
            "text" => "老公房",
            "price" => 0.6,
            "rent" => 0.6,
            "inflation" => 1.0,
        ),
        array(
            "text" => "电梯公寓",
            "price" => 1.05,
            "rent" => 1.05,
            "inflation" => 1.0,
        ),
        array(
            "text" => "拆迁房",
            "price" => 0.7,
            "rent" => 0.7,
            "inflation" => 1.0,
        ),
    );

    protected static $estate = array(
        array(
            "text" => "不好",
            "price" => 0.95,
            "rent" => 0.98,
            "inflation" => 1.0,
        ),
        array(
            "text" => "一般",
            "price" => 0.98,
            "rent" => 0.99,
            "inflation" => 1.0,
        ),
        array(
            "text" => "好",
            "price" => 1.02,
            "rent" => 1.01,
            "inflation" => 1.0,
        ),
        array(
            "text" => "非常好",
            "price" => 1.05,
            "rent" => 1.02,
            "inflation" => 1.0,
        ),
    );

    protected static $names = array(
        'grade1' => array(
            array("min"=>30, "max"=>60, "text"=>"一居一厅房子"),
            array("min"=>60, "max"=>90, "text"=>"二居一厅房子"),
        ),
        'grade2' => array(
            array("min"=>90, "max"=>115, "text"=>"三居一厅大房子"),
            array("min"=>100, "max"=>125, "text"=>"三居二厅大房子"),
            array("min"=>115, "max"=>140, "text"=>"四居二室大房子"),
        ),
        'grade3' => array(
            array("min"=>140, "max"=>160, "text"=>"三居室豪宅"),
            array("min"=>160, "max"=>200, "text"=>"四居室豪宅"),
            array("min"=>200, "max"=>300, "text"=>"五居室豪宅"),
        ),
        'grade4' => array(
            array("min"=>190, "max"=>500, "text"=>"别墅"),
        )
    );


    public static function GetRandomHouse($grade, $city)
    {
        $district_array = House::$district;
        $around_array = House::$around;
        $type_array = House::$type;
        $estate_array = House::$estate;

        $district = $district_array[rand(0, count($district_array)-1)];
        $around = $around_array[rand(0, count($around_array)-1)];
        $type = $type_array[rand(0, count($type_array)-1)];
        $estate = $estate_array[rand(0, count($estate_array)-1)];

        $names = House::$names;
        if (empty($names["grade".$grade]))
            return NULL;
        $name_array = $names["grade".$grade];
        $name = $name_array[rand(0, count($name_array)-1)];
        $area = rand($name["min"],$name["max"]);

        $price = $area * $city["house_price"] * $district["price"] * $around["price"] * $type["price"] * $estate["price"];
        $rent = $area * $city["house_rent"] * $district["rent"] * $around["rent"] * $type["rent"] * $estate["rent"];
        $sale_rate = 1.2 * $district["inflation"] * $around["inflation"] * $type["inflation"] * $estate["inflation"];
        $describe = "".$around["text"]." ".$district["text"]." ".$type["text"]." 物业管理".$estate["text"];
        $tax_rate = rand(Config::get("args.house.tax_min"), Config::get("args.house.tax_max"));

        $price = 100 * (int)($price/100);
        $rent = (int)($rent);

        return new House(array(
            "name" => $name["text"],
            "type" => "房产",
            "describe" => $describe,
            "area" => $area,
            "price" => $price,
            "rent" => $rent,
            "sale_rate" => $sale_rate,
            "tax_rate" => $tax_rate,
            "grade" => $grade,
        ));
    }

    public function GetFirstHouseString()
    {
        $me = &$this->me;
        $unitPrice = (int)($me["price"] / $me["area"]);
        $salePrice = (int)($me["price"] * $me["sale_rate"]);

        $ret = $this->GetType().": ".$this->GetName()."\n".
            "描述：".$this->GetDescribe()."\n".
            "总价: ".($me["price"]/10000)."万\n".
            "面积：".$me["area"]."平米\n".
            "单价：".$unitPrice."元/平米\n".
            "可以卖到：".($salePrice/10000)."万\n";
        if ($this->IsExt())
        {
            $ext = &$me["ext"];
            $ret = $ret.
                Config::get("info.common.split0")."\n".
                "首期支付: ".$ext["down_payment"]."\n".
                "贷款周期: ".$ext["left_month"]."个月\n".
                "抵押贷款: ".$ext["loan"]."\n".
                "税费交易费：".$ext["tax_payment"]."\n".
                "月供：".$ext["month_payment"]."\n".
                Config::get("info.common.split0")."\n".
                "最终首付: ".$ext["first"]."\n";
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