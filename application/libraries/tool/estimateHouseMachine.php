<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-1
 * Time: 下午9:53
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;

class EstimateHouseMachine extends LinearMachine
{
    public function __construct()
    {
        parent::__construct("estimate", "none");

        $stateArray = array(
            'none',
            'price',
            'age',
            'rooms',
            'floor',
            'orientation',
            'edu',
            'elevator',
            'village',
            'management',
            'area',
            'finish',
        );

        $stateInfos = array(
            'none' => array(
                "mode" => "choise",
                "message" => Config::get("tool.estimate.none"),
                "in" => array('1','9'),
                "input_error" => Config::get("tool.estimate.error"),
                "especials" => array(
                    "9" => "home",
                )
            ),
            'price' => array(
                "mode" => "uint",
                "min" => 500,
                "input_min_error" => Config::get("tool.estimate.error_1"),
                "max" => 100000,
                "input_max_error" => Config::get("tool.estimate.error_2"),
            ),
            'age' => array(
                "mode" => "uint",
                "max" => 50,
                "input_max_error" => Config::get("tool.estimate.error_3"),
            ),
            'rooms' => array(
                "mode" => "choise",
                "in" => array('0','1','2'),
            ),
            'floor' => array(
                "mode" => "choise",
                "in" => array('0','1','2','3','4'),
            ),
            'orientation' => array(
                "mode" => "choise",
                "in" => array('0','1','2'),
            ),
            'edu' => array(
                "mode" => "choise",
                "in" => array('0','1'),
            ),
            'elevator' => array(
                "mode" => "choise",
                "in" => array('0','1'),
            ),
            'village' => array(
                "mode" => "choise",
                "in" => array('0','1','2'),
            ),
            'management' => array(
                "mode" => "choise",
                "in" => array('0','1','2'),
            ),
            'area' => array(
                "mode" => "uint",
                "min" => 30,
                "input_min_error" => Config::get("tool.estimate.error_4"),
                "max" => 300,
                "input_max_error" => Config::get("tool.estimate.error_5"),
            ),
            'finish' => array(
                "mode" => "none",
                "especials" => array(
                    "1" => "first",
                    "9" => "home",
                )
            ),
        );

        //赋值 "message" 和 "input_error"
        foreach($stateInfos as $state => $stateInfo)
        {
            assert(isset($stateInfo["mode"]));
            $stateInfos[$state]["message"] = Config::get("tool.estimate.$state");
            $stateInfos[$state]["input_error"] = Config::get("tool.estimate.error");
        }

        $this->InitStates($stateArray, $stateInfos);
    }

    protected function DoGotoState_finish()
    {
        Log::info("EstimateHouseMachine DoGotoFinalState");

        $detail = $this->detail;
        // 计算结果
        $factor = 1.0;
        $factor = $factor - 0.02 * $detail['age'];

        switch($detail['rooms']) {
            case '1':
            case '2': $factor -= 0.1; break;
            default: break;
        }

        switch($detail['floor']) {
            case '1': $factor -= 0.06; break;
            case '2': $factor -= 0.03; break;
            case '3':  $factor -= 0.05; break;
            case '4':  $factor -= 0.02; break;
            default: break;
        }

        switch($detail['orientation']) {
            case '1': $factor -= 0.08; break;
            case '2': $factor += 0.05; break;
            default: break;
        }

        switch($detail['edu']) {
            case '1': $factor += 0.15; break;
            default: break;
        }

        switch($detail['elevator']) {
            case '1': $factor += 0.03; break;
            default: break;
        }

        switch($detail['village']) {
            case '1': $factor -= 0.08; break;
            case '2': $factor += 0.03; break;
            default: break;
        }

        switch($detail['management']) {
            case '1': $factor -= 0.08; break;
            case '2': $factor += 0.05; break;
            default: break;
        }

        $factor -= 0.1;

        $otherPrice = $detail['price'];
        $price = (int)($otherPrice * $factor);
        $total = $price * $detail['area'];

        $resultStr = sprintf(Config::get("tool.estimate.finish_str"), $price, $otherPrice, $total);
        $this->Output($resultStr);

        return false;
    }
}