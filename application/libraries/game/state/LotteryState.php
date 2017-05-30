<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-10
 * Time: 下午6:12
 * To change this template use File | Settings | File Templates.
 */

class LotteryState extends State {

    public function DoGotoState() {
        Log::info("LotteryState::DoGotoState");

        $u = $this->u;

        $lottery = new Lottery($u->GetFocusLottery());
        $this->Output($lottery);

        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("LotteryState::DoOnState $input");

        $u = $this->u;
        $m = $this->m;

        $lottery = new Lottery($u->GetFocusLottery());

        if ($input === 'b')
        {
            $this->Output(Config::get("game.lottery_cannot_loan"));
            return true;
        }
        else if ($input === 'a')
        {
            if ($u->GetCash() < $lottery->first)
            {   //钱不够
                $this->Output(Config::get("game.lottery_cannot_by"));
                return true;
            }

            $u->AddCash(-$lottery->first);

            $result = rand(1, $lottery->rate);
            if ($result == 1)
            {   // 中奖
                $u->AddCash($lottery->award);
                $msg = MessageState::CreateMessage(
                    "你成功了",
                    "你获得了奖品 现金 ".$lottery->award."元");
                $u->PushMessage($msg);
            }
            else
            {   // 没中
                $msg = MessageState::CreateMessage(
                    "你失败了",
                    "你白白付出了 ".$lottery->first."元");
                $u->PushMessage($msg);
            }


            $m->GotoState('Base');
            return false;
        }
    }
}