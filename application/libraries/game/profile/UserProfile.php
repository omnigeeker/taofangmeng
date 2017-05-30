<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-3
 * Time: 下午7:33
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Redis;

define("SALARY_AT_RATE", 0.83);
define("SALARY_ACC_RATE", 0.14);
define("BEGIN_YEAR", 2013);

use \Laravel\Config;

class UserProfile
{
    public $me = NULL;

    public function __get($varName)
    {
        if (!$this->me)
            return NULL;

        if (!isset($this->me[$varName]))
            return NULL;

        return $this->me[$varName];
    }

    public function CreateFromUser($userName, &$character)
    {
        $stocks_market = new StocksMarket();
        $this->me = array(
            "userName" => $userName,
            "version" => PROFILE_VERSION,
            "last_time" => null,
            "step" => -1,
            "profession" => $character->profession,
            "succeed" => false,
            "guid" => util::create_guid(),

            "character" => $character->Get(),
            "city" => array(
                "name" => 'None',
                "rate" => 1.0,
                "price" => 24000,
            ),

            "initial" => array(
                "salary" => $character->salary,
                "rent" => $character->rent,
                "pay_live" => $character->pay_live,
                "pay_car" => $character->pay_car,
                "pay_kid" => $character->pay_kid,
                "pay_old" => $character->pay_old,
                "pay_other" => $character->pay_other,
                "cash" => $character->cash,
                "accumulation" => $character->accumulation,
            ),
            "now" => array(
                "salary" => $character->salary,
                "rent" => $character->rent,
                "pay_live" => $character->pay_live,
                "pay_car" => $character->pay_car,
                "pay_kid" => $character->pay_kid,
                "pay_old" => $character->pay_old,
                "pay_other" => $character->pay_other,
            ),

            // 自住房产
            "self_house" => NULL,

            // 其他不动产
            "real_estate_no" => 1,    // 我自己的不动产编号
            "real_estates" => array(),

            // 理财产品
            "licai_no" => 1,
            "licais" => array(),

            // 股票基金
            "stocks" => array(),
            // array(
            //  $name => array(
            //      "name" =>,
            //      "type" =>,
            //      "count" => 11,
            //      "total" => 123.2,
            //  ),
            //  ...
            // );

            // 借款
            "loan_no" => 1,
            "loans" => array(),

            // 证书
            "certificates" => array(),

            // 学习中
            "study" => null,
//            array(
//                "type" => "cetificate",  // 学习的类型
//                "name" => "xxxx",       // 学习的目标
//                "body" => array(),       // 学习的目标
//                "left_month" => 6,       // 剩余学习时间
//            ),

            "unemployed" => array(
                "is" => false,
                "left_months" => 0,
                "last_step" => 0,       // 上次失业的月数
                "count" => 0,           // 总共失业的次数
            ),

            "cash" => $character->cash,
            "accumulation" => $character->accumulation,
            "assets" => 0,
            "liabilities" => 0,

            "month" => array(
                "work_income" => 0,     // 工作收入
                "passive_income" => 0,  // 非工作收入
                "income" => 0,          // 总收入
                "outlay" => 0,          // 总支出
                "cash_flow" => 0,       // 现金流

                "total_rent_income" => 0,
                "total_house_outlay" => 0,
            ),

            // 累计涨价幅度
            "increment" => array(
                //"price" => 1.0,
                //"rent" => 1.0,
                "accident" => 1.0,
            ),

            "messages" => array(
            ),

            "temp" => array(
                "down_left_month" => 0,
                "chances" => 0,
                "sales" => 0,
                "accidents" => 0,

                "focus_buying_real_estate" => NULL,
                "focus_assets" => NULL,
                "focus_sale" => NULL,
                "focus_accident" => NULL,
                "focus_lottery" => NULL,
                "focus_licai" => NULL,
                "focus_certificate" => NULL,
                "focus_handle_real_estate" => NULL,
                "focus_stock" => NULL,
                "focus_sale_stock" => NULL,
            ),

            "accidents" => array(
            ),

            "stocks_market" => $stocks_market->Get(),
        );

        $this->CalculateData();
    }

    public function CreateFromValue(&$me)
    {
        $this->me = &$me;
    }

    public function SaveToCache($key)
    {
        if (!$this->me)
            return;
        $r = Redis::db();
        $this->me["last_time"] = date("Y-m-d H:i:s");
        $r->set($key, json_encode($this->me));
    }

    public function LoadFromCache($key)
    {
        $r = Redis::get();
        $meStr = $r->get($key);
        if (!$meStr)
            $this->me = json_decode($meStr);
    }

    public function Get()
    {
        return $this->me;
    }

    /** 获得年龄
     * @return int
     */
    public function GetAge()
    {
        if ( !$this->me ) return 0;
        return 30 + (int)($this->me['step'] / 12);
    }

    public function GetLoanYears($type)
    {
        $age = $this->GetAge();
        if ($type == "house") {
            if ($age <= 35) return 30;
            if ($age <= 40) return 25;
            if ($age <= 45) return 20;
            if ($age <= 50) return 15;
            if ($age <= 55) return 10;
            if ($age <= 60) return 5;
        } else if ($type == "shop") {
            if ($age <= 55) return 10;
            if ($age <= 60) return 5;
        }
        else return 0;
    }

    /** 获得等级
     * @return string
     */
    public function GetGrade()
    {
        if ($this->NotHasSelfHouse())
            return "蜗居";

        $c = $this->GetRealEstateCount();
        if ($c == 0) return "房奴";
        if ($c <= 3) return "小房虫";
        if ($c <= 6) return "房虫";
        if ($c <= 9) return "房哥";
        if ($c <= 12) return "房叔";
        if ($c <= 15) return "房爷";
        return "房神";
    }

    public function StepBegin()
    {
        $this->me["step"] = 0;
    }

    public function GetCash()
    {
        return $this->me["cash"];
    }

    public function GetCashFlow()
    {
        return $this->me["month"]["cash_flow"];
    }

    public function CheckByAccident($arg, $op, $value)
    {
        if (!$arg)
            return true;
        $me = &$this->me;
        if (isset($me[$arg]))
            $key = $me[$arg];
        else if (isset($me["now"][$arg]))
            $key = $me["now"][$arg];
        else
            return false;

        switch($op) {
            case '>': return $key > $value;
            case '=': return $key == $value;
            case '==': return $key == $value;
            case '<': return $key < $value;
            default:
        }
        return false;
    }

