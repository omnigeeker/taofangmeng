<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-2
 * Time: 上午12:09
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class LoanMachine extends LinearMachine
{
    public function __construct()
    {
        parent::__construct("loan", "none");

        $stateArray = array(
            'none',
            'totalprice',
            'rate',
            'downpayment',
            'years',
            'result1',
            'tax',
            'intro',
            'fitment',
            'area',
            'result2',
        );

        $stateInfos = array(
            'none' => array(
                "mode" => "choise",
                //"message" => Config::get("tool.loan.none"),
                "in" => array('1','9'),
                //"input_error" => Config::get("tool.loan.error"),
                "especials" => array(
                    "9" => "home",
                )
            ),
            'totalprice' => array(
                "mode" => "uint",
                "min" => 50000,
                "input_min_error" => Config::get("tool.loan.totalPrice_error1"),
                "max" => 50000000,
                "input_max_error" => Config::get("tool.loan.totalPrice_error2"),
            ),
            'rate' => array(
                "mode" => "choise",
                "in" => array('1','2','3','4','5','6','7','8','9'),
                "especials" => array(
                    "1" => "goto.years",
                    "2" => "goto.years",
                ),
            ),
            'downpayment' => array(
                "mode" => "choise",
                "in" => array('3','4','5','6','7','8','9','0'),
                "especials" => array(
                    "0" => "goto.downpayment1",
                ),
            ),
            'downpayment1' => array(
                "mode" => "uint",
                "min" => 15000,
                "input_min_error" => Config::get("tool.loan.totalPrice_error1"),
                "max" => 50000000,
                "input_max_error" => Config::get("tool.loan.totalPrice_error2"),

                "back" => "downpayment",
                "next" => "years",
            ),
            'years' => array(
                "mode" => "choise",
                "in" => array('1','2','3','4','5','6','0'),
            ),
            'result1' => array(
                "mode" => "choise",
                "in" => array('1', '2', '9'),
                "especials" => array(
                    "2" => "first",
                    "9" => "home",
                ),
            ),

            'tax' => array(
                "mode" => "choise",
                "in" => array('1','2','3','4','5','6','7','8','9'),
                "especials" => array(
                    "1" => "goto.fitness",
                    "2" => "goto.fitness",
                    "3" => "goto.fitness",
                    "4" => "goto.fitness",
                    "9" => "goto.tax1",
                    "7" => "goto.tax2",
                    "8" => "goto.tax2",
                ),
            ),
            "tax1" => array(
                "mode" => "uint",
                "min" => 0,
                "input_min_error" => Config::get("tool.loan.totalPrice_error1"),
                "max" => 1000000,
                "input_max_error" => Config::get("tool.loan.totalPrice_error2"),

                "back" => "tax",
                "next" => "intro",
            ),
            "tax2" => array(
                "mode" => "uint",
                "min" => 0,
                "input_min_error" => Config::get("tool.loan.totalPrice_error1"),
                "max" => 5000000,
                "input_max_error" => Config::get("tool.loan.totalPrice_error2"),

                "back" => "tax",
                "next" => "intro",
            ),

            'intro' => array(
                "mode" => "choise",
                "in" => array('1','2','9'),
                "especials" => array(
                    "9" => "goto.intro1",
                ),
            ),
            "intro1" => array(
                "mode" => "uint",
                "min" => 0,
                "input_min_error" => Config::get("tool.loan.totalPrice_error1"),
                "max" => 100000,
                "input_max_error" => Config::get("tool.loan.totalPrice_error2"),

                "back" => "intro",
                "next" => "fitment",
            ),

            'fitment' => array(
                "mode" => "choise",
                "in" => array('0','1','2','3','4'),
            ),
            'area' => array(
                "mode" => "uint",
                "min" => 15,
                "input_min_error" => Config::get("tool.loan.area_error1"),
                "max" => 500,
                "input_max_error" => Config::get("tool.loan.area_error2"),
            ),

            'result2' => array(
                "mode" => "choise",
                "in" => array('1', '9'),
                "especials" => array(
                    "1" => "first",
                    "9" => "home",
                ),
            ),

        );

        //赋值 "message" 和 "input_error"
        foreach($stateInfos as $state => $stateInfo)
        {
            assert(isset($stateInfo["mode"]));
            $stateInfos[$state]["message"] = Config::get("tool.loan.".$state);
            $stateInfos[$state]["input_error"] = Config::get("tool.loan.error");
        }

        $this->InitStates($stateArray, $stateInfos);
    }

    protected $totolPrice;      // 总房价
    protected $rate;            // 实际享受利率
    protected $downPaymant;     // 首付
    protected $loan;            // 贷款
    protected $monthPayment;    // 月供
    protected $years;           // 贷款年限

    private function CaclResult1()
    {
        $detail = $this->detail;

        //基准利率
        $rate = 0.0705;
        $downpaymentRate = 1.0;

        switch($detail['rate'])
        {
            case '1': $rate *= 0.85; $downpaymentRate = 0.3; break;
            case '2': $rate *= 1.1; $downpaymentRate = 0.6; break;
            case '4': $rate *= 0.75; break;
            case '5': $rate *= 0.8; break;
            case '6': $rate *= 0.85; break;
            case '7': $rate *= 0.9; break;
            case '8': $rate *= 1.1; break;
            case '9': $rate *= 1.2; break;
            default: break;
        }
        $monthRate = $rate/12.0;

        if( isset($detail['downpayment']))
        {
            switch($detail['downpayment'])
            {
                case '3': $downpaymentRate = 0.3; break;
                case '4': $downpaymentRate = 0.4; break;
                case '5': $downpaymentRate = 0.5; break;
                case '6': $downpaymentRate = 0.6; break;
                case '7': $downpaymentRate = 0.7; break;
                case '8': $downpaymentRate = 0.8; break;
                case '9': $downpaymentRate = 0.9; break;
                default: break;
            }
        }

        $years = 30;
        switch($detail["years"])
        {
            case '1': $years = 30; break;
            case '2': $years = 20; break;
            case '3': $years = 10; break;
            case '4': $years = 5; break;
            case '5': $years = 15; break;
            case '6': $years = 25; break;
            default: break;
        }
        $months = $years*12;

        if (isset($detail['downpayment1']))
            $downPayment = $detail['downpayment1'];
        else
            $downPayment = $detail['totalprice']*$downpaymentRate;
        $loan = $detail['totalprice'] - $downPayment;

        //月供=[本金*月利率*(1+月利率)^贷款月数]/[(1+月利率)^贷款月数-1]
        // $temp = (1+月利率)^贷款月数
        $temp = pow(1+$monthRate, $months);
        $monthPayment = ($monthRate*$loan*$temp)/($temp-1);

        $downPayment = (int)$downPayment;
        $loan = (int)$loan;
        $monthPayment = (int)$monthPayment;

        $this->totolPrice = (int)$detail['totalprice'];
        $this->rate = $rate;
        $this->downPaymant = $downPayment;
        $this->loan = $loan;
        $this->monthPayment = $monthPayment;
        $this->years = $years;
    }

    protected function DoGotoState_result1()
    {
        Log::info("LoanMachine DoGotoState_result1");

        $detail = $this->detail;

        $this->CaclResult1();

        $resultStr = sprintf(Config::get("tool.loan.result1_str"),
            $this->downPaymant, $this->monthPayment, $this->loan, $this->years);
        $this->Output($resultStr);

        return false;
    }

    protected $tax;
    protected $intro;
    protected $fitment;

    private function CaclResult2()
    {
        $this->CaclResult1();

        $detail = $this->detail;
        $tax = 0;

        $qiTax = 0.03;
        $yingyetax = 0.055;
        $gerenTax = 0.01;
        $yinhuaTax = 0.0005;

        switch($detail["tax"])
        {
            case '1': $tax = $this->totolPrice * 0.01; break;
            case '2': $tax = $this->totolPrice * 0.02; break;
            case '3': $tax = $this->totolPrice * 0.03; break;
            case '4': $tax = $this->totolPrice * 0.04; break;
            case '5': $tax = $this->totolPrice * ($qiTax+$yinhuaTax); break;
            case '6': $tax = $this->totolPrice * ($qiTax+$yingyetax+$gerenTax+$yinhuaTax); break;
            case '7':
            case '8':
                $gerenTaxTotal = $detail["tax2"];
                if ($gerenTaxTotal < 0 )
                    $gerenTax = 0;
                $gerenTax = $gerenTaxTotal * 0.2;
                if ($detail["tax"] == '7')
                    $tax = $this->totolPrice * ($qiTax+$yingyetax+$gerenTax+$yinhuaTax);
                else if ($detail["tax"] == '8')
                    $tax = $this->totolPrice * ($qiTax+$gerenTax+$yinhuaTax);
                break;
            case '9':
                $tax = (int)$detail['tax1'];
                break;
            default: break;
        }

        $intro = 0;
        switch($detail["intro"])
        {
            case '1': $intro = $this->totolPrice * 0.01; break;
            case '2': $intro = $this->totolPrice * 0.02; break;
            case '9':
                $intro = (int)$detail['intro1'];
                break;
            default: break;
        }

        $fitment = 0;
        $area = $detail["area"];
        switch($detail["fitment"])
        {
            case '1': $fitment = $area * 500; break;
            case '2': $fitment = $area * 1000; break;
            case '3': $fitment = $area * 2000; break;
            case '4': $fitment = $area * 4000; break;
            default: break;
        }

        $this->tax = (int)$tax;
        $this->intro = (int)$intro;
        $this->fitment = (int)$fitment;
    }

    protected function DoGotoState_result2()
    {
        Log::info("LoanMachine DoGotoState_result2");
        $this->CaclResult2();

        $allDownPayment = $this->downPaymant + $this->tax + $this->intro + $this->fitment;
        $allTotalPrice = $this->totolPrice + $this->tax + $this->intro + $this->fitment;
        $allPay = $allDownPayment + $this->monthPayment*$this->years*12;

        $resultStr = sprintf(Config::get("tool.loan.result2_str"),
            $allDownPayment, $this->downPaymant, $this->tax, $this->intro, $this->fitment,
            $this->monthPayment, $allTotalPrice, $allPay);
        $this->Output($resultStr);

        return false;
    }
}