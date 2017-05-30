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

class AssetsHandleState extends State {

    // assethandle
    public function DoGotoState() {
        Log::info("AssetsHandleState::DoGotoState");

        $u = $this->u;

        $handle_assets_num = $u->GetFocusHandleRealEstate();
        $houses = $u->GetRealEstates();
        $house = $houses[$handle_assets_num];
        $houseProfile = new HouseProfile();
        $houseProfile->CreateFromValue($house);

        $u->SetFocusAssets($handle_assets_num);

        $this->Output("你要对你的资产做什么?");
        $this->Output("$houseProfile");
        $this->Output(Config::get("info.common.split0"));
        $this->Output("由于是紧急售，你只能卖到这个价钱 ".$houseProfile->now["price"]);
        $this->Output("分析出售参数");
        $this->Output($houseProfile->GetEstimateSaleString(NULL));

        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("AssetsHandleState::DoOnState $input");

        $u = $this->u;
        $m = $this->m;

        $handle_assets_num = $u->GetFocusAssets();
        Log::info("handle_assets_num = $handle_assets_num");

        switch ($input)
        {
            case 'a':
                $houses = $u->GetRealEstates();
                $this->Output("你低价贱卖了房产：".$houses[$handle_assets_num]["name"]);
                $this->Output(Config::get("info.common.split"));
                $u->SaleRealEstate($handle_assets_num, NULL);
                $m->GotoState('Base');
                break;
            //case '2':
            //    $this->GotoState('AssetsRefund');
            //    break;
            default:
                $this->Output("你的输入有误，请重新输入");
                return false;
                break;
        }

        return true;
    }

}