    /*
     * Succeed
     */

    public function SignSucceed()
    {
        $this->me["succeed"] = true;
    }

    public function IsSucceed()
    {
        return $this->me["succeed"] == true;
    }

    public function CheckSucceed()
    {
        $me = &$this->me;

        if (!$this->NotHasSelfHouse() &&
            $me["month"]["passive_income"] >= $me["month"]["outlay"] &&
            $this->GetRealEstateCount() >= 10)
        {
            return 1;
        }

        return 0;
    }

    public function GetSucceedCheckInfo()
    {
        $me = &$this->me;
        $str = "你已经达成:\n";
        $count = 0;
        if (!$this->NotHasSelfHouse()) {
            $count ++;
            $str .= "- 购买自住房产\n";
        }
        if ($me["month"]["passive_income"] >= $me["month"]["outlay"]) {
            $count ++;
            $str .= "- 达到财富自由\n";
        }
        if ($this->GetRealEstateCount() >= 10) {
            $count ++;
            $str .= "- 至少购买10个不动产\n";
        }
        if ($count > 0)
            return $str;
        return "你目前还没有达成任何胜利条件";
    }

    public function IsFinancialFreedom(){
        $me = &$this->me;
        return $me["month"]["passive_income"] >= $me["month"]["outlay"];
    }

    /** 获得税后工资
     * @return int
     */
    public function GetATSalary()
    {
    	$pre_tax = $this->me["now"]["salary"] - $this->GetACCSalary() / 2;//3534
    	$pre_tax2 = $pre_tax - 3500;//34
    	$tax = 0;
    	if ($pre_tax2 <= 1500) {
    		$tax = $pre_tax2 * 0.03 - 0;
    	} else if ($pre_tax2 <= 4500) {
    		$tax = $pre_tax2 * 0.10 - 105;
    	} else if ($pre_tax2 <= 9000) {
    		$tax = $pre_tax2 * 0.20 - 555;
    	} else if ($pre_tax2 <= 35000) {
    		$tax = $pre_tax2 * 0.25 - 1005;
    	} else if ($pre_tax2 <= 55000) {
    		$tax = $pre_tax2 * 0.30 - 2755;
    	} else if ($pre_tax2 <= 80000) {
    		$tax = $pre_tax2 * 0.35 - 5505;
    	} else {
    		$tax = $pre_tax2 * 0.45 - 13505;
    	}

    	//3534 - 1 = 3533
        return (int)($pre_tax - $tax);
    }

    /** 获得公积金工资
     * @return int
     */
    public function GetACCSalary()
    {
        return (int)($this->me["now"]["salary"] * SALARY_ACC_RATE);
    }

    public function GetLiveOutlay() {
        return $this->me["now"]["pay_live"];
    }
    public function GetCarOutlay() {
        return $this->me["now"]["pay_car"];
    }
    public function GetKidOutlay() {
        if ($this->GetAge() <= 50)
            return $this->me["now"]["pay_kid"];
        return 0;
    }
    public function GetOldOutlay() {
        if ($this->GetAge() >= 40)
            return $this->me["now"]["pay_old"];
        return 0;
    }

    public function GetOtherOutlay() {
        return $this->me["now"]["pay_other"];
    }

    private function GetSelfOutlay() {
        return $this->GetLiveOutlay() + $this->GetCarOutlay() +
            $this->GetKidOutlay() + $this->GetOldOutlay() + $this->GetOtherOutlay();
    }

    // 获得天朝银行无抵押贷款的额度限制
    public function GetMaxLowerLoan() {
        $salary_estimate = 3 * $this->me["now"]["salary"] * 12;
        $cash_flow_estimate = 3 * $this->GetCashFlow() * 12;
        $base =  $salary_estimate > $cash_flow_estimate ? $salary_estimate : $cash_flow_estimate;

        //计算已经借出部分
        $loanSum = 0;
        foreach( $this->me["loans"] as $loan)
        {
            $loanProfile = new LoanProfile();
            $loanProfile->CreateFromValue($loan);
            if ($loanProfile->type == "lower")
                $loanSum += $loanProfile->GetTotal();
        }

        return $base - $loanSum;
    }

    /**
     * 重新计算所有数据
     */
    public function CalculateData()
    {
        if ( !$this->me )
            return;

        $me = &$this->me;
        $outlay = $this->GetSelfOutlay();

        if ($this->NotHasSelfHouse())
        {   // 没有房子
            $me["month"]["work_income"] = $this->GetATSalary();
            $me["month"]["passive_income"] = 0;
            $me["month"]["income"] = $me["month"]["work_income"];
            $outlay += $me["now"]["rent"];  // 没有房子还要支出月租
            $me["month"]["outlay"] = $outlay;
        }
        else
        {
            // 有房子, 公积金能用了
            $me["month"]["work_income"] = $this->GetATSalary() + (int)($this->me["now"]["salary"]*SALARY_ACC_RATE);
            //计算自住房子
            $selfHouse = new HouseProfile();
            $selfHouse->CreateFromValue($me["self_house"]);
            $outlay += $selfHouse->GetMonthOverlay();
        }

        $income = 0;
        if (!$this->IsUnemployed()) {
            $income += $me["month"]["work_income"];
        }

        $passive_income = 0;
        $assets = 0;
        $liabilities = 0;
        $total_rent_income = 0;
        $total_house_outlay = 0;                                 ;

        // 计算不动产
        foreach($me["real_estates"] as $houseDict)
        {
            $realEstate = new RealEstateProfile();
            $realEstate->CreateFromValue($houseDict);

            $income += $realEstate->GetMonthIncome();           // 房租纪录为 总收入
            $outlay += $realEstate->GetMonthOverlay();          // 贷款纪律为 总支出
            $passive_income += $realEstate->GetMonthIncome();   // 房租纪录为 被动收入

            $assets += $realEstate->GetAssetValue();
            $liabilities += $realEstate->GetLiabilities();

            $total_rent_income += $realEstate->GetMonthIncome();
            $total_house_outlay += $realEstate->GetMonthOverlay();
        }

        // 计算借款
        foreach($me["loans"] as $loan)
        {
            $loanProfile = new LoanProfile();
            $loanProfile->CreateFromValue($loan);
            $outlay += $loanProfile->GetMonthOverlay();
            $liabilities += $loanProfile->GetLeftLoan();
        }

        // 计算挂证
        foreach($me["certificates"] as $name => $certificate) {
            $income += $certificate["income"];
            $passive_income += $certificate["income"];
        }

        // 计算股票
        foreach($me["stocks"] as $name => $stock)
        {
            $market_stock = $me["stocks_market"][$name];
            $assets += round($stock["count"]*$market_stock["price"]);
        }

        // 数值赋值
        $me["month"]["passive_income"] = $passive_income;
        $me["month"]["total_rent_income"] = $total_rent_income;
        $me["month"]["total_house_outlay"] = $total_house_outlay;
        $me["month"]["income"] = $income;
        $me["month"]["outlay"] = $outlay;
        $me["assets"] = $assets;
        $me["liabilities"] = $liabilities;
        $me["month"]["cash_flow"] = $me["month"]["income"] - $me["month"]["outlay"];
    }

