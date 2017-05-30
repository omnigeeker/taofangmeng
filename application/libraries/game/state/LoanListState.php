<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 下午5:20
 * To change this template use File | Settings | File Templates.
 */

class LoanListState extends State {
    // assetslist
    public function DoGotoState() {
        Log::info("LoanList::DoGotoState");

        $u = $this->u;

        if ($u->GetLoanCount() == 0)
        {
            $this->Output("你当前没有贷款，无需偿还");
            return false;
        }

        $loans = $u->GetLoans();
        $this->Output("请选择你要偿还的借款：");
        $this->Output("【梦梦提示】贷款可以还一部分");
        foreach($loans as $num => $loan)
        {
            $this->Output(Config::get("info.common.split0"));
            $loanProfile = new LoanProfile();
            $loanProfile->CreateFromValue($loan);
            $this->Output("回复【".$num."】处理：");
            $this->Output($loanProfile->__toString());
        }
        $this->Output(Config::get("info.common.split0"));
        $allLoan = 0;
        foreach($loans as $loan) {
            $loanProfile = new LoanProfile();
            $loanProfile->CreateFromValue($loan);
            $allLoan += $loanProfile->GetLeftLoan();
        }
        $this->Output("总剩余贷款:".$allLoan);
        $this->Output("总手上现金:".$u->GetCash());

        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("LoanListState::DoOnState $input");

        $u = $this->u;
        $loans = $u->GetLoans();

        if ($input == 't') {
            $cash = $u->GetCash();
            $allLoan = 0;
            foreach($loans as $loan) {
                $loanProfile = new LoanProfile();
                $loanProfile->CreateFromValue($loan);
                $allLoan += $loanProfile->GetLeftLoan();
            }
            if ($allLoan > $cash) {
                $this->Output("对不起，你的钱不足以还完所有贷款");
                return false;
            }
            foreach($loans as $no => $loan) {
                $u->RefundLoanAll($no);
            }

            $msg = MessageState::CreateMessage("全部还款完成","你已经不在有高利息的负债");
            $u->PushMessage($msg);

            $this->m->GotoState('Base');
        }

        if (empty($loans[$input])) {
            $this->Output("没有找到该贷款，请重新输入");
            return false;
        }

        $u->SetFocusLoan($input);
        $this->m->GotoState('LoanRefund');
        return true;
    }
}