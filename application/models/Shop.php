<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 上午9:21
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;

class Shop extends RealEstate
{
//    public static $shops = array(
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
        return "商铺";
    }

    // 地段
    protected static $district = array(
        array(
            "text" => "中心区",
            "price" => 2,
            "rent" => 2.2,
            "inflation" => 1.0,
        ),
        array(
            "text" => "次中心",
            "price" => 1.5,
            "rent" => 1.5,
            "inflation" => 1.0,
        ),
        array(
            "text" => "城郊结合部",
            "price" => 0.8,
            "rent" => 0.6,
            "inflation" => 1.0,
        ),
        array(
            "text" => "郊区",
            "price" => 0.4,
            "rent" => 0.3,
            "inflation" => 1.0,
        ),
    );

    // 地段
    protected static $around = array(
        array(
            "text" => "地铁铺",
            "price" => 1.3,
            "rent" => 1.3,
            "inflation" => 1.0,
        ),
        array(
            "text" => "小区铺",
            "price" => 1.0,
            "rent" => 1.1,
            "inflation" => 1.0,
        ),
        array(
            "text" => "景观铺",
            "price" => 1.1,
            "rent" => 1.02,
            "inflation" => 1.0,
        ),
        array(
            "text" => "科技区铺",
            "price" => 1,
            "rent" => 0.9,
            "inflation" => 1.0,
        ),
        array(
            "text" => "CBD铺",
            "price" => 2,
            "rent" => 2.5,
            "inflation" => 1.0,
        ),
        array(
            "text" => "路边铺",
            "price" => 0.9,
            "rent" => 0.8,
            "inflation" => 1.0,
        ),
        array(
            "text" => "空铺",
            "price" => 0.6,
            "rent" => 0.2,
            "inflation" => 1.0,
        ),
    );

    protected static $names = array(
        'grade1' => array(
            array("min"=>10, "max"=>20, "text"=>"超小商铺"),
            array("min"=>20, "max"=>40, "text"=>"小商铺"),
        ),
        'grade2' => array(
            array("min"=>40, "max"=>50, "text"=>"中商铺"),
            array("min"=>50, "max"=>70, "text"=>"中商铺"),
            array("min"=>70, "max"=>100, "text"=>"中商铺"),
        ),
        'grade3' => array(
            array("min"=>100, "max"=>125, "text"=>"大商铺"),
            array("min"=>125, "max"=>150, "text"=>"大商铺"),
            array("min"=>150, "max"=>300, "text"=>"大商铺"),
        ),
    );

    public static function GetRandomShop($grade, $city)
    {
        $district_array = Shop::$district;
        $around_array = Shop::$around;

        $district = $district_array[rand(0, count($district_array)-1)];
        $around = $around_array[rand(0, count($around_array)-1)];

        $names = Shop::$names;
        if (empty($names["grade".$grade]))
            return NULL;
        $name_array = $names["grade".$grade];
        $name = $name_array[rand(0, count($name_array)-1)];
        $area = rand($name["min"],$name["max"]);

        //$start = Config::get("args.start.shop");
        $price = $area * $city["shop_price"] * $district["price"] * $around["price"];
        $rent = $area * $city["shop_rent"] * $district["rent"] * $around["rent"];
        $sale_rate = 1.2 * $district["inflation"] * $around["inflation"];
        $describe = "".$around["text"]." ".$district["text"];
        $tax_rate = rand(Config::get("args.shop.tax_min"), Config::get("args.shop.tax_max"));

        $price = 100 * (int)($price/100);
        $rent = (int)($rent);

        return new House(array(
            "name" => $name["text"],
            "type" => "商铺",
            "describe" => $describe,
            "area" => $area,
            "price" => $price,
            "rent" => $rent,
            "sale_rate" => $sale_rate,
            "tax_rate" => $tax_rate,
            "grade" => $grade,
        ));
    }
}