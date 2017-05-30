<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 下午8:31
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class HouseChanceLoanState extends State {

    public function DoGotoState() {
        Log::info("HouseChaneLoanState::DoGotoState");

        $u = $this->u;

        $house = new House($u->GetFocusBuyingRealEstate());

        $this->Output("你看上的房子\n");
        $this->Output(Config::get("info.common.split"));
        if ($u->NotHasSelfHouse())
            $this->Output($house->GetFirstHouseString());
        else $this->Output($house);

        // 现金分析
        $money = 0 - $u->GetBuyHouseLeftMoney($house);
        $loan = 10000 * (1+floor($money/10000));
        $months = 36;
        $monthPayment = LoanProfile::CaclMonthPayment($loan, TIANCHAO_RATE, $months);

        $u_left_cashflow = $u->GetCashFlow() + $house->GetCashFlow() - $monthPayment;

        $this->Output(Config::get("info.common.split"));
        $this->Output("还需向天朝银行无抵押贷款");
        $this->Output("额外贷款：".($loan/10000)."万");
        $this->Output("月额外还款:".$monthPayment);
        $this->Output("额外贷款期限:".$months."月");
        $this->Output(Config::get("info.common.split"));
        $this->Output("之后你剩余现金:".($loan-$money));
        $this->Output("之后你月现金流:".$u_left_cashflow);

        $this->Output("回复\"meng\"梦梦提示");


        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("HouseChaneLoanState::DoOnState $input");

        if ($input == "meng" || $input == "梦梦") {
            $this->Output("【梦梦分析】由于额外贷款利率高，且周期短，一定要注意控制你的现金流");
            $this->Output("回复\"now\"继续游戏");
            return true;
        }

        if ( ($input !== 'a')) {
            return false;
        }

        $u = $this->u;
        $m = $this->m;

        $focus_house = $u->GetFocusBuyingRealEstate();
        $house = new House($focus_house);
        $money = 0 - $u->GetBuyHouseLeftMoney($house);
        $loan = 10000 * (1+floor($money/10000));

        if ($loan > $u->GetMaxLowerLoan())
        {
            $this->Output("你的天朝银行无抵押贷款额度只有 ".$u->GetMaxLowerLoan());
            $this->Output(Config::get("game.housechanceloan_cannot_by"));
            return false;
        }

        $u->LoanLower($loan);
        $u->BuyHouse($house);

        $this->Output("你成功购买了房产：".$house->GetName());
        $this->Output(Config::get("info.common.split"));

        $count = $u->GetRealEstateCount();
        switch ($count) {
            case 0:
            case 1:
            case 4:
            case 7:
            case 10:
            case 13:
            case 16:
                $msg = MessageState::CreateMessage(
                    "恭喜你", Config::get("game.celebrate_".$count));
                $u->PushMessage($msg);
                break;
            default:
                break;
        }
        $m->GotoState('Base');
        return false;
    }
}