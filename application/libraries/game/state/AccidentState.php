<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午4:07
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class AccidentState extends State {

    // accident(
    public function DoGotoState() {
        Log::info("AccidentState::DoGotoState");

        $u = $this->u;

        $accident = new Accident($u->GetFocusAccident());

        $this->Output($accident);
        $this->Output(SPLIT);
        $mode = $accident->GetMode();
        switch($mode)
        {
            case "normal":
            case "cashflow":
            case "result":
                $this->Output(Config::get("game.accident_0"));
                break;
            case "credit_card":
                $this->Output(Config::get("game.accident_1"));
                break;
            default:
                assert(false);
        }

        return true;
    }

    public function DoOnState($userName, $input) {
        Log::info("AccidentState::DoOnState $input");

        $u = $this->u;
        $m = $this->m;

        $accident = new Accident($u->GetFocusAccident());

        $mode = $accident->GetMode();
        switch ($mode) {
            case "normal":
                if ($input !== 'a')
                {
                    $this->Output(Config::get("game.accident_other"));
                    $this->Output(Config::get('game.accident_0'));
                    return true;
                }
                $u->AddCash(-$accident->GetFirst());
                $u->RecordAccident($accident);
                break;
            case "credit_card":
                if ($input == 'a') {
                    $u->AddCash(-$accident->GetFirst());
                    $u->RecordAccident($accident);
                } else if ($input == 'b') {
                    $u->LoanCredit($accident->GetFirst());
                    $u->AddCash(-$accident->GetFirst());
                    $u->RecordAccident($accident);
                } else {
                    $this->Output(Config::get("game.accident_other"));
                    $this->Output(Config::get('game.accident_1'));
                    return true;
                }
                break;
            case "cashflow":
                if ($input !== 'a')
                {
                    $this->Output(Config::get("game.accident_other"));
                    $this->Output(Config::get('game.accident_0'));
                    return true;
                }
                $u->MeetCashFlowAccident($accident);
                $u->RecordAccident($accident);
                break;
            case "result":
                if ($input !== 'a')
                {
                    $this->Output(Config::get("game.accident_other"));
                    $this->Output(Config::get('game.accident_0'));
                    return true;
                }
                $u->MeetResultAccident($accident);
                $u->RecordAccident($accident);
                break;
            default:
                assert(false);
        }
        $m->GotoState('Base');
        return false;
    }

}