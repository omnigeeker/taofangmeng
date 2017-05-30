<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午4:07
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class AssetsListState extends State {

    // assetslist
    public function DoGotoState() {
        Log::info("AssetsListState::DoGotoState");

        $u = $this->u;
        $houses = $u->GetRealEstates();

        $this->Output("请选择你要处理的资产：");
        $this->Output("[梦梦提示]急售房子一般不能卖到好的价钱");
        foreach($houses as $num => $house)
        {
            $this->Output(Config::get("info.common.split0"));
            $houseProfile = new HouseProfile();
            $houseProfile->CreateFromValue($house);
            $this->Output("回复【".$num."】处理：");
            $this->Output("急售只能卖到: ".$houseProfile->now["price"]);
            $this->Output($houseProfile->GetSimpleString());
        }
        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("AssetsListState::DoOnState $input");

        $u = $this->u;
        $houses = $u->GetRealEstates();
        if (empty($houses[$input]))
        {
            $this->Output("没有找到该房产，请重新输入");
            return false;
        }

        $u->SetFocusHandleRealEstate($input);
        $this->m->GotoState('AssetsHandle');
        return true;
    }

}