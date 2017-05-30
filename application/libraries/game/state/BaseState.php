<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午4:06
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class BaseState extends State {

    public function DoGotoState() {
        Log::info("BaseState::DoGotoState");

        $u = $this->u;
        $m = $this->m;

        if ($u->GetMessageCount() > 0) {
            $m->GotoState('Message');
            return true;
        }

        $this->Output($u);
        $this->Output(Config::get("info.common.split"));

        $temp = $u->GetTemp();

        $this->Output("现在:".$u->GetTime());

        if ($temp["accidents"] > 0) {
            $this->Output(Config::get("game.base_accident"));
            return true;
        }
        if ($temp["sales"] > 0) {
            $this->Output(Config::get("game.base_sale"));
            return true;
        }
        if ($temp["chances"] > 0) {
            $this->Output(Config::get("game.base_chance"));
            return true;
        }

        if ($u->IsStudying())
        {
            $study = $u->study;
            $this->Output("本月你专心学习[".$study["name"]."]，不干其他事情,你还需要学习".$study["left_month"]."个月");
        }
        else if ($u->IsUnemployed())
        {   // 失业了
            if ($u->IsFinancialFreedom()) {
                $this->Output("你虽然失业了，但是你已经财富自由，你一点也不担心，所以你可以干其他事情");
            } else {
                $this->Output("你失业了，你只有专心找工作，没有任何心情干其他事情");
            }
        }
        $this->Output(Config::get("game.base_step"));

        return true;
    }

    public function CreateFocusAccident()
    {
        $u = $this->u;
        $accident = Accident::GetRandomAccident($u);
        $accident->AdjustPrice($u->increment);
        $u->SetFocusAccident($accident->Get());
    }

    public function CreateFocusSale()
    {
        $u = $this->u;
        $sale = Sale::GetRandomSale();
        if ($sale->type == '房产' || $sale->type == '商铺') {
            $no = $u->SearchForSale($sale);
            $sale->SetRealEstateNo($no);
        }
        $u->SetFocusSale($sale->Get());
    }

    public function CreateFocusLottery() {
        $u = $this->u;
        $lottery = Lottery::GetRandomLottery();
        $u->SetFocusLottery($lottery->Get());
    }

    public function CreateFocusLicai() {
        $u = $this->u;
        $licai = Licai::GetRandomLicai();
        $u->SetFocusLicai($licai->Get());
    }

    public function CreateCertificate() {
        $u = $this->u;
        $certificate = null;
        while(true) {
            $certificate = Certificate::GetRandomCertificate();
            $certificates = $u->GetCertificates();
            if (empty($certificates[$certificate->name])) {
                break;
            }
        }
        $u->SetFocusCertificate($certificate->Get());
    }

    public function CreateFocusStock() {
        $u = $this->u;
        $stock = Stock::GetRandomStock($u);
        $u->SetFocusStock($stock->Get());
    }

    public function DoOnState($userName, $input) {
        Log::info("BaseState::DoOnState");

        $u = $this->u;
        $m = $this->m;

        $temp = $u->GetTemp();
        if ($temp["accidents"] > 0) {
            if ($input === 'a') {
                $u->HasUseAccident();
                $this->CreateFocusAccident();
                $m->GotoState('Accident');
            }
            return true;
        }
        else if ($temp["sales"] > 0) {
            if ($input === 'a') {
                $u->HasUseSale();
                $this->CreateFocusSale();
                $m->GotoState('Sale');
            } else if ($input === 'q') {
                $u->HasUseSale();
                $m->GotoState('Base');
            }
            return true;
        }
        else if ($temp["chances"] > 0) {
            switch(true)
            {
                case $input === 'a':
                    $m->GotoState('HouseBase');
                    return true;
                case $input === 'b':
                    $m->GotoState('ShopBase');
                    return true;
                case $input === 'c':
                    $u->HasUseChance();
                    $this->CreateFocusStock();
                    $m->GotoState('StockChance');
                    return true;
                case $input === 'f':
                    $this->Output("暂时还不支持，请重新输入");
                    return true;
                case $input === 'd':
                    $u->HasUseChance();
                    $this->CreateFocusLicai();
                    $m->GotoState('LicaiChance');
                    return true;
                case $input === 'e':
                    $max_count = Config::get("args.certificate.max_count");
                    if ($u->GetCertificateCount() >= $max_count)
                    {
                        $msg = MessageState::CreateMessage("不能再考证了","人的精力有限，最多挂考".$max_count."个证");
                        $u->PushMessage($msg);
                        $m->GotoSameState();
                        return true;
                    }
                    $u->HasUseChance();
                    $this->CreateCertificate();
                    $m->GotoState("CertificateChance");
                    return;
                case $input === 'g':
                    $u->HasUseChance();
                    $this->CreateFocusLottery();
                    $m->GotoState('Lottery');
                    return true;

                default: break;
            }
        }

        switch(true)
        {
            case $input === 'o':
                $m->GotoState('OtherBase');
                return true;
            case $input === 'x':
                $m->GotoState('AssetsList');
                return true;
            case $input === 'y':
                $m->GotoState('Loan');
                return true;
            case $input === 'z':
                $m->GotoState('StockList');
                return true;
            default: break;
        }

        if ($input === 'n')
        {
            if ($u->GetCash() >= 0)
            {
                $m->GotoState('Step');
                return true;
            }

            // else
            if (!(
                $u->GetRealEstateCount() > 0 ||
                $u->GetMaxLowerLoan() >= 10000
            )) {
                $m->GotoState('Step');
                return true;
            }

            $this->Output(
                "你现在的现金是 ".$u->cash."\n".
                "你是你临时找特殊渠道垫付的".
                "你需要立即偿还，不然下个月将 GameOver");
            if ($u->GetRealEstateCount() > 0)
            {
                $this->Output("【x】急售不动产");
            }
            if ($u->GetMaxLowerLoan() >= 10000)
            {
                $this->Output("【y】银行无抵押贷款");
            }
            $this->Output("【home】重新开始");
            return true;
        }

        return false;
    }

}