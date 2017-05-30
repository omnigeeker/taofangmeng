<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 上午10:46
 * To change this template use File | Settings | File Templates.
 */

class ShopBaseState  extends State {

    public function DoOnState($userName, $input) {
        Log::info("ShopBaseState::DoOnState");

        $u = $this->u;
        $m = $this->m;

        $grade = 0;
        switch ($input) {
            case 'a': $grade = 1; break;
            case 'b': $grade = 2; break;
            case 'c': $grade = 3; break;
            default: break;
        }

        if ($grade == 0)
        {
            $this->Output("输入错误，请重新输入");
            return true;
        }

        $u->HasUseChance();

        $shop = NULL;
        for ($i = 0; $i < 3; $i ++)
        {
            $shop = Shop::GetRandomShop($grade, $u->city);
            //$shop->AdjustPrice($u->increment, $u->city);
            $shop->GenerateExtInfo(SHOP_DOWN_RATE, SHOP_LOAN_RATE, $u->GetLoanYears("shop"));
            if ($shop->GetFirstPayment() < $u->GetCash() )
            {   // 如果首付现金足够，就不需要再随机了
                break;
            }
        }
        $u->SetFocusBuyingRealEstate($shop->Get());
        $m->GotoState('ShopChance');

        return true;
    }
}