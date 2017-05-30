<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-16
 * Time: 下午4:42
 * To change this template use File | Settings | File Templates.
 */

class Certificate extends Study {


//    array(
//        "name" => "注册建筑师",
//        "months" => 6,
//        "first" => 30000,
//        "income" => 10000,
//        "succeed_rate" => 0.95,
//    ),

    public static function GetRandomCertificate()
    {
        //$certificates = Config::get("certificates");
        $certificates = CSVReader::GetArrayFromCSV("certificates.csv");
        $random = rand(0, count($certificates)-1);
        $certificate = $certificates[$random];
        return new Certificate($certificate);
    }

    public function GetType() {
        return "certificate";
    }

    public function GetName() {
        return $this->me["name"];
    }

    public function GetFirst()
    {
        return $this->me["first"];
    }

    public function GetMonths()
    {
        return $this->me["months"];
    }

    public function GetSucceedRatePrecent() {
        return 100*$this->me["succeed_rate"];
    }


    public function __toString()
    {
        $me = &$this->me;

        $ret = "证书：".$me["name"]."\n";
        $ret .= "培训考试费:".$me["first"]."\n";
        $ret .= "培训时间:".$me["months"]."个月\n";
        $ret .= "培训机构承诺\n考试通过率:".$this->GetSucceedRatePrecent()."%\n";
        $ret .= "获得证书之后\n";
        $ret .= "挂证月收入:".$me["income"]."\n";
        $ret .= "挂证年收入:".($me["income"]*12)."\n";
        $ret .= "考试很苦逼，高收入人群由于不重视，往往很难通过\n";
        return $ret;

    }
}