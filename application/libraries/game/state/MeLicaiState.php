<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-12
 * Time: 下午2:04
 * To change this template use File | Settings | File Templates.
 */

class MeLicaiState extends State {
    public function DoGotoState() {
        Log::info("MeLoanState::DoGotoState");

        $u = $this->u;

        if ($u->GetLicaiCount() == 0)
        {
            $this->Output("你当前没有理财产品");
            return false;
        }

        $loans = $u->GetLicais();
        $this->Output("你购买的理财产品：");
        foreach($loans as $num => $loan)
        {
            $this->Output(Config::get("info.common.split0"));
            $licaiProfile = new LicaiProfile();
            $licaiProfile->CreateFromValue($loan);
            $this->Output($licaiProfile);
        }
        return false;
    }
}