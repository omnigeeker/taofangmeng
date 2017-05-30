<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-1
 * Time: 下午8:42
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;

abstract class LinearMachine extends BaseMachine
{
    /**
     * @var
     */
    protected $stateArray;      // 依次写出 顺序执行每个状态的名称

    /**
     * @var 用于记录状态描述
     * 例如
     * array（
     *  "s0" => array(
     *      "mode" => "choise",     // 选择模式
     *      “message” => "",        // 提示的一一段话
     *      “in" => array('0','1','2')  // 供选择的内容
     *      ”input_error“ => ”“,    // 输入的内容不再里面的提示
     *      “especials" => array(
     *          ”9“ => "first",
     *          "0" => "home"
     *          "1" => "goto.sss"   // 强制转向到状态sss
     *      )
     *   )
     *  "s1" => array(
     *      "mode" => "uint",       // 整数模式
     *      “message” => "",        // 提示的一一段话
     *      ”input_error“ => ”“,    // 输入错误的提示
     *      “min" => 1,             // 最小值
     *      "input_min_error" => "" // 输入小于了最小值，提示错误
     *      "max" => 100,           // 最大值
     *      "input_max_error" => "" // 输入大于了最小值，提示错误
     *  )
     * "s9" => array(
     *      "mode" => "none",       // 什么都没有
     *      “message” => "",        // 提示的一一段话
     *  )
     * );
     */
    protected $stateInfos;          // 写出所有的状态的值

    /** 用于记录每个状态上一个状态是干什么的
     * @var
     */
    protected $backStates;

    /** 用于记录每个状态下一个状态是干什么的
     * @var
     */
    protected $nextStates;

    protected $stopState;

    public function __construct($machineName, $startState)
    {
        parent::__construct($machineName, $startState);
    }

    public function __call($name, $argument)
    {
        Log::info("Call _call $name, not implememt");
        return false;
    }

    public function InitStates($stateArray, $stateInfos)
    {
        Log::info("LinearMachine begin InitStates");

        assert(count($stateArray) > 0);
        assert(count($stateArray) <= count($stateInfos));

        foreach($stateArray as $state)
        {
           //Log::info("InitStates state=".$state);
           assert(isset($stateInfos[$state]));
        }
        $this->stateArray = $stateArray;
        $this->stateInfos = $stateInfos;
        $this->startState = $stateArray[0];
        $length = count($stateArray);

        $backStates[$stateArray[0]] = $stateArray[0];
        for ($i = 1; $i < $length; $i++)
        {
            $backStates[$stateArray[$i]] = $stateArray[$i-1];
        }

        for ($i = 0; $i < $length-1; $i++)
        {
            $nextStates[$stateArray[$i]] = $stateArray[$i+1];
        }
        $nextStates[$stateArray[$length-1]] = $stateArray[$length-1];

        foreach ($stateInfos as $state => $stateinfo)
        {
            if (isset($stateinfo["back"]))
                $backStates[$state] = $stateinfo["back"];
            if (isset($stateinfo["next"]))
                $nextStates[$state] = $stateinfo["next"];
        }

        $this->backStates = $backStates;
        $this->nextStates = $nextStates;

        $this->stopState = $stateArray[$length-1];
    }

    protected function DoOnState($userName, $input)
    {
        $state = $this->state;

        if ($input == "home")
        {
            $this->GotoState($this->startState);
            $this->End();
            return;
        }
        else if ($input == "first")
        {
            $this->GotoState($this->startState);
            return;
        }

        $funcDoOnState = "DoOnState_".$state;
        if (true == $this->$funcDoOnState($input))
        {
            return;
        }

        assert(isset($this->stateInfos[$state]));

        $stateInfo = $this->stateInfos[$state];

        // 特殊输入
        if (isset($stateInfo["especials"][$input]))
        {
            $keyword = $stateInfo["especials"][$input];
            switch($keyword) {
                case "back":
                case "home":
                case "first":
                    $input = $stateInfo["especials"][$input];
                    $this->DoOnState($userName, $input);
                    return;
                default:
                    list($cmd,$newState) = explode(".", $keyword);
                    if($cmd == "goto")
                    {
                        $this->detail[$this->state] = $input;
                        $this->GotoState($newState);
                    }
                    return;
            }
        }


        if ($input == "back")
        {
            if (empty($this->backStates[$state]))
            {
                $this->Output(Config::get("info.common.noback"));
                return;
            }
            $this->GotoState($this->backStates[$state]);
            return;
        }

        if ($stateInfo["mode"] == "none")
        {
        }
        else if ($stateInfo["mode"] == 'choise')
        {
            if (!in_array($input, $stateInfo['in']))
            {
                $this->Output($stateInfo["input_error"]);
                $this->GotoSameState();
                return;
            }

            $this->detail[$this->state] = $input;
        }
        else if ($stateInfo["mode"] == "uint")
        {
            $value = (int)($input);
            if ($value == 0)
            {
                $this->Output($stateInfo["input_error"]);
                $this->GotoSameState();
                return;
            }
            if (isset($stateInfo["min"]) && $value < $stateInfo["min"])
            {
                $this->Output($stateInfo["input_min_error"]);
                $this->GotoSameState();
                return;
            }
            if (isset($stateInfo["max"]) && $value > $stateInfo["max"])
            {
                $this->Output($stateInfo["input_max_error"]);
                $this->GotoSameState();
                return;
            }
            $this->detail[$this->state] = $value;
        }
        else
        {
            $this->Output("!!!Error!!! no linear state mode");
            return;
        }

        if ($state == $this->stopState)
        {
            $this->GotoSameState();
            return;
        }

        if (empty($this->nextStates[$state]))
        {
            Log::info(count($this->nextStates));
            Log::info($this->nextStates[$state]);
            $this->Output("!!!Error!!! no next state, state=".$state);
            return;
        }
        $this->GotoState($this->nextStates[$state]);
    }

    protected function DoGotoState($state)
    {
        $funcDoGotoState = "DoGotoState_".$state;
        if (true == $this->$funcDoGotoState())
        {
            return;
        }

        if (isset($this->stateInfos[$state]['message']))
        {
            $this->Output($this->stateInfos[$state]['message']);
        }
        else
            $this->Output("!!!ERROR!!! no output message in state = ".$state);
    }
}