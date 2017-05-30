<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-7-13
 * Time: 下午10:29
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Log;

class StockChanceState extends State {

    public function DoGotoState() {
        Log::info("StockChanceState::DoGotoState");

        $u = $this->u;

        $stock = new Stock($u->GetFocusStock());
        $this->Output("你发现了一个\"机会\"\n");
        $this->Output($stock);
        $this->Output(SPLIT0);
        $this->Output("你的现金:".$u->GetCash());
        $this->Output("回复\"meng\"梦梦提示");

        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("StockChanceState::DoOnState");

        if ($input == "meng" || $input == "梦梦") {
            $this->Output("【梦梦分析】股市变幻风云莫测，很多所谓内幕信息其实不可靠，股评家的话也不一定可信");
            $this->Output("回复\"now\"继续游戏");
            return true;
        }

        $u = $this->u;
        $m = $this->m;

        $value = (int)($input);
        if ($value == 0)
            return true;

        if ($value <= 0) {
            $this->Output("你的输入有误，清重新输入");
            return false;
        }
        if ($value > $u->GetCash()) {
            $this->Output("你的输入超出了你的现金，清重新输入".$u->GetCash());
            return false;
        }

        $stock = new Stock($u->GetFocusStock());
        $count = floor($value / ($stock->price  *100)) * 100;
        if($count == 0) {
            $this->Output("你购买的股票或者基金必须是100的整数倍，你输入的金额买不了，清重新输入");
            return false;
        }

        $u->BuyStock($stock, $count);

        $this->Output("你成功购买了股票 $count 股");
        $this->Output(SPLIT0);

        $this->m->GotoState('Base');


        return true;
    }

}