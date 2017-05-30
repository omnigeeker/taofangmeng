<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 下午9:55
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;


class ShopChanceLoanState extends State {

    public function DoGotoState() {
        Log::info("ShopChanceLoanState::DoGotoState");

        $u = $this->u;

        $shop = new Shop($u->GetFocusBuyingRealEstate());

        $this->Output("你看上的商业地产\n");
        $this->Output(Config::get("info.common.split"));
        $this->Output($shop);

        // 现金分析
        $money = 0 - $u->GetBuyRealEstateLeftMoney($shop);
        $loan = 10000 * (1+floor($money/10000));
        $months = 36;
        $monthPayment = LoanProfile::CaclMonthPayment($loan, TIANCHAO_RATE, $months);

        $u_left_cashflow = $u->GetCashFlow() + $shop->GetCashFlow() - $monthPayment;

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
        Log::info("ShopChanceLoanState::DoOnState $input");

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

        $shop = new Shop($u->GetFocusBuyingRealEstate());
        $money = 0 - $u->GetBuyRealEstateLeftMoney($shop);
        $loan = 10000 * (1+floor($money/10000));

        if ($loan > $u->GetMaxLowerLoan())
        {
            $this->Output("你的天朝银行无抵押贷款额度只有 ".$u->GetMaxLowerLoan());
            $this->Output(Config::get("game.shopchanceloan_cannot_by"));
            return false;
        }

        $u->LoanLower($loan);
        $u->BuyShop($shop);

        $this->Output("你成功购买了商铺：".$shop->GetName());
        $this->Output(Config::get("info.common.split"));

        $m->GotoState('Base');
        return false;
    }
}