<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-3
 * Time: 下午4:22
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;

abstract class GameMachine extends BaseMachine
{
    /**
     * @var 用于记录状态描述
     * 例如
     * array（
     *  "none" => array(
     *      “message” => "",        // 提示的一一段话
     *      “especials" => array(
     *          ”1“ => "",
     *          "3" => "sss"   // 强制转向到状态sss
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

    public function __construct($machineName, $startState)
    {
        parent::__construct($machineName, $startState);
    }

    protected function SetRedisKeyValue($key, $value)
    {
        // game not set expires
        $this->redis->set($key, $value);
    }

    public function __call($name, $argument)
    {
        Log::info("Call _call $name, not implememt");
        return false;
    }

    public function InitStates($stateInfos)
    {
        Log::info("GameMachine begin InitStates");
        $this->stateInfos = $stateInfos;
    }

    protected function DoOnState($userName, $input)
    {
        $state = $this->state;

        Log::info("DoOnState $state");
        assert(isset($this->stateInfos[$state]));

        $stateInfo = $this->stateInfos[$state];

        // 先看在输入状态表中是否匹配
        if (isset($stateInfo["inputs"][$input]))
        {
            $newState = $stateInfo["inputs"][$input];
            if ($newState == "home" || $newState == "新的开始")
            {
                $this->End();
                return;
            }

            if (empty($this->stateInfos[$newState]))
            {
                Log::info("GameMachine No This State ".$newState);
                $this->GotoSameState();
                return;
            }
            $this->GotoState($newState);
            return;
        }

        $className = $state.'State';
        $stateClass = new $className();
        $stateClass->SetMachine($this, $this->detail);
        if (true == $stateClass->DoOnState($userName, $input))
        {
            return;
        }

        return;
    }

    protected function DoGotoState($state)
    {
        if ($state == $this->startState)
        {
            $this->SetRedisKeyValue($this->detail_key, json_encode(array()));
        }

        $className = $state.'State';
        $stateClass = new $className();
        $stateClass->SetMachine($this, $this->detail);
        if (true == $stateClass->DoGotoState())
        {
            return;
        }

        if (isset($this->stateInfos[$state]['message']))
        {
            $this->Output(Config::get("info.common.split"));
            $this->Output($this->stateInfos[$state]['message']);
        }
        else
        {
            Log::info("!!!ERROR!!! no output message in state = ".$state);
        }
    }
}