<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 上午10:45
 * To change this template use File | Settings | File Templates.
 */

class ShopChanceState extends State {

    public function DoGotoState() {
        Log::info("ShopChanceState::DoGotoState");

        $u = $this->u;

        $focus_shop = $u->GetFocusBuyingRealEstate();
        $shop = new Shop($focus_shop);

        $this->Output("你看上了低于市场价的不动产\n");
        $this->Output(Config::get("info.common.split"));

        $this->Output($shop);
        $this->Output($u->BuyShopInfo($shop->Get()));

        $this->Output("回复\"meng\"梦梦提示");

        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("ShopChanceState::DoOnState $input");

        $u = $this->u;
        $m = $this->m;

        $focus_shop = $u->GetFocusBuyingRealEstate();
        $shop = new Shop($focus_shop);

        if ($input == "meng" || $input == "梦梦") {
            $this->Output($u->MengMengBuyShop($shop->Get()));
            $this->Output("回复\"now\"继续游戏");
            return true;
        }
        else if ($input === 'b') {
            if ($u->CanBuyRealEstate($shop))
            {
                $this->Output("现金足够，无需额外贷款购买");
                $m->GotoSameState();
                return true;
            }
            $m->GotoState('ShopChanceLoan');
            return true;
        }
        else if ($input === 'a') {
            if (!$u->CanBuyRealEstate($shop))
            {
                $this->Output(Config::get('game.shopchance_cannot_buy'));
                return true;
            }
            $u->BuyShop($shop);

            $this->Output("你成功购买商铺：".$shop->GetName());
            $this->Output(Config::get("info.common.split"));

            $m->GotoState('Base');

            return false;
        }
    }
}