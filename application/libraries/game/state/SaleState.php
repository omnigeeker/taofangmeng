<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午4:08
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class SaleState extends State {
    
    // sale
    public function DoGotoState() {
        Log::info("SaleState::DoGotoState");

        $u = $this->u;

        $sale = new Sale($u->GetFocusSale());
        $this->Output($sale);

        $no = $sale->GetRealEstateNo();
        if ( $no == 0)
        {   // 没有找到对应的房产
            $this->Output("可惜你没有这样的资产，没有高价被收购的机会");

            $this->Output(Config::get("info.common.split"));
            $this->Output(Config::get("game.sale_0"));

            return true;
        }

        $this->Output(Config::get("info.common.split0"));

        if ($sale->type == "房产")
        {
            $realEstates = $u->GetRealEstates();
            $houseProfile = new HouseProfile();
            $houseProfile->CreateFromValue($realEstates[$no]);
            $salePrice = (int)($houseProfile->GetSaleValue()*$sale->factor);
            $salePrice = 1000*round($salePrice/1000.0);
            $this->Output("他看上你的一项房产\n");
            $this->Output($houseProfile);
            $this->Output(Config::get("info.common.split0"));
            $this->Output("他愿意高价收购\n");
            $this->Output("他的收购价: ".($salePrice/10000)."万\n");
            $this->Output("如果你出售，你的这套房子将");
            $this->Output($houseProfile->GetEstimateSaleString($salePrice));
            $this->Output(Config::get("info.common.split"));
            $this->Output(Config::get("game.sale_1"));
            return true;
        }
        else if ($sale->type == "商铺")
        {
            $realEstates = $u->GetRealEstates();
            $shopProfile = new ShopProfile();
            $shopProfile->CreateFromValue($realEstates[$no]);
            $salePrice = (int)($shopProfile->GetSaleValue()*$sale->factor);
            $salePrice = 1000*round($salePrice/1000.0);
            $this->Output("他看上你的一项商业地产\n");
            $this->Output($shopProfile);
            $this->Output(Config::get("info.common.split0"));
            $this->Output("他愿意高价收购\n");
            $this->Output("他的收购价: ".($salePrice/10000)."万\n");
            $this->Output("如果你出售，你的这套房子将");
            $this->Output($shopProfile->GetEstimateSaleString($salePrice));
            $this->Output(Config::get("info.common.split"));
            $this->Output(Config::get("game.sale_1"));
            return true;
        }
    }

    public function DoOnState($userName, $input) {
        Log::info("SaleState::DoOnState $input");

        if ($input !== 'a')
        {
            $this->Output("你的输入错误，请重新输入");
            return true;
        }

        $u = $this->u;
        $m = $this->m;

        $sale = new Sale($u->GetFocusSale());

        if ($sale->type == "房产" || $sale->type == "商铺")
        {
            $no = $sale->GetRealEstateNo();
            if ($no == 0)
            {
                $this->Output("你没有能够买给他的资产");
                return true;
            }

            $realEstates = $u->GetRealEstates();
            $realEstateProfile = new RealEstateProfile();
            $realEstateProfile->CreateFromValue($realEstates[$no]);
            $salePrice = (int)($realEstateProfile->GetSaleValue()*$sale->factor);
            $salePrice = 1000*round($salePrice/1000.0);
            $u->SaleRealEstate($no, $salePrice);
            $this->Output("你高价卖了".$realEstateProfile->type."：".$realEstateProfile->name);
            $this->Output(Config::get("info.common.split"));
        }

        $m->GotoState('Base');

        return false;
    }
    
}