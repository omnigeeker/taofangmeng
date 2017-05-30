<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午4:05
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class StepState extends State {

    protected function GetIncrease($old, $new) {
        assert($old > 0);
        $percent = ($new - $old)*100/$old;
        $percent = round($percent, 1);
        if ($percent > 0)
            return '(+'.$percent.'%)';
        else return '('.$percent.'%)';
    }

    public function SaveYearProfileToDB() {
        $yearProfile = new YearProfile();
        $yearProfile->username = $this->m->GetUserName();
        $yearProfile->guid = $this->u->guid;
        $yearProfile->year = floor($this->u->step / 12);
        $yearProfile->detail = json_encode($this->u->Get());
        $yearProfile->Save();
    }

    public function NextYear() {
        $u = $this->u;

        $this->SaveYearProfileToDB();

        $cityName = $u->city["name"];

        $old_info = $u->Get();
        $old = new UserProfile();
        $old->CreateFromValue($old_info);
        $u->AdjustPriceByYear();
        $new = $u;


        $year = 2013 + (int)($u->step/12);

        $str = "";
        if ($u->IsUnemployed()) {
            $str .= "你现在失业了，没有工作收入aa\n";
        } else {
            if ($new->now["salary"] > $old->now["salary"]) {
                $str .= "你的领导告诉你工资涨了"."\n";
                $str .= "原工资:".$old->now["salary"]."\n";
                $str .= "新工资:".$new->now["salary"].
                    $this->GetIncrease($old->now["salary"],$new->now["salary"])."\n";
            } else {
                $str .= "你的单位认为你的价值不大，所以没有给你涨工资"."\n";
            }
        }
        $str .= Config::get("info.common.split")."\n";
        $str .= "你突然发现物价上涨"."\n";
        $str .= "原生活支出:".$old->GetLiveOutlay()."\n";
        $str .= "现生活支出:".$new->GetLiveOutlay().
            $this->GetIncrease($old->GetLiveOutlay(),$new->GetLiveOutlay())."\n";
        if ($old->GetCarOutlay() > 0) {
            $str .= "原汽车支出:".$old->GetCarOutlay()."\n";
            $str .= "现汽车支出:".$new->GetCarOutlay().
                $this->GetIncrease($old->GetCarOutlay(),$new->GetCarOutlay())."\n";
        }
        if ($new->GetKidOutlay() > 0) {
            $str .= "原小孩支出:".$old->GetKidOutlay()."\n";
            $str .= "现小孩支出:".$new->GetKidOutlay().
                $this->GetIncrease($old->GetKidOutlay(),$new->GetKidOutlay())."\n";
        } else {
            $str .= "你的小孩已经长大了";
        }
        if ($new->GetOldOutlay() > 0) {
            $str .= "原老人支出:".$old->GetOldOutlay()."\n";
            $str .= "现老人支出:".$new->GetOldOutlay().
                $this->GetIncrease($old->GetOldOutlay(),$new->GetOldOutlay())."\n";
        } else {
            $str .= "老人身体还健康,还无须子女负担费用\n";
        }
        $commonOutlay = $new->GetLiveOutlay() + $new->GetCarOutlay() + $new->GetKidOutlay() + $new->GetOldOutlay();
        $str .= "你现在的日常总支出是".$commonOutlay."\n";
        $str .= Config::get("info.common.split")."\n";
        $str .= "你还发现，".$cityName."房价也变了"."\n";
        $str .= "原来均价:".$old->city["house_price"]."\n";
        $str .= "新的均价:".$new->city["house_price"];
        $str .=  $this->GetIncrease($old->city["house_price"],$new->city["house_price"])."\n";
        $msg = MessageState::CreateMessage("现在进入了 $year 年", $str);
        $u->PushMessage($msg);

        if ($u->NotHasSelfHouse()) {
            // 没有房子，租金上涨

            $str = "";
            $str .= "你租房的房东打来电话，告诉你现在房租涨价了，如果不接受就只有另换地方"."\n";
            $str .= "原租金:".$old->now["rent"]."\n";
            $str .= "新租金:".$new->now["rent"]."\n";
            $str .= "你出去看了一圈房租，发现房租都涨了，于是接受了房东的要求"."\n";
            $msg = MessageState::CreateMessage("现在进入了 $year 年", $str);
            $u->PushMessage($msg);
        }

        if ($u->GetRealEstateCount() > 0) {
            $str = "物价上涨，你上调了投资房产的租金"."\n";
            $allCashFlow = 0;
            foreach($new->GetRealEstates() as $no => $realEstate) {
                $str .= "--".$realEstate["name"]."\n";
                $houseProfile = new HouseProfile();
                $houseProfile->CreateFromValue($realEstate);
                $delta_rent = $new->real_estates[$no]["now"]["rent"] - $old->real_estates[$no]["now"]["rent"];
                $str .= "租金上涨:".$delta_rent.
                    $this->GetIncrease($old->real_estates[$no]["now"]["rent"], $new->real_estates[$no]["now"]["rent"])."\n";
                $str .= "现金流增加到:".$houseProfile->GetCashFlow()."\n";
                $allCashFlow += $houseProfile->GetCashFlow();
            }
            $str .= SPLIT0."\n";
            $str .= "你被动现金流增加到:".$allCashFlow."\n";

            $msg = MessageState::CreateMessage("现在进入了 $year 年", $str);
            $u->PushMessage($msg);
        }

        //年度提示
        {
            $str = Config::get("game.succeed_condition");
            $str .= Config::get("info.common.split")."\n";
            $str .= $u->GetSucceedCheckInfo();

            $msg = MessageState::CreateMessage("现在进入了 $year 年", $str);
            $u->PushMessage($msg);
        }
    }

    // step
    public function DoGotoState() {
        Log::info("StepState::DoGotoState");

        $u = $this->u;
        $m = $this->m;

        if ($u->step < 0)
        {   //开始
            $this->Output(Config::get("game.succeed_condition"));
            $this->Output(Config::get("game.welcome"));
            $u->SetChances(1);
            $u->StepBegin();
            return false;
        }

        // 判断游戏结束
        if ($u->step >= 360)
        {
            $this->Output(Config::get("game.failed_60"));
            $m->GotoState('End');
            return true;
        }
        if ($u->GetCash() < 0)
        {
            $this->Output(Config::get("game.failed_no_cash"));
            $m->GotoState('End');
            return true;
        }


        if (!$u->IsSucceed())
        {
            $result = $u->CheckSucceed();
            if ($result > 0) {
                $this->Output(Config::get("game.succeed_0"));
                if ($result === 1)
                    $this->Output(Config::get("game.succeed_1"));
//                else if ($result === 2)
//                    $this->Output(Config::get("game.succeed_2"));
                $m->GotoState('Succeed');
                return true;
            }
        }

        if ($u->IsSucceed())
            $this->Output("你已胜利，回复home可重来");

        // step +=1
        Log::info("userProfile->NextStep();");

        $u->NextStep();

        if ($u->step > 0)
        {
            if ($u->step >=2)
            {
                if(mt_rand(1,Config::get("args.sale.rate")) <= 1 && 0<$u->GetRealEstateCount())
                    $u->SetSales();
                if(mt_rand(1,Config::get("args.accident.rate")) <= 1)
                    $u->SetAccidents();

                if (!$u->IsUnemployed() && $u->CanUnemployed()) {
                    if(mt_rand(1,Config::get("args.unemployed.rate")) <= 1) {
                        $u->SetUnemployed();
                    }
                }
            }

            if ($u->step % 12 == 0)
            {   // 一整年
                $this->NextYear();
            }
        }

        if ($u->IsStudying()) {
            $u->SetChances(0);
        } else if ($u->IsUnemployed()) {
            if ($u->IsFinancialFreedom()) {
                $u->SetChances(1);
            } else {
                $u->SetChances(0);
            }
        } else {
            $u->SetChances(1);
        }

        $m->GotoState('Base');

        return true;
    }

}