<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-7-27
 * Time: 下午3:23
 * To change this template use File | Settings | File Templates.
 */

class Macro {

    protected $me;

    public function __construct() {
        $this->me = CSVReader::GetYearsMapFromCSV("macro_years.csv");

        assert(array_key_exists("inc_house_price",$this->me));
        assert(array_key_exists("inc_house_rent",$this->me));
        assert(array_key_exists("inc_shop_price",$this->me));
        assert(array_key_exists("inc_shop_rent",$this->me));
        assert(array_key_exists("inc_character_salary",$this->me));
        assert(array_key_exists("inc_character_pay",$this->me));
        assert(array_key_exists("CPI",$this->me));
    }

    public function Get() {
        return $this->me;
    }

    public function GetTemplateInc($base_key, $csv_name, $key, $year)
    {
        $map = CSVReader::GetYearsMapFromCSV($csv_name);
        if (!array_key_exists($key, $map))
            return 0.0;
        if ($year < 0 || $year >= 30)
            return 0.0;
        $percent = $this->me[$base_key][$year]*$map[$key][$year];
        return $percent/100;
    }

    /*
     * Character
     */

    public function GetCharacterPayInc($key, $year)
    {
        return $this->GetTemplateInc(
            "inc_character_pay", "character_pay_inc.csv", $key, $year);
    }

    public function GetCharacterSalaryInc($key, $year)
    {
        return $this->GetTemplateInc(
            "inc_character_salary", "character_salary_inc.csv", $key, $year);
    }

    /*
     * Character
     */


    public function GetHousePriceInc($city, $year)
    {
        return $this->GetTemplateInc(
            "inc_house_price", "city_house_price_inc.csv", $city, $year);
    }

    public function GetHouseRentInc($city, $year)
    {
        return $this->GetTemplateInc(
            "inc_house_rent", "city_house_rent_inc.csv", $city, $year);
    }

    public function GetShopPriceInc($city, $year)
    {
        return $this->GetTemplateInc(
            "inc_shop_price", "city_shop_price_inc.csv", $city, $year);
    }

    public function GetShopRentInc($city, $year)
    {
        return $this->GetTemplateInc(
            "inc_shop_rent", "city_shop_rent_inc.csv", $city, $year);
    }

    /*
     * CPI
     */
    public function GetCPI($year)
    {
        if ($year < 0 || $year >= 30)
            return 0.0;
        $percent = $this->me["CPI"][$year];
        return $percent/100;
    }

}