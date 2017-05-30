<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 下午3:52
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class LoanState extends State {

    public function DoGotoState() {
        Log::info("LoanState::DoGotoState");

        $this->Output(Config::get("game.loan_header"));
        $max = $this->u->GetMaxLowerLoan();

        $max_wan = floor($max/10000);

        $this->Output("你的最高贷款额度为".$max_wan."万\n");

        $this->Output("回复\"meng\"梦梦提示");

        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("LoanState::DoOnState $input");

        if ($input == "meng" || $input == "梦梦") {
            $this->Output("[梦梦提示]贷款每个月都要还，注意不要让自己的现金流为负。无节制地使用高利息的贷款，很容易导致破产。".
                $this->u->GetCashFlow()."\n");
            $this->Output("回复\"now\"继续游戏");
            return true;
        }

        $value = (int)($input);
        if ($value == 0)
            return true;

        $max = $this->u->GetMaxLowerLoan();
        $max_wan = floor($max/10000);
        if ($value <= 0)
        {
            $this->Output("你的输入有误，清重新输入", $max_wan);
            return false;
        }
        if ($value > $max_wan)
        {
            $this->Output("你的输入超出了你贷款的最大限额", $max_wan);
            return false;
        }

        $this->u->LoanLower($value * 10000);

        $this->Output("你成功向天朝银行无抵押贷款 $value 万");

        $this->m->GotoState('Base');

        return false;
    }

}