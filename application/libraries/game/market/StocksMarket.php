<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-7-13
 * Time: 下午7:38
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;

class StocksMarket {

    protected $stocks;

    //array(
    //        "name" => "NTES",
    //        "type" => "米股",
    //        "price" => 64.55,
    //        "last_price" => 64.55,
    //        "lower_rate" => 40,     //每月跌幅最大比分，涨幅与之相反
    //        "upper_rate" => 66.5,
    //        "max" => 0,
    //        "min" => 0;
    //        ),

    public function __construct(&$stocks = NULL) {
        if (!$stocks) {
            $config_stocks = Config::get("stocks");
            $this->stocks = array();
            $stocks = &$this->stocks;
            foreach($config_stocks as $stock) {
                $stocks[$stock["name"]] = $stock;
                $stocks[$stock["name"]]["upper_rate"] = round(100 * $stock["lower_rate"] / (100 - $stock["lower_rate"]));
                $stocks[$stock["name"]]["last_price"] = $stock["price"];
                $stocks[$stock["name"]]["max"] = round($stock["price"]*1.2, 2);
                $stocks[$stock["name"]]["min"] = round($stock["price"]*0.8, 2);
            }
        } else {
            $this->stocks = &$stocks;
        }
    }

    public function Get() {
        return $this->stocks;
    }

    public function NextStep($step) {
        $stocks = &$this->stocks;
        foreach($stocks as $name => &$stock) {
            $stock["last_price"] = $stock["price"];

            $rate = rand(100 - $stock["lower_rate"], 100 + $stock["upper_rate"]) / 100;

            $macroRates = StocksMarket::GetMacroRates();
            $yearStep = (int)floor($step/12);
            $stockType = $stock["type"];
            $macro = $macroRates[$stockType][$yearStep];
            $rateMacro = (100 + $macro) / 100;
            $price = $stock["price"] * $rate * $rateMacro;
            $price = number_format($price, 2);
            $stock["price"] = $price;
            if ($price > $stock["max"])
                $stock["max"] = $price;
            else if ($price < $stock["min"])
                $stock["min"] = $price;
        }
    }

    static public function GetMacroRates()
    {
        return array(
            "米股" => array(
                -3,  -4,  3, -5, -7,-10,  5, 10,  13,  0, -3,
                -3,  -4,  3, -5, -7,-10,  5, 10,  13,  0, -3,
                -3,  -4,  3, -5, -7,-10,  5, 10,  13,  0, -3,
            ),
            "A股" => array(
               -5, -3,  0,  5,  8, 10, 15, -5,-10, -5, -5,
               -5, -3,  0,  5,  8, 10, 15, -5,-10, -5, -5,
               -5, -3,  0,  5,  8, 10, 15, -5,-10, -5, -5,
            ),
            "开放式基金" => array(
               -3, -2,  0,  3,  4,  5,  7, -3, -5, -3, -2,
               -3, -2,  0,  3,  4,  5,  7, -3, -5, -3, -2,
               -3, -2,  0,  3,  4,  5,  7, -3, -5, -3, -2,
            ),
        );
    }


}