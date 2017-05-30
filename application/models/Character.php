<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-3
 * Time: 下午5:12
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class Character
{
//     protected static $charactor = array(
//        "profession" => "互联网产品经理",
//        "salary" => 12000,
//        "rent" => 1800,
//        "pay_live" => 1000,
//        "pay_car" => 1500,
//        "pay_kid" => 1600,
//        "pay_old" => 500,
//        "cash" => 10000000,
//        "accumulation" => 12000,
//     );

    public static function GetCharactersFromCSV()
    {
        $characters = CSVReader::GetArrayFromCSV("characters.csv");

        // !
        foreach($characters as &$character) {
            $character["unemployed_months"] = 3;
        }

        return $characters;
    }

    public static function GetGotoMessage()
    {
        $characters = Character::GetCharactersFromCSV();

        $ret = array();
        for ($i=0; $i<count($characters); $i++)
        {
            $format = "【%d】%s\n".
                "工资:%dK 储蓄:%d万";
            $c = $characters[$i];
            $str = sprintf($format, $i+1,
                $c['profession'],
                floor((int)($c["salary"])/1000),
                floor((int)($c['cash'])/10000));
            array_push($ret, $str);
        }
        return join("\n", $ret);
    }

    protected $me = NULL;

    public function IsNULL()
    {
        return !($this->me) ? true : false;
    }

    public function GetFromSelect($input)
    {
        $characters = Character::GetCharactersFromCSV();

        $index = (int)$input;
        if ($input <= 0 || $input > count($characters))
            return NULL;
        $index -= 1;
        $character = $characters[$index];

        $character["salary"] = (int)($character["salary"]);
        $character["rent"] = (int)($character["rent"]);
        $character["pay_live"] = (int)($character["pay_live"]);
        $character["pay_car"] = (int)($character["pay_car"]);
        $character["pay_kid"] = (int)($character["pay_kid"]);
        $character["pay_old"] = (int)($character["pay_old"]);

        $character["cash"] = (int)($character["cash"]);
        $character["accumulation"] = (int)($character["accumulation"]);

        $this->me = $character;
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