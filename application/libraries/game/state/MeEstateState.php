<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-10
 * Time: 下午12:13
 * To change this template use File | Settings | File Templates.
 */

class MeEstateState extends State {

    public function DoGotoState() {
        Log::info("MeEstateState::DoGotoState");

        $u = $this->u;
        $realEstates = $u->GetRealEstates();

        if (count($realEstates) == 0)
        {
            $this->Output("你目前还不拥有投资性不动产");
            return false;
        }

        $this->Output("不动产信息：");
        foreach($realEstates as $num => $realEstate)
        {
            $this->Output(Config::get("info.common.split0"));
            $houseProfile = new HouseProfile();
            $houseProfile->CreateFromValue($realEstate);
            $this->Output("回复【".$num."】查看：");
            $this->Output($houseProfile->GetSimpleString());
        }
        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("MeEstateState::DoOnState $input");

        $u = $this->u;
        $houses = $u->GetRealEstates();
        if (empty($houses[$input]))
        {
            $this->Output("没有找到不动产，请重新输入");
            return true;
        }

        $houseProfile = new HouseProfile();
        $houseProfile->CreateFromValue($houses[$input]);
        $this->Output($houseProfile->GetDetailString());
        $this->Output("【q】返回");
        return false;
    }
}