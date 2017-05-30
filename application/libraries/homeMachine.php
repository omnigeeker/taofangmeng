<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-1
 * Time: 下午3:32
 * To change this template use File | Settings | File Templates.
 */
use \Laravel\Redis;
use \Laravel\Config;

class HomeMachine extends  BaseMachine
{
    public function __construct()
    {
        parent::__construct("home","s0");
    }

    protected function DoOnState($userName, $input)
    {
        if (substr($input, 0, 3) == "tfm") {

            $callback = new Callback();
            $callback->username = $userName;
            $callback->content = substr($input, 3);
            $callback->save();
            $this->Output("非常感谢你给淘房梦说得话，梦梦会\"伺机而动\"喔");
            return;
        }

        switch($input) {
            case 'home':
            case '新的开始':
                $this->GotoState('none');
                return;
            case 'now':
            case '旧的回忆':
                // 检查游戏存档
                $this->DoGame($input);
                return;
            default: break;
        }

        $state = $this->state;
        switch($state) {
            case "none": $this->DoNone($input); break;
            case "game": $this->DoGame($input); break;
            default:
                $this->GotoState('none');
                break;
        }
    }

    protected function DoNone($input)
    {
        Log::info( "Do None ".$input);
        switch($input) {
            case "a": $this->GotoState('game'); break;
            default: $this->GotoState('none');
        }
    }

//    protected function DoS23($input)
//    {
//        $whetherMachine = new WhetherMachine();
//        $whetherMachine->OnState($this->userName, $input);
//        if (true === $whetherMachine->isEnded())
//        {
//            $this->GotoState('s0');
//            return;
//        }
//        $this->result = $whetherMachine->GetResult();
//    }
//
//    protected function DoS20($input)
//    {
//        switch($input)
//        {
//            case '1': $this->GotoState('s21'); break;
//            case '2': $this->GotoState('s22'); break;
//            case '3': $this->GotoState('s23'); break;
//            case '9': $this->GotoState('s0'); break;
//            default:
//                $this->GotoState('s20');
//                break;
//        }
//    }

    protected function DoGame($input)
    {
        $beginnerGameMachine = new BegginerGameMachine();
        $beginnerGameMachine->OnState($this->userName, $input);
        if (true === $beginnerGameMachine->isEnded())
        {
            $this->GotoState('none');
            return;
        }
        $this->result = $beginnerGameMachine->GetResult();
    }

//    protected function DoS21($input)
//    {
//        $estimateHouseMachine = new EstimateHouseMachine();
//        $estimateHouseMachine->OnState($this->userName, $input);
//        if (true === $estimateHouseMachine->isEnded())
//        {
//            $this->GotoState('s0');
//            return;
//        }
//
//        $this->result = $estimateHouseMachine->GetResult();
//    }
//
//    protected function DoS22($input)
//    {
//        $loanMachine = new LoanMachine();
//        $loanMachine->OnState($this->userName, $input);
//        if (true === $loanMachine->isEnded())
//        {
//            $this->GotoState('s0');
//            return;
//        }
//
//        $this->result = $loanMachine->GetResult();
//    }

    protected function DoGotoState($state)
    {
        Log::info( "Goto State .$state");
        $this->SetRedisKeyValue($this->key, $state);
        switch($state)
        {
            case 'none':
                $this->Output(Config::get("info.home.s0"));
                break;
//            case 's20':
//                $this->Output(Config::get("info.home.s20"));
//                break;
//            case 's21':
//                $estimateHouseMachine = new EstimateHouseMachine();
//                $estimateHouseMachine->SetUserName($this->userName);
//                $estimateHouseMachine->GotoStartState();
//                $this->result = $estimateHouseMachine->GetResult();
//                break;
//            case 's22':
//                $loanMachine = new LoanMachine();
//                $loanMachine->SetUserName($this->userName);
//                $loanMachine->GotoStartState();
//                $this->result = $loanMachine->GetResult();
//                break;
//            case 's23':
//                $whetherMachine = new WhetherMachine();
//                $whetherMachine->SetUserName($this->userName);
//                $whetherMachine->GotoStartState();
//                $this->result = $whetherMachine->GetResult();
//                break;
            case 'game':
                $beginnerGameMachine = new BegginerGameMachine();
                $beginnerGameMachine->SetUserName($this->userName);
                $beginnerGameMachine->GotoStartState();
                $this->result = $beginnerGameMachine->GetResult();
                break;
            default:
        }
    }
}