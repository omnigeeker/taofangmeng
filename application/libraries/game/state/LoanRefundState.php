<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 下午5:48
 * To change this template use File | Settings | File Templates.
 */

class LoanRefundState extends State {
    // assethandle
    public function DoGotoState() {
        Log::info("LoanRefundState::DoGotoState");

        $u = $this->u;

        $handle_loan_num = $u->GetFocusLoan();
        $loans = $u->GetLoans();
        $loan = $loans[$handle_loan_num];
        $loanProfile = new LoanProfile();
        $loanProfile->CreateFromValue($loan);

        $this->Output($loanProfile->GetDetailString());
        $this->Output("你现在现金是 ".$u->GetCash());

        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("LoanRefundState::DoOnState $input");

        $u = $this->u;
        $m = $this->m;

        $handle_loan_num = $u->GetFocusLoan();
        $loanProfile = new LoanProfile();
        $loans = $u->GetLoans();
        $loanProfile->CreateFromValue($loans[$handle_loan_num]);

        $money = 0;
        if ($input == "t") {
            $money = $loanProfile->GetLeftLoan();
        } else {
            $money = (int)$input;
        }

        if ($money <= 0){
            $this->Output("输入错误，请重新输入");
            return false;
        }

        if ($money > $u->GetCash()){
            $this->Output("输入金额不能超过你手上的现金，请重新输入");
            return false;
        }

        if ($money > $loanProfile->GetLeftLoan()){
            $this->Output("输入金额不能超过还款额喔，请重新输入");
            return false;
        }

        $u->RefundLoan($handle_loan_num, $money);

        $this->Output("成功偿还贷款".$loanProfile->GetName()." $money 元\n");

        $m->GotoState('Base');

        return true;
    }
}