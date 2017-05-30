<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-10
 * Time: 下午12:13
 * To change this template use File | Settings | File Templates.
 */

class MeLoanState extends State {

    public function DoGotoState() {
        Log::info("MeLoanState::DoGotoState");

        $u = $this->u;

        if ($u->GetLoanCount() == 0)
        {
            $this->Output("你当前没有贷款");
            return false;
        }

        $loans = $u->GetLoans();
        $this->Output("你的额外贷款：");
        foreach($loans as $num => $loan)
        {
            $this->Output(Config::get("info.common.split0"));
            $loanProfile = new LoanProfile();
            $loanProfile->CreateFromValue($loan);
            $this->Output("回复【".$num."】查看：");
            $this->Output($loanProfile);
        }
        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("MeLoanState::DoOnState $input");

        $u = $this->u;
        $loans = $u->GetLoans();
        if (empty($loans[$input]))
        {
            $this->Output("没有找到该贷款，请重新输入");
            return false;
        }

        $loanProfile = new LoanProfile();
        $loanProfile->CreateFromValue($loans[$input]);
        $this->Output($loanProfile->GetDetailString());
        $this->Output("【0】返回");
        return true;
    }

}