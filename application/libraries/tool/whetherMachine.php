<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-2
 * Time: 下午5:21
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;

class WhetherMachine extends LinearMachine
{
    public function __construct()
    {
        parent::__construct("whether", "none");

        $stateArray = array(
            'none',
            'salary',
            'age',
            'accumulation',
            'rent',
            'result1',
            'advanced',
            'result2',
        );

        $stateInfos = array(
            'none' => array(
                "mode" => "choise",
                "in" => array('1','9'),
                "especials" => array(
                    "9" => "home",
                )
            ),
            'salary' => array(
                "mode" => "uint",
                "min" => 3000,
                "input_min_error" => Config::get("tool.whether.salary_error_1"),
                "max" => 100000,
                "input_max_error" => Config::get("tool.whether.salary_error_2"),
            ),
            'age' => array(
                "mode" => "choise",
                "in" => array('1','2','3','4'),
            ),
            'accumulation' => array(
                "mode" => "choise",
                "in" => array('1','2'),
                "especials" => array(
                    "2" => "goto.accumulation1",
                ),
            ),
            'accumulation1' => array(
                "mode" => "uint",
                "max" => 15000,
                "input_max_error" => Config::get("tool.whether.salary_error_2"),

                "back" => "accumulation",
                "next" => "rent",
            ),
            'rent' => array(
                "mode" => "uint",
                "max" => 20000,
                "input_max_error" => Config::get("tool.whether.rent_error_2"),
            ),
            'result1' => array(
                "mode" => "choise",
                "in" => array('1','2','9'),
                "especials" => array(
                    "1" => "first",
                    "9" => "home",
                ),
            ),

            'advanced' => array(
                "mode" => "uint",
                "min" => 3000,
                "input_min_error" => Config::get("tool.whether.salary_error_1"),
                "max" => 100000,
                "input_max_error" => Config::get("tool.whether.salary_error_2"),
            ),
            'result2' => array(
                "mode" => "choise",
                "in" => array('1','9'),
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
            $stateInfos[$state]["message"] = Config::get("tool.whether.".$state);
            $stateInfos[$state]["input_error"] = Config::get("tool.whether.error");
        }

        $this->InitStates($stateArray, $stateInfos);
    }

    protected  function DoGotoState_accumulation()
    {
        Log::info("WhetherMachine DoGotoState_accumulation");

        $detail = $this->detail;

        $accumulation = (int)($detail["salary"] * 0.07);

        $resultStr = sprintf(Config::get("tool.whether.accumulation_str"),
            $accumulation, $accumulation*2);
        $this->Output($resultStr);
    }

    protected function CalcPayment($monthPayment, $years)
    {
        $months = $years * 12;

        $rate = 0.0705;
        $rate *= 0.85;
        $monthRate = $rate / 12.0;

        //月供=[本金*月利率*(1+月利率)^贷款月数]/[(1+月利率)^贷款月数-1]
        //本金= 月供*[(1+月利率)^贷款月数-1]/[月利率*(1+月利率)^贷款月数]
        $temp = pow(1.0 + $monthRate, $months);
        $loan = (int)( ($monthPayment *($temp-1)) / ($monthRate*$temp));
        $totalPrice = (int)($loan / 0.7);
        $payment =  (int)($totalPrice * 0.3);

        return array(
            "totalPrice" => $totalPrice,
            "payment" => $payment,
            "loan" => $loan,
        );
    }

    protected function DoGotoState_result1()
    {
        Log::info("WhetherMachine DoGotoState_result1");

        $detail = $this->detail;

        $accumulation = 0;
        if ($detail["accumulation"] == '2')
            $accumulation = (int)$detail["accumulation1"];
        else
            $accumulation = (int)($detail["salary"] * 0.07);

        $monthPayment = $detail["rent"] + 2*$accumulation;

        $years = 10;
        switch($detail["age"])
        {
            case '1': $years = 30; break;
            case '2': $years = 20; break;
            case '3': $years = 10; break;
            case '4': $years = 5; break;
            default: break;
        }

        $result = $this->CalcPayment($monthPayment, $years);

        $resultStr = sprintf(Config::get("tool.whether.result1_str"),
            $monthPayment, $result['payment'], $result['totalPrice'], $years);
        $this->Output($resultStr);



        return false;
    }

    protected function DoGotoState_result2()
    {
        Log::info("WhetherMachine DoGotoState_result2");

        $detail = $this->detail;

        $accumulation = 0;
        if ($detail["accumulation"] == '2')
            $accumulation = (int)$detail["accumulation1"];
        else
            $accumulation = (int)($detail["salary"] * 0.07);

        $monthPaymentSolid = $detail["rent"] + 2*$accumulation;
        $monthPaymentOther = (int)($detail["advanced"] / 3);
        $monthPayment = $monthPaymentSolid + $monthPaymentOther;

        $years = 10;
        switch($detail["age"])
        {
            case '1': $years = 30; break;
            case '2': $years = 20; break;
            case '3': $years = 10; break;
            case '4': $years = 5; break;
            default: break;
        }

        $result = $this->CalcPayment($monthPayment, $years);

        $resultStr = sprintf(Config::get("tool.whether.result2_str"),
            $monthPaymentOther, $monthPaymentSolid,
            $monthPayment, $result['payment'], $result['totalPrice'], $years);
        $this->Output($resultStr);

        return false;
    }
}