    public function NextStep()
    {
        Log::info("NextStep");
        $me = &$this->me;
        $me["step"] += 1;
        if ($this->NotHasSelfHouse()) {   //没有房子
            $me["accumulation"] += $this->GetACCSalary();
        } else {
            $selfHouse = new HouseProfile();
            $selfHouse->CreateFromValue($me["self_house"]);
            $selfHouse->NextStep($me["step"]);
        }

        // 理财产品
        foreach($me["licais"] as $no => $houseDict) {
            $licai = new LicaiProfile();
            $licai->CreateFromValue($me["licais"][$no]);
            $licai->NextStep($me["step"]);

            if ($licai->IsEnd()) {
                $this->AccountLicai($licai);
                unset($me['licais'][$no]);
            }
        }

        // RealEstate
        foreach($me["real_estates"] as $no => $houseDict) {
            $realEstate = new RealEstateProfile();
            $realEstate->CreateFromValue($me["real_estates"][$no]);
            $realEstate->NextStep($me["step"]);
        }

        // Loan
        foreach($me["loans"] as $no => $loan) {
            $loanProfile = new LoanProfile();
            $loanProfile->CreateFromValue($me['loans'][$no]);
            $loanProfile->NextStep($me["step"]);

            if ($loanProfile->IsEnd()) {
                unset($me['loans'][$no]);
            }
        }

        // Study
        if ($this->IsStudying()) {
            assert($me["study"]["left_month"] > 0);
            $me["study"]["left_month"] -= 1;
            if ($me["study"]["left_month"] == 0) {
                $this->OnStudyEnd($me["study"]);
                $me["study"] = NULL;
            }
        }

        if ($this->IsUnemployed()) {
            if($me["unemployed"]["left_months"] == 0) {
                $this->OnUnemployedEnd();
                $me["unemployed"]["is"] = false;
            }
            $me["unemployed"]["left_months"] -= 1;
        }

        $this->CalculateData();
        $me["cash"] += $me["month"]["cash_flow"];

        // StocksMarket
        $stocksMarket = new StocksMarket($me["stocks_market"]);
        $stocksMarket->NextStep($me["step"]);
    }

    public function NotHasSelfHouse()
    {
        return !($this->me["self_house"]) ? true : false;
    }

    // 年度价格调整
    public function AdjustPriceByYear()
    {
        $me = &$this->me;
        $now = &$me["now"];

        $year = floor($me["step"] / 12) - 1;
        $macro = new Macro();
        $salaryInc = $macro->GetCharacterSalaryInc($me["character"]["key"], $year);
        $payInc = $macro->GetCharacterPayInc($me["character"]["key"], $year);
        $housePriceInc = $macro->GetHousePriceInc($me["city"]["key"], $year);
        $houseRentInc = $macro->GetHouseRentInc($me["city"]["key"], $year);
        $shopPriceInc = $macro->GetShopPriceInc($me["city"]["key"], $year);
        $shopRentInc = $macro->GetShopRentInc($me["city"]["key"], $year);
        $cpiInc = $macro->GetCPI($year);

        $now["salary"] = (int)($now["salary"]*(1.0 + $salaryInc));
        $now["salary"] = round($now["salary"]/10)*10;

        $now["rent"] = (int)($now["rent"]*(1 + $houseRentInc));
        $now["pay_live"] = (int)($now["pay_live"]*(1.0 + $payInc));
        $now["pay_car"] = (int)($now["pay_car"]*(1.0 + $payInc));
        $now["pay_kid"] = (int)($now["pay_kid"]*(1.0 + $payInc));
        $now["pay_old"] = (int)($now["pay_old"]*(1.0 + $payInc));

        //$me["increment"]["price"] *= (1.0 + $housePriceInc);
        //$me["increment"]["rent"] *= (1.0 + $houseRentInc);
        $me["increment"]["accident"] *= (1.0+$cpiInc);

        $me["city"]["house_price"] = (int)($me["city"]["house_price"] * (1.0 + $housePriceInc));
        $me["city"]["house_price"] = round($me["city"]["house_price"]/100)*100;
        $me["city"]["shop_price"] = (int)($me["city"]["shop_price"] * (1.0 + $shopPriceInc));
        $me["city"]["shop_price"] = round($me["city"]["shop_price"]/100)*100;
        $me["city"]["house_rent"] = (int)($me["city"]["house_rent"] * (1.0 + $houseRentInc));
        $me["city"]["shop_rent"] = (int)($me["city"]["shop_rent"] * (1.0 + $shopRentInc));

        if (!$this->NotHasSelfHouse())
        {
            $selfHouse = new RealEstateProfile();
            $selfHouse->CreateFromValue($me["self_house"]);
            $selfHouse->AdjustPriceByRate($housePriceInc, $houseRentInc);
        }

        foreach($me["real_estates"] as $no => $houseDict)
        {
            $realEstate = new RealEstateProfile();
            $realEstate->CreateFromValue($me["real_estates"][$no]);
            if ($realEstate->GetType() == "房产")
                $realEstate->AdjustPriceByRate($housePriceInc, $houseRentInc);
            else
                $realEstate->AdjustPriceByRate($shopPriceInc, $shopRentInc);
        }
        $this->CalculateData();
    }


    public function GetTime()
    {
        $step = $this->me['step'];
        $year = BEGIN_YEAR + (int)($step/12);
        $month = 1 + $this->me['step'] % 12;
        return $year."年".$month."月";
    }

    /*
     * Accidents
     */

    public function MeetCashFlowAccident($accident) {
        assert($accident->mode == "cashflow");

        $this->AddCash(-$accident->first);
        $me = &$this->me;

        $out = $accident->GetOut();
        $me["now"][$out] += $accident->month;

        if ($me["now"][$out] < 0)
            $me["now"][$out] = 0;

        $this->CalculateData();
    }

