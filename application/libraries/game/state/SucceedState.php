<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-10
 * Time: 下午3:55
 * To change this template use File | Settings | File Templates.
 */

class SucceedState extends State {

    public function DoGotoState() {
        Log::info("SucceedState::DoGotoState");

        $u = $this->u;
        $str = "通关档案:\n";
        $str .= "你的角色: ".$u->profession."\n";
        $str .= "你的城市: ".$u->city["name"]."\n";
        $str .= "启始工资: ".$u->initial["salary"]."\n";
        $str .= "启始储蓄: ".$u->initial["cash"]."\n";
        $year = floor($u->step / 12);
        $str .= "你经过了 $year 年的努力"."\n";
        $str .= "你现在".$u->GetAge()."岁"."\n";
        $str .= "月现金流:".$u->GetCashFlow()."\n";
        $str .= "不动产数目:".$u->GetRealEstateCount()."\n";
        $str .= "总资产:".$u->assets."\n";
        $str .= "现金:".$u->GetCash()."\n";

        $victorProfile = new VictorProfile();
        $victorProfile->username = $this->m->GetUserName();
        $victorProfile->guid = $u->guid;
        $victorProfile->year = floor($u->step / 12);
        $victorProfile->detail = json_encode($u->Get());
        $victorProfile->content = $str;
        $victorProfile->Save();

        $this->Output($str);

        return false;
    }


    public function DoOnState($userName, $input) {
        Log::info("SucceedState::DoOnState");

        $u = $this->u;
        $m = $this->m;

        switch(true)
        {
            case $input === 'b':
                $u->SignSucceed();
                $this->Output("游戏还将继续，随时回复home即从头开始");
                $m->GotoState('Base');
                break;
            default:
                $this->Output("输入错误，请重新输入");
                return true;
        }

        return true;
    }
}