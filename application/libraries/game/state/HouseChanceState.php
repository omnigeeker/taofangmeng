<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午4:06
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class HouseChanceState extends State {

    public function DoGotoState() {
        Log::info("ChanceState::DoGotoState");

        $u = $this->u;

        $focus_house = $u->GetFocusBuyingRealEstate();
        $house = new House($focus_house);

        $this->Output("你看上了性价比非常好的房子，低于市场价\n");
        $this->Output(Config::get("info.common.split"));
        if ($u->NotHasSelfHouse())
            $this->Output($house->GetFirstHouseString());
        else $this->Output($house);
        $this->Output($u->BuyHouseInfo($house->Get()));

        $this->Output("回复\"meng\"梦梦提示");

        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("ChanceState::DoOnState $input");

        $u = $this->u;
        $m = $this->m;

        $focus_house = $u->GetFocusBuyingRealEstate();
        $house = new House($focus_house);

        if ($input == "meng" || $input == "梦梦") {
            $this->Output($u->MengMengBuyHouse($house->Get()));
            $this->Output("回复\"now\"继续游戏");
            return true;
        }
        else if ($input === 'b') {
            if ($u->CanBuyHouse($house))
            {
                $this->Output("现金足够，无需额外贷款购买");
                $m->GotoSameState();
                return true;
            }
            $m->GotoState('HouseChanceLoan');
            return true;
        }
        else if ($input === 'a') {
            if (!$u->CanBuyHouse($house))
            {
                $this->Output(Config::get('game.housechance_cannot_buy'));
                return true;
            }
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
                        "", Config::get("game.celebrate_".$count));
                    $u->PushMessage($msg);
                    break;
                default:
                    break;
            }
            $m->GotoState('Base');
            return false;
        }

        return false;
    }
}