    public function MeetResultAccident($accident) {
        assert($accident->mode == "result");

        $this->AddCash(-$accident->first);

        /* result_func 有些问题暂时还不能使用
         *
        if (isset($accident["result_func"])) {
            $accident["result2"]($this);
        }
        */
        $this->CalculateData();
    }

    public function RecordAccident($accident) {
        $me = &$this->me;
        $name = $accident->name;
        $me["accidents"][$name] = $me["step"];
    }

    /*
     * GetAssets
     */

    public function GetRealEstates() {
        return $this->me["real_estates"];
    }

    public function GetRealEstateCount() {
        return count($this->me["real_estates"]);
    }

    public function GetLoans() {
        return $this->me["loans"];
    }

    public function GetLoanCount() {
        return count($this->me["loans"]);
    }

    public function GetLicais() {
        return $this->me["licais"];
    }

    public function GetLicaiCount() {
        return count($this->me["licais"]);
    }

    public function GetCertificates() {
        return $this->me["certificates"];
    }

    public function GetCertificateCount() {
        return count($this->me["certificates"]);
    }


    // 调整参数

    public function SetChances($chance) {
        $this->me["temp"]["chances"] = $chance;
    }

    public function SetAccidents() {
        $this->me["temp"]["accidents"] = 1;
    }

    public function SetSales() {
        $this->me["temp"]["sales"] = 1;
    }

    public function HasUseChance() {
        $this->me["temp"]["chances"] -= 1;
    }

    public function HasUseAccident() {
        $this->me["temp"]["accidents"] -= 1;
    }

    public function HasUseSale() {
        $this->me["temp"]["sales"] -= 1;
    }

    public function  GetTemp()
    {
        return $this->me["temp"];
    }

    /*
     * Focus
     */

    public function SetFocusBuyingRealEstate($focus_buying_real_estate) {
        $this->me["temp"]["focus_buying_real_estate"] = $focus_buying_real_estate;
    }

    public function GetFocusBuyingRealEstate() {
        return $this->me["temp"]["focus_buying_real_estate"];
    }

    public function SetFocusAssets($focus_assets) {
        $this->me["temp"]["focus_assets"] = $focus_assets;
    }

    public function GetFocusAssets() {
        return $this->me["temp"]["focus_assets"];
    }

    public function SetFocusLoan($focus_loan) {
        $this->me["temp"]["focus_loan"] = $focus_loan;
    }
    public function GetFocusLoan() {
        return $this->me["temp"]["focus_loan"];
    }

    public function SetFocusSale($focus_sale) {
        $this->me["temp"]["focus_sale"] = $focus_sale;
    }
    public function GetFocusSale() {
        return $this->me["temp"]["focus_sale"];
    }

    public function SetFocusAccident($focus_accident) {
        $this->me["temp"]["focus_accident"] = $focus_accident;
    }
    public function GetFocusAccident() {
        return $this->me["temp"]["focus_accident"];
    }

    public function SetFocusLottery($focus_lottery) {
        $this->me["temp"]["focus_lottery"] = $focus_lottery;
    }
    public function GetFocusLottery() {
        return $this->me["temp"]["focus_lottery"];
    }

    public function SetFocusLicai($focus_licai) {
        $this->me["temp"]["focus_licai"] = $focus_licai;
    }
    public function GetFocusLicai() {
        return $this->me["temp"]["focus_licai"];
    }

    public function SetFocusCertificate($focus_certificate) {
        $this->me["temp"]["focus_certificate"] = $focus_certificate;
    }
    public function GetFocusCertificate() {
        return $this->me["temp"]["focus_certificate"];
    }

    public function SetFocusHandleRealEstate($focus_handle_real_estate) {
        $this->me["temp"]["focus_handle_real_estate"] = $focus_handle_real_estate;
    }
    public function GetFocusHandleRealEstate() {
        return $this->me["temp"]["focus_handle_real_estate"];
    }

    public function SetFocusStock($focus_stock) {
        $this->me["temp"]["focus_stock"] = $focus_stock;
    }
    public function GetFocusStock() {
        return $this->me["temp"]["focus_stock"];
    }
    public function SetFocusSaleStock($focus_sale_stock) {
        $this->me["temp"]["focus_sale_stock"] = $focus_sale_stock;
    }
    public function GetFocusSaleStock() {
        return $this->me["temp"]["focus_sale_stock"];
    }

    /*
     * Buy RealEstate
     */

    public function CanBuyRealEstate($real_estate) {
        return $this->GetBuyRealEstateLeftMoney($real_estate) >= 0;
    }

    public function GetBuyRealEstateLeftMoney($real_estate) {
        return $this->me["cash"] - $real_estate->GetFirstPayment();
    }

    public function CanBuyHouse($house) {
        return $this->GetBuyHouseLeftMoney($house) >= 0;
    }

    public function GetBuyHouseLeftMoney($house) {
        //Log::info( json_encode($house->Get()) );
        if ($this->NotHasSelfHouse())
            return $this->me["cash"] + $this->me["accumulation"] - $house->GetFirstPayment();
        else
            return $this->me["cash"] - $house->GetFirstPayment();
    }

    public function BuyHouse($house) {
        $me = &$this->me;
        $houseProfile = new HouseProfile();
        $houseProfile->CreateFromHouse($house->Get(), $this->step);

        $firstPayment = $house->GetFirstPayment();
        if ($this->NotHasSelfHouse())
        {   // 购买自住房
            $houseProfile->SetIsSelf(true);
            $me["cash"] -= $firstPayment;
            $me["cash"] += $me["accumulation"];
            $me["accumulation"] = 0;
            $me["self_house"] = $houseProfile->Get();
        }
        else
        {
            $houseProfile->SetIsSelf(false);
            $me["cash"] -= $firstPayment;
            $me["real_estates"][$me["real_estate_no"]] = $houseProfile->Get();
        }

        $me["real_estate_no"] += 1;
        $this->CalculateData();
    }

    public function BuyShop($shop) {
        $me = &$this->me;
        $shopProfile = new ShopProfile();
        Log::info(json_encode($shop->Get()));
        $shopProfile->CreateFromShop($shop->Get(), $this->step);

        $firstPayment = $shop->GetFirstPayment();
        $me["cash"] -= $firstPayment;
        $me["real_estates"][$me["real_estate_no"]] = $shopProfile->Get();

        $me["real_estate_no"] += 1;
        $this->CalculateData();
    }

    /*
     * Loan
     */
    protected function Loan($name, $type, $total, $rate, $months) {
        $me = &$this->me;
        $loanProfile = new LoanProfile();
        $loanProfile->CreateNew($name,
            $me["step"], $type, $total, $rate, $months);
        $me["loans"][$me["loan_no"]] = $loanProfile->Get();
        $me["loan_no"] = $me["loan_no"] + 1;
        $me["cash"] += $total;

        $this->CalculateData();

        $str = "新增一笔".$name."\n";
        $str .= "总额:" .$total."\n";
        $str .= "分期:".$months."个月\n";
        $str .= "每月还款:".$loanProfile->GetMonthOverlay()."\n";
        $str .= "你的现金流减到:".$me["month"]["cash_flow"]."\n";

        $msg = MessageState::CreateMessage("新增借款", $str);
        $this->PushMessage($msg);

    }

    public function LoanLower($total) {
        $this->Loan("天朝银行无抵押贷款",
            "lower", $total, TIANCHAO_RATE, 36);
    }

    public function LoanUpper($total) {
        $this->Loan("民间抵押贷款",
            "upper", $total, P2P_RATE, 24);
    }

    public function LoanCredit($total) {
        $this->Loan("信用卡分期还款",
            "credit", $total, CREDIT_RATE, CREDIT_MONTHS);
    }

    public function RefundLoan($no, $money) {
        $me = &$this->me;
        $loanProfile = new LoanProfile();
        $loanProfile->CreateFromValue($me["loans"]["$no"]);
        $loanProfile->Refund($money);
        if ($loanProfile->IsEnd())
            unset($me["loans"]["$no"]);

        $me["cash"] = $me["cash"] - $money;
        $this->CalculateData();
    }

    public function RefundLoanAll($no) {
        $me = &$this->me;
        $loanProfile = new LoanProfile();
        $loanProfile->CreateFromValue($me["loans"]["$no"]);
        $me["cash"] = $me["cash"] - $loanProfile->GetLeftLoan();
        unset($me["loans"]["$no"]);
        $this->CalculateData();
    }

    /*
     * Licai
     */
    public function BuyLicai($licai, $money) {
        $me = &$this->me;
        $licaiProfile = new LicaiProfile();
        $licaiProfile->CreateNew($licai, $me["step"], $money);
        $me["licais"][$me["licai_no"]] = $licaiProfile->Get();
        $me["licai_no"] += 1;
        $this->AddCash(-$money);
    }

    protected function AccountLicai($licaiProfile) {
        assert($licaiProfile->IsEnd());
        $result = $licaiProfile->GetResult();
        $this->AddCash($result["money"]);
        $msg_title = $licaiProfile->name."到结算时间了\n";
        $msg_context = "这次投资".$result["result"]."\n".
            $result["str"]."\n".
            "收到现金 ".$result["money"]."\n";
        $msg = MessageState::CreateMessage($msg_title, $msg_context);
        $this->PushMessage($msg);
    }

    /*
     * Study
     */
    public function StartStudy($study) {
        $me = &$this->me;

        $me["study"] = array(
            "type" => $study->GetType(),
            "name" => $study->GetName(),
            "body" => $study->Get(),
            "left_month" => $study->GetMonths(),
        );

        $this->AddCash( -$study->GetFirst() );
    }

    public function IsStudying() {
        return $this->me["study"] != null;
    }

    protected function OnStudyEnd($studyInfo) {
        $study = Study::CreateStudyByType($studyInfo["type"], $studyInfo["body"]);
        switch ($study->GetType()) {
            case "certificate":
                $this->OnStudyCertificateEnd($study);
                break;
            default:
                break;
        }
    }

    protected function OnStudyCertificateEnd($certificate) {
        $me = &$this->me;
        $random = rand(1, 100);
        if ($random > $certificate->GetSucceedRatePrecent() * $me["character"]["certificate_succeed_rate"]) {
            $this->PushMessage(
                MessageState::CreateMessage("考证失败", "之前的培训费用考试费用白白花了，另外，你对这门考试的信心大减，你要重新参加爱培训才能考过。")
            );
            return;
        }
        $me["certificates"][$certificate->name] = array(
            "name" => $certificate->name,
            "income" => $certificate->income,
        );
        $str = "恭喜你，你通过考试成功获得了[".$certificate->name."]\n";
        $str .= "你把这个证书挂靠出去，每月得到了被动收入".$certificate->income;
        $this->PushMessage(
            MessageState::CreateMessage("考证通过，成功获得证书", $str)
        );
        $this->CalculateData();
    }

    /*
     * unempleyed
     */

    public function GetWorkedYear() {
        $me = &$this->me;
        return floor( ($me["step"]-$me["unemployed"]["last_step"]) / 12);
    }

    public function CanUnemployed() {
        $me = &$this->me;
        if ($me["unemployed"]["count"] > Config::get("args.unemployed.max_count"))
            return false;
        if ($me["unemployed"]["last_step"] == 0) {
            if ($me["step"] - $me["unemployed"]["last_step"] < Config::get("args.unemployed.first_safe_months"))
                return false;
            return true;
        }
        if ($me["step"] - $me["unemployed"]["last_step"] < Config::get("args.unemployed.safe_months"))
            return false;
        return true;
    }

    public function IsUnemployed() {
        $me = &$this->me;
        return $me["unemployed"]["is"] == true;
    }

    public function SetUnemployed() {
        $me = &$this->me;
        $me["unemployed"]["is"] = true;
        $me["unemployed"]["left_months"] = $me["character"]["unemployed_months"];
        $me["unemployed"]["last_step"] = $me["step"];
        $me["unemployed"]["count"] += 1;

        $result = mt_rand(1,4);
        $str = "";

        $monthRealSalary = floor($me["now"]["salary"] * SALARY_AT_RATE);

        if ($result == 1)
        {   // 没有赔偿
            $str .= "你在公司干得事情太无聊了，你实在受不了了，所以一气之下向公司提出了辞职，这种情况公司是不给赔偿的";
            $this->PushMessage(
                MessageState::CreateMessage("你主动辞职了", $str)
            );
        } else if ($result == 2) {
            $money = $monthRealSalary;
            $this->AddCash($money);
            $str .= "你的公司不行了，实在发不出钱了，要你离开。你的领导非常真诚地和你谈了话，让你非常感动；你接受一个1个月赔偿的条件\n";
            $str .= "共计:".$money."\n";
            $this->PushMessage(
                MessageState::CreateMessage("你公司不行了", $str)
            );
        } else if ($result == 3) {
            $money = 3*$monthRealSalary;
            $this->AddCash($money);
            $str .= "你被公司裁掉了，你的公司还算不错，赔偿了你3个月的\n";
            $str .= "共计:".$money."\n";
            $this->PushMessage(
                MessageState::CreateMessage("你被公司裁掉了", $str)
            );
        } else {
            $N = $this->GetWorkedYear();
            $money = ($N+1)*$monthRealSalary;
            $this->AddCash($money);
            $str .= "你被公司裁掉了，你的公司提出的赔偿条件你不同意，你要求N+1配成，你在这家公司工作了".$N."年".
                "，所以能获得".($N+1)."个月工资的赔偿\n";
            $str .= "共计:".$money."\n";
            $this->PushMessage(
                MessageState::CreateMessage("你被公司裁掉了", $str)
            );
        }

        $this->CalculateData();
    }

    public function OnUnemployedEnd() {
        $me = &$this->me;
        $str = "你经过了".$me["character"]["unemployed_months"]."个月的努力,终于重新找到了工作\n";
        $str .= "月薪：".$me["now"]["salary"]."\n";
        $str .= "到手月薪：".$me["month"]["work_income"]."\n";
        $this->PushMessage(
            MessageState::CreateMessage("你重新找到了工作", $str)
        );
        $this->CalculateData();
    }

    /*
     * Sale RealEstate
     */

    public function SaleRealEstate($num, $money)
    {
        Log::info("SaleRealEstate $num $money");
        $me = &$this->me;
        if (!isset($me["real_estates"][$num]))
            return;

        $RealEstateProfile = new RealEstateProfile();
        $RealEstateProfile->CreateFromValue($me["real_estates"][$num]);
        $restMoney = $RealEstateProfile->GetSellMoney($money);

        $me['cash'] += $restMoney;
        unset($me['real_estates'][$num]);

        $this->CalculateData();
    }

    public function PrepaymentRealEstate($num, $money)
    {
        $me = &$this->me;
        if (!isset($me["real_estates"][$num]))
            return;
        $RealEstateProfile = new RealEstateProfile();
        $RealEstateProfile->CreateFromValue($me["real_estates"][$num]);
        $RealEstateProfile->Prepayment();
        $this->CalculateData();
    }

    public function SearchForSale($sale)
    {
        $me = &$this->me;
        foreach($me["real_estates"] as $num => $realEstate)
        {
            if ($realEstate["type"] != $sale->type)
                continue;

            if ($realEstate["grade"] != $sale->grade)
                continue;

            if ($sale->keyword && $sale->keyword != '')
            {
                if (FALSE === strpos($realEstate["describe"], $sale->keyword))
                    continue;
            }

            return $num;
        }
        return 0;
    }

    public function AddCash($money) {
        $this->me["cash"] += $money;
    }

    public function SetCity($city) {
        $this->me["city"] = $city;
    }

    /*
     * Stock
     */
    public function BuyStock($stock, $count) {
        $me = &$this->me;
        $stocks = &$me["stocks"];
        $total = round($stock->price * $count);
        $this->AddCash(-$total);

        if (empty($stocks[$stock->name])) {
            $stocks[$stock->name] = array(
                "name" => $stock->name,
                "type" => $stock->type,
                "count" => $count,
                "total" => $total,
            );
        } else {
            $stocks[$stock->name]["count"] += $count;
            $stocks[$stock->name]["total"] += $total;
        }
        return $total;
    }

    public function SaleStockAll($stockName) {
        $me = &$this->me;
        if (empty($me["stocks"][$stockName])) {
            return;
        }
        $stock = &$me["stocks"][$stockName];
        $marketStock = &$me["stocks_market"][$stockName];
        $cash = round($stock["count"]*$marketStock["price"]);
        $this->AddCash($cash);
        unset($me["stocks"][$stockName]);
        return $cash;
    }

    public function SaleStock($stockName, $count){
        $me = &$this->me;
        if (empty($me["stocks"][$stockName])) {
            return;
        }
        $stock = &$me["stocks"][$stockName];
        $marketStock = &$me["stocks_market"][$stockName];
        if ($stock["count"] < $count)
            $count = $stock["count"];
        $cash = round($count*$marketStock["price"]);
        $this->AddCash($cash);
        $stock["count"] -= $count;
        if ($stock["count"] == 0) {
            unset($me["stocks"][$stockName]);
        }
        return $cash;
    }

    public function GetStocksCount() {
        $me = &$this->me;
        return count($me["stocks"]);
    }

    public function GetSimpleStocksString() {
        $me = &$this->me;
        $stocks = &$me["stocks"];
        $str = "股票基金 ".count($stocks)." 个\n";
        if (count($stocks) == 0)
            return $str;
        $delta = 0;
        foreach ($stocks as $name => $stock) {
            $now_price = $me["stocks_market"][$name]["price"];
            $now_total = round($now_price * $stock["count"]);
            $delta += $now_total - $stock["total"];
        }
        if ($delta > 0)
            $str .= "赚".$delta."\n";
        else $str .= "亏".-$delta."\n";
        return $str;
    }

    /*
     * Message
     */

    public function GetMessageCount() {
        return count($this->me["messages"]);
    }

    public function PushMessage($msg) {
        array_push( $this->me["messages"], $msg);
    }

    public function PopMessage() {
        return array_shift($this->me["messages"]);
    }

    public function GetAllMessages() {
        return $this->me["messages"];
    }

    /*
     * String
     */
    public function __toString() {
        $me = &$this->me;
        $month = &$me["month"];
        $ret = "你的信息(".$this->GetGrade().")\n";
        $split = Config::get("info.common.split");
        $split0 = Config::get("info.common.split0");
        $ret .= "年龄:".$this->GetAge()."(".$this->GetTime().")\n";
        $ret .= "税前工资:".$me["now"]["salary"]."\n";
        if ($this->GetRealEstateCount() > 0)
            $ret .= "投资不动产 ".$this->GetRealEstateCount()." 套\n";
        if ($this->GetLoanCount() > 0)
            $ret .= "额外贷款 ".$this->GetLoanCount()." 笔\n";
        if ($this->GetLicaiCount() > 0)
            $ret .= "理财产品 ".$this->GetLicaiCount() ." 个 \n";
        if ($this->GetCertificateCount() > 0)
            $ret .= "挂证 ".$this->GetCertificateCount() ." 个 \n";

        if ($this->GetStocksCount() > 0) {
            $ret .= $split0."\n";
            $ret .= $this->GetSimpleStocksString();
            if (count($me["stocks"]) >0)
                $ret .= "回复[z]可以查看和出售\n";
        }

        $ret .= $split0."\n";

        if ($this->NotHasSelfHouse() )
        {
            $ret .= "月总收入: ".$month["income"]."\n";
            $ret .= "月房租支出: ".$me["now"]["rent"]."\n";
            $ret .= "月生活支出: ".$this->GetLiveOutlay()."\n";
            if($this->GetCarOutlay()>0)
                $ret .= "月汽车支出: ".$this->GetCarOutlay()."\n";
            if($this->GetKidOutlay()>0)
                $ret .= "月小孩支出: ".$this->GetKidOutlay()."\n";
            if($this->GetOldOutlay()>0)
                $ret .= "月老人支出: ".$this->GetOldOutlay()."\n";
            if($this->GetOtherOutlay()>0)
                $ret .= "月其他支出: ".$this->GetOtherOutlay()."\n";
            $ret .= "月总支出: ".$month["outlay"]."\n";
            $ret .= $split0."\n";
            
//            $ret .= "公积金定缴： ".$this->GetACCSalary()."\n";
//            $ret .= "公积金账户： ".$me["accumulation"]."\n";
//            $ret .= "月现金流: ".$month["cash_flow"]."\n";
//            $ret .= "手上现金: ".$me["cash"]."\n";

            $ret .= util::FormatLine("公积金定缴", $this->GetACCSalary())."\n";
            $ret .= util::FormatLine("公积金", $me["accumulation"])."\n";
            $ret .= util::FormatLine("月现金流", $month["cash_flow"])."\n";
            $ret .= util::FormatLine("现金", $me["cash"])."\n";
        }
        else
        {
            $ret .= "月被动收入: ".$month["passive_income"]."\n";
            $ret .= "月总收入: ".$month["income"]."\n";
            $ret .= "月总支出: ".$month["outlay"]."\n";
            $ret .= $split0."\n";
            $ret .= "手上现金: ".$me["cash"]."\n";
            $ret .= "月现金流收入: ".$month["cash_flow"]."\n";
        }

        return $ret;
    }

    public function GetMeString()
    {
        $me = &$this->me;
        $month = &$me["month"];
        $ret = "你的信息\n";

        $split = Config::get("info.common.split");
        $split0 = Config::get("info.common.split0");

        if ($this->NotHasSelfHouse() )
        {
            $ret = $ret.
                "日期: ".$this->GetTime()."\n".
                "税前工资: ".$me["now"]["salary"]."\n".
                $split0."\n".
                "税前工资: ".$me["now"]["salary"]."\n".
                "到手工资: ".$this->GetATSalary()."\n".
                "公积金工资：".$this->GetACCSalary()."\n".
                "买房前公积金不到手\n".
                "月总收入: ".$month["income"]."\n".
                $split0."\n".
                "月房租支出: ".$me["now"]["rent"]."\n".
                "月生活支出: ".$this->GetLiveOutlay()."\n".
                "月汽车支出: ".$this->GetCarOutlay()."\n".
                "月小孩支出: ".$this->GetKidOutlay()."\n".
                "月老人支出: ".$this->GetOldOutlay()."\n".
                "月其他支出: ".$this->GetOtherOutlay()."\n".
                "月总支出: ".$month["outlay"]."\n";
            if ($this->GetRealEstateCount() > 0)
                $ret = $ret.$split0."\n".
                    "投资不动产 ".$this->GetRealEstateCount()." 套\n".
                    "资产总额: ".$me["assets"]."\n".
                    "贷款总额: ".$me["liabilities"]."\n";
            $ret = $ret.$split0."\n".
            	
//                "公积金定缴： ".$this->GetACCSalary()."\n".
//                "公积金账户： ".$me["accumulation"]."\n".
//            	"月现金流: ".$month["cash_flow"]."\n".
//                "手上现金: ".$me["cash"]."\n".
            	util::FormatLine("公积金定缴", $this->GetACCSalary())."\n".
            	util::FormatLine("公积金", $me["accumulation"])."\n".
            	util::FormatLine("月现金流", $month["cash_flow"])."\n".
            	util::FormatLine("现金", $me["cash"])."\n".
                $split0."\n";
        }
        else if (count($me["real_estates"]) >= 0)
        {
            $ret = $ret.
                "日期: ".$this->GetTime()."\n".
                "年龄: ".$this->GetAge()."\n".
                "等级: ".$this->GetGrade()."\n".
                $split0."\n".
                "税前工资: ".$me["now"]["salary"]."\n".
                "到手工资: ".($this->GetATSalary()+$this->GetACCSalary())."\n".
                "总月租收入: ".$me["month"]["total_rent_income"]."\n".
                "月总收入: ".$month["income"]."\n".
                $split0."\n".
                "住房月供: ".$me["self_house"]["now"]["month_payment"]."\n".
                "月生活支出: ".$this->GetLiveOutlay()."\n".
                "月汽车支出: ".$this->GetCarOutlay()."\n".
                "月小孩支出: ".$this->GetKidOutlay()."\n".
                "月老人支出: ".$this->GetOldOutlay()."\n".
                "月其他支出: ".$this->GetOtherOutlay()."\n".
                "总月供支出: ".$me["month"]["total_house_outlay"]."\n".
                "月总支出: ".$month["outlay"]."\n".
                $split0."\n".
                "投资不动产 ".count($me["real_estates"])." 套\n".
                "资产总额: ".$me["assets"]."\n".
                "贷款总额: ".$me["liabilities"]."\n".
                $split0."\n".
                "月总收入: ".$month["income"]."\n".
                "月总支出: ".$month["outlay"]."\n".
                $split0."\n".
                "手上现金: ".$me["cash"]."\n".
                "月现金流收入: ".$month["cash_flow"]."\n".
                "月被动收入: ".$month["passive_income"]."\n".
                $split0."\n";
        }

        $ret .= $split0."\n";
        if ($this->GetRealEstateCount() > 0)
            $ret .= "投资不动产 ".$this->GetRealEstateCount()." 套\n";
        if ($this->GetLoanCount() > 0)
            $ret .= "额外贷款 ".$this->GetLoanCount()." 笔\n";
        if ($this->GetLicaiCount() > 0)
            $ret .= "理财产品 ".$this->GetLicaiCount() ." 个 \n";
        if ($this->GetCertificateCount() > 0)
            $ret .= "挂证 ".$this->GetCertificateCount() ." 个 \n";
        if ($this->GetStocksCount() > 0) {
            $ret .= $split0."\n";
            $ret .= $this->GetSimpleStocksString();
        }

        return $ret;
    }

    public function GetDetailString()
    {
        $me = $this->me;
        $ret = $this->GetMeString();
        $split = Config::get("info.common.split");
        return $ret;
    }

    public function BuyHouseInfo($focus_buying_real_estate)
    {
        $me = &$this->me;
        $willCash = $me["cash"]+$me["accumulation"] - $focus_buying_real_estate["ext"]["first"];

        $ret = "当前储蓄: ".$me["cash"]."\n";
        if ($this->NotHasSelfHouse()) {
            $ret .= "公积金余额: ".$me["accumulation"]."\n";
        }
        if ($willCash < 0)
        {
            $ret = $ret."你的钱不够，需要额外贷款"."\n";
            return $ret;
        }
        $ret = $ret."剩余储蓄:".$willCash."\n";
        return $ret;
    }

    /** 梦梦买房前分析
     * @param $focus_buying_real_estate
     */
    public function MengMengBuyHouse($focus_buying_real_estate)
    {
        $me = &$this->me;
        $ret = "";
        if ($this->NotHasSelfHouse())
        {   // 自住房分析
            $ret .= "[梦梦自住房分析]"."\n";
            if ($focus_buying_real_estate["grade"] === 'grade3' ||
                $focus_buying_real_estate["grade"] === 'grade4')
            {
                $ret .= "你的第一套房子不建议购买豪宅或者别墅"."\n";
            }

            $nowRent = $this->me["now"]["rent"];
            $monthAcc = $this->GetACCSalary();
            $monthPayment = $focus_buying_real_estate["ext"]["month_payment"];

            $ret = $ret."你现在的房租:".$nowRent."\n";
            if ($nowRent > $monthPayment)
            {
                $ret .= "你的月供小于你付的房租，只要你喜欢这个房子，梦梦强烈建议你买下，别傻乎乎的帮别人养房子了"."\n";
                return $ret;
            }
            if ($nowRent + $monthAcc > $monthPayment)
            {
                $ret .= "乍一看，你的月供比房租高，但是你忘了还有一笔钱，它只有在买房时才能用，这就是公积金。".
                    "你的月公积金是 ".$monthAcc.",月供扣除公积金之后，已经小于房租，你说买房划算还是租房划算。如果这样还不买，那就老老实实帮别人养房子去吧。";
                return $ret;
            }

            $delta = $monthPayment - $monthAcc - $nowRent;
            $cashFlow = $me["month"]["cash_flow"];
            if ($delta < $cashFlow / 3)
            {
                $ret .= "乍一看，你的月供比房租高，但是你忘了还有一笔钱，它只有在买房时才能用，这就是公积金。".
                    "你的月公积金是 ".$monthAcc.",算上公积金，你每月只需要多负担.".$delta."\n".
                    "你每月现金流是".$cashFlow."你增加的负担不到现金流的1/3, 一般来说对你生活影响不大，梦梦非常建议你购买\n";
                return $ret;
            }
            else if ($delta < $cashFlow * 2 / 3)
            {
                $ret .= "乍一看，你的月供比房租高，但是你忘了还有一笔钱，它只有在买房时才能用，这就是公积金。".
                    "你的月公积金是 ".$monthAcc.",算上公积金，你每月要多负担.".$delta."\n".
                    "你每月现金流是 ".$cashFlow."。你增加的负担接近现金流的2/3, 生活将相当吃紧，你要考虑清楚。\n";
                return $ret;
            }
            else if ($delta > $cashFlow)
            {
                $ret .= "如果你购买了这个房产后，你的月现金流就为负了，你这样做一定要保证有足够的存款。梦梦不建议你采用这样极端的方式来实现淘房梦。";
                return $ret;
            }
            else
            {
                $ret .= "注意喔，你买了后相当于每月比现在多负担".$delta."。".
                    "你每个月算上其他开支，如果买了这套房子，剩下的现金流就不多了。\n";
                return $ret;
            }
        }
        else
        {   //投资分析
            $ret .= "[梦梦投资房产分析]"."\n";
            $monthPayment = $focus_buying_real_estate["ext"]["month_payment"];
            $monthRent = $focus_buying_real_estate["rent"];
            $delta = $monthPayment - $monthRent;
            if ($delta < 0) {
                $ret .= "哇，在天朝居然还能找到租金大于月供的房子，梦梦建议你买下来。因为你只出了首付，不仅获得了房子，而且每个月还能赚钱。\n";
                return $ret;
            }
            $cashFlow = $me["month"]["cash_flow"];
            if ($delta < $cashFlow * 2/3 && $focus_buying_real_estate["sale_rate"]>1.5)
            {
                $ret .= "你看好的房子居然比市场价便宜那么多，但是不意味着你将来能以市场价把它卖出，梦梦认为这笔生意值得，但不是完全没有风险。\n";
                return $ret;
            }

            if ($monthPayment * 0.8 < $monthRent)
            {
                $ret .= "你看好的房子的租金比月供少，但是少不了多少，梦梦建议你可以考虑，因为道理很简单，随着货币贬值，房价上涨，房租会跟着上涨，但是月供不变，过几年再看，或许每个月就能给你带来收入了。\n";
                return $ret;
            }
            else
            {
                $ret .= "梦梦建议这种情况有点危险，你要注意喔\n";
                return $ret;
            }
        }

    }

    public function BuyShopInfo($focus_buying_real_estate)
    {
        $me = &$this->me;
        $willCash = $me["cash"] - $focus_buying_real_estate["ext"]["first"];

        $ret = "现有储蓄: ".$me["cash"]."\n";
        if ($willCash < 0)
        {
            $ret = $ret."你的现金不够，需要额外贷款"."\n";
            return $ret;
        }
        $ret .= "剩余储蓄:".$willCash."\n";
        return $ret;
    }


    public function MengMengBuyShop($focus_buying_real_estate)
    {
        $ret = "[梦梦自商业地产分析]商业地产投资风险远远大于住宅投资，而且变数很多，你一定要注意";
        return $ret;
    }
}
