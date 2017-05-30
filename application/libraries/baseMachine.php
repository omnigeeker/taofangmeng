<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-1
 * Time: 下午8:10
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Redis;

/**
 * Class BaseMachine
 * 总体状态机
 * 用于纪录整体情况
 */
abstract class BaseMachine
{
    protected $redis;
    protected $result;
    protected $isEnded;

    protected $state;       // 当前状态
    protected $detail;      // 当前详细信息

    protected $userName;
    protected $key;
    protected $detail_key;

    protected $startState;
    protected $machineName;

    public function __construct($machineName, $startState)
    {
        $this->redis = Redis::db();
        $this->result = "";
        $this->isEnded = false;
        $this->detail = array();

        $this->machineName = $machineName;
        $this->startState = $startState;
    }

    protected  function SetRedisKeyValue($key, $value)
    {
        $timeout = 4*60*60;
        $this->redis->set($key, $value);
        //$this->redis->expire($key, $timeout);
    }

    public function Output($str)
    {
        $str = (string)$str;
        if (strlen($str) > 1 && $str[strlen($str)-1] == "\n")
            $str = substr($str, 0, strlen($str)-1);

        if ($this->result == "")
            $this->result .= $str;
        else $this->result .= "\n".$str;
    }

    public function GetResult()
    {
        if ($this->result != '')
            return $this->result;
        return '回复错误，请重新回复';
    }

    public function IsEnded()
    {
        return $this->isEnded;
    }

    public function End()
    {
        $this->isEnded = true;
    }

    public function SetUserName($userName)
    {
        $this->userName = $userName;
        $this->key = 'tfm:'.$this->machineName.':'.$userName;
        $this->detail_key = 'tfm:'.$this->machineName.':detail:'.$userName;
    }

    public function GetUserName() {
        return $this->userName;
    }

    protected function DoOnState($userName, $input)
    {

    }

    public function OnState($name, $input)
    {
        $this->SetUserName($name);

        $state = $this->redis->get($this->key);
        if (!$state)
            $state = $this->startState;

        $this->state = $state;
        Log::info( "Machine ".$this->machineName." Do State = ".$state." Input = ".$input);

        $detail = $this->redis->get($this->detail_key);
        if ($detail)
            $this->detail = json_decode($detail, true);
        else
            $this->detail = array();

        $this->DoOnState($name, strtolower($input));
    }

    protected function DoGotoState($state)
    {
        //
    }

    public  function GotoState($state)
    {
        Log::info("GotoState $state");

        if ($this->state != $state)
        {
            $this->state = $state;
            $this->SetRedisKeyValue($this->key, $state);
        }

        $this->DoGotoState($state);

        if ($state == $this->startState)
        {
            $this->SetRedisKeyValue($this->detail_key, json_encode(array()));
        }
        else
        {
            $this->SetRedisKeyValue($this->detail_key, json_encode($this->detail));
        }
    }

    public function GotoSameState()
    {
        $this->GotoState($this->state);
    }

    public function GotoStartState()
    {
        $this->GotoState($this->startState);
    }

    public function __get($varName)
    {
        if (!isset($this->$varName))
            return NULL;

        return $this->$varName;
    }
}