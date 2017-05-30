<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-12
 * Time: 上午11:06
 * To change this template use File | Settings | File Templates.
 */

class LicaiChanceState extends State {

    public function DoGotoState() {
        Log::info("LicaiChanceState::DoGotoState");

        $u = $this->u;

        $licai = new Licai($u->GetFocusLicai());
        $this->Output($licai);

        $this->Output("你的现金:".$u->GetCash());
        if ($u->GetCash() < 0 ) {
            $this->Output("你没有钱了，不能购买");
        } else {
            $join = floor($u->GetCash() / 10000);
        }

        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("LicaiChanceState::DoOnState $input");

        $u = $this->u;
        $m = $this->m;

        $licai = new Licai($u->GetFocusLicai());

        $value = (int)($input);
        if ($value <= 0)
        {
            $this->Output("你的输入有误，清重新输入");
            return true;
        }

        $max_wan = floor($u->GetCash() / 10000);
        $min_wan = floor($licai->first / 10000);
        if ($value > $max_wan)
        {
            $this->Output("你的输入超出了你能购买的最大限额");
            return false;
        }
        if ($value < $min_wan)
        {
            $this->Output("你的输入少于理财产品的最低要求", $max_wan);
            return false;
        }

        $u->BuyLicai($licai, $value*10000);
        $this->Output("你成功购买了理财产品 $value 万");

        $m->GotoState('Base');
    }

}