<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-7-14
 * Time: 上午12:22
 * To change this template use File | Settings | File Templates.
 */

class StockListState extends State {

    public function DoGotoState() {
        Log::info("StockListState::DoGotoState");

        $u = $this->u;

        $this->Output("选择你要处理的股票或基金");
        $stocks = $u->stocks;
        $no = 0;
        foreach($stocks as $name => $stock) {
            $no += 1;
            $this->Output(SPLIT);
            $this->Output("回复【".$no."】出售");
            $this->Output($stock["type"]." ".$stock["name"]);
            $stocks_market = $u->stocks_market;
            $market_stock = $stocks_market[$name];
            $this->Output("股数:".$stock["count"]);
            $this->Output("购买总价:".$stock["total"]);
            $this->Output("当前股价:".$market_stock["price"]);
            $now_total = round($stock["count"]*$market_stock["price"]);
            $this->Output("当前总价:".$now_total);
        }
        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("StockListState::DoOnState");

        $u = $this->u;
        $m = $this->m;
        $stocks = $u->stocks;

        $value = (int)($input);
        if ($value <= 0)
            return true;

        if ($value > $u->GetStocksCount()) {
            $this->Output("你的输入有误，清重新输入");
            return false;
        }

        $no = 0;
        foreach($stocks as $name => $stock) {
            $no += 1;
            if ($no == $value) {
                $u->SetFocusSaleStock($stock);
                $m->GotoState("StockSale");
                return true;
            }
        }

        assert(false);

        return true;
    }
}