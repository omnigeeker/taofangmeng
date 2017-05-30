<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-7-27
 * Time: 下午4:55
 * To change this template use File | Settings | File Templates.
 */

class City {
    /*
     array(
        "key" => "city010",
        "name" => "帝都",
        "house_price" => 2.6,
        "shop_price" => 2.6,
        "house_rent" => 2.6,
        "shop_rent" => 2.6
     ),
     */
    public static function GetCitiesFromCSV()
    {
        $cities = CSVReader::GetMapFromCSV("cities.csv");
        foreach($cities as $key => &$city) {
            $city["house_price"] *= Config::get("args.house.price");
            $city["house_price"] = 100*(int)($city["house_price"]/100);
            $city["house_rent"] *= Config::get("args.house.rent");
            $city["house_rent"] = (int)$city["house_rent"];

            $city["shop_price"] *= Config::get("args.shop.price");
            $city["shop_price"] = 100*(int)($city["shop_price"]/100);
            $city["shop_rent"] *= Config::get("args.shop.rent");
            $city["shop_rent"] = (int)$city["shop_rent"];
        }
        return $cities;
    }

    protected $me = NULL;

    public function IsNULL()
    {
        return !($this->me) ? true : false;
    }

    public function GetFromSelect($input)
    {
        $cities = City::GetCitiesFromCSV();
        $key = "city".$input;
        if (empty($cities[$key]))
            return false;
        $this->me = $cities[$key];
        return true;
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
}