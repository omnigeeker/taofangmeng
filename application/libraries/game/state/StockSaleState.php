<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-7-14
 * Time: 上午10:57
 * To change this template use File | Settings | File Templates.
 */

class StockSaleState extends State {
    public function DoGotoState() {
        Log::info("StockSaleState::DoGotoState");

        $u = $this->u;

        $saleStock = $u->GetFocusSaleStock();

        $name = $saleStock["name"];
        $stocks_market = $u->stocks_market;
        $market_stock = $stocks_market[$name];

        $this->Output("你要出售的股票或基金");
        $this->Output("股数:".$saleStock["count"]);
        $this->Output("购买总价:".$saleStock["total"]);
        $this->Output("购买平均估价:".number_format($saleStock["total"]/$saleStock["count"], 2));
        $this->Output(SPLIT0);
        $this->Output("当前股价:".$market_stock["price"]);
        $now_total = round($saleStock["count"]*$market_stock["price"]);
        $this->Output("当前总价:".$now_total);
        $this->Output(SPLIT0);
        $incresement = $now_total - $saleStock["total"];
        if ($incresement > 0)
            $this->Output("赚:".$incresement);
        else
            $this->Output("亏:".$incresement);
        $this->Output("投机收益率:".round(100*$incresement/$saleStock["total"]).'%');
        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("StockSaleState::DoOnState");

        $u = $this->u;
        $m = $this->m;

        $saleStock = $u->GetFocusSaleStock();

        $name = $saleStock["name"];
        $stocks_market = $u->stocks_market;
        $market_stock = $stocks_market[$name];

        if ($input == "t") {
            $cash = $u->SaleStockAll($name);
        } else {
            $value = (int)($input);
            if ($value <= 0)
                return true;

            $count = 100*floor($value/($market_stock["price"]*100));
            if($count == 0) {
                $this->Output("你出售的股票或者基金必须是100的整数倍，你输入的金额不够100股，清重新输入");
                return false;
            }
            $cash = $u->SaleStock($name, $count);
        }
        $this->Output("成功出售".$saleStock["type"]." ".$name." 获得".$cash."元");
        $this->Output(SPLIT0);
        $m->GotoState('Base');

        return true;
    }
}