<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-3
 * Time: 下午4:41
 * To change this template use File | Settings | File Templates.
 */

define("HOUSE_DOWN_RATE", 0.3);
define("HOUSE_LOAN_RATE", 0.0705*0.85);
define("SHOP_DOWN_RATE", 0.5);
define("SHOP_LOAN_RATE", 0.0705);

use \Laravel\Config;

class BegginerGameMachine extends GameMachine
{
    public function __construct()
    {
        parent::__construct("beginner_game", 'None');

        $stateInfos = array(
            'None' => array(
                "inputs" => array(
                    "a" => 'Characters',
                    "q" => "home",
                ),
            ),
            'Characters' => array(
                "inputs" => array(
                    // "1" => 选择角色1
                    // “2” => 选择角色2
                    // ...
                    // "x" => 选择角色x
                    "q" => 'None',
                ),
            ),
            'City' => array(
                "inputs" => array(
                    "q" => 'Characters',
                ),
            ),
            'Step' => array(
                "inputs" => array(
                    "a" => 'Base',
                ),
            ),
            'Base' => array(
                "inputs" => array(
                ),
            ),

            'HouseBase' => array(
                "inputs" => array(
                    "q" => 'Base'
                ),
            ),
            'HouseChance' => array(
                "inputs" => array(
                    "q" => 'Base',
                ),
            ),
            'HouseChanceLoan' => array(
                "inputs" => array(
                    "q" => "Base",
                ),
            ),

            'ShopBase' => array(
                "inputs" => array(
                    "q" => 'Base'
                ),
            ),
            'ShopChance' => array(
                "inputs" => array(
                    "q" => 'Base',
                ),
            ),
            'ShopChanceLoan' => array(
                "inputs" => array(
                    "q" => 'Base',
                ),
            ),
            'StockChance' => array(
                "inputs" => array(
                    "q" => 'Base',
                ),
            ),
            'StockList' => array(
                "inputs" => array(
                    "q" => "Base",
                ),
            ),
            'StockSale' => array(
                "inputs" => array(
                    "q" => "StockList",
                ),
            ),
            'LicaiChance' => array(
                "inputs" => array(
                    "q" => 'Base',
                ),
            ),
            'CertificateChance' => array(
                "inputs" => array(
                    "q" => 'Base',
                ),
            ),
            'Lottery' => array(
                "inputs" => array(
                    "q" => 'Base',
                ),
            ),
            'OtherBase' => array(
                "inputs" => array(
                    "q" => 'Base',
                    "a" => 'Me',
                    "b" => 'AssetsList',
                    "c" => 'Loan',
                    "d" => 'LoanList',
                    "e" => 'StockList',
                )
            ),
            'Me' => array(
                "inputs" => array(
                    "q" => 'Base',
                    "b" => 'MeEstate',
                    "c" => 'MeLoan',
                    "d" => 'MeLicai',
                )
            ),
            'MeEstate' => array(
                "inputs" => array(
                    "q" => 'Me',
                ),
            ),
            'MeLoan' => array(
                "inputs" => array(
                    "q" => 'Me',
                ),
            ),
            'MeLicai' => array(
                "inputs" => array(
                    "q" => 'Me',
                ),
            ),
            'Loan' => array(
                "inputs" => array(
                    "q" => 'OtherBase',
                ),
            ),
            'LoanList' => array(
                "inputs" => array(
                    "q" => "OtherBase"
                ),
            ),
            'LoanRefund' => array(
                "inputs" => array(
                    "q" => "LoanList",
                ),
            ),
            'Accident' => array(
                "inputs" => array(
                ),
            ),
            'AssetsList' => array(
                "inputs" => array(
                    "q" => 'OtherBase',
                ),
            ),
            'AssetsHandle' => array(
                "inputs" => array(
                    "q" => 'AssetsList',
                ),
            ),
            'AssetsRefund' => array(
                "inputs" => array(
                    "q" => 'AssetsList',
                ),
            ),
            'Sale' => array(
                "inputs" => array(
                   "q" => 'Base',
                ),
            ),
            'End' => array(
                "inputs" => array(
                    "a" => 'None',
                ),
            ),
            'Succeed' => array(
                "inputs" => array(
                    "a" => 'None',
                ),
            ),
            'Message' => array(
                "inputs" => array(
                    "a" => 'Base',
                )
            ),
        );

        //赋值 "message" 和 "input_error"
        foreach($stateInfos as $state => $stateInfo)
        {
            $temp = Config::get("game.$state");
            $stateInfos[$state]["message"] = $temp;
        }

        $this->InitStates($stateInfos);
    }

    protected function DoOnState($userName, $input)
    {
        if ($input === 'help' || $input === '帮助')
        {
            $this->Output(Config::get('game.succeed_condition'));
            $this->Output(Config::get('info.common.split'));
            $this->Output(Config::get('game.help'));
        }
        if ($input === 'now' || $input === '旧的回忆') {
            $detail = &$this->detail;
            if (!isset($detail['step'])) {
                $this->Output("你没有游戏纪录，请回复新的【新的开始】");
                return;
            }
            $this->GotoSameState();
            return;
        }

        $detail = &$this->detail;
        if (isset($detail['version']))
        {
            $version = $detail['version'];
            if ($version < PROFILE_VERSION)
            {
                $this->Output(Config::get("game.upgrade"));
                return;
            }
        }

        if ($input === 'me' ||
            $input === 'all' ||
            $input === 'killmyself' ||
            $input === 'showmethemoney' ||
            $input === 'unemployed' ||
            $input === 'var_dump' ||
            $input === 'goto_victory')
        {
            if (!isset($detail['version']))
            {
                $this->Output("目前档案还没有建立成功，不能察看自我信息");
                return;
            }

            $u = new UserProfile();
            $u->CreateFromValue($detail);
            if ($input === 'me') {
                $this->Output($u);
            }else if ($input === 'all') {
                $this->Output($u->GetDetailString());
            }else if ($input === 'killmyself') {
                $u->AddCash(-10000000);
                $this->Output("你的现金减少了1000万");
                $u->SaveToCache($this->detail_key);
            } else if ($input === 'showmethemoney') {
                $u->AddCash(10000000);
                $this->Output("你的现金增加了1000万");
                $u->SaveToCache($this->detail_key);
            } else if ($input === 'unemployed') {
                $u->SetUnemployed();
                $this->Output("你人工失业了");
                $u->SaveToCache($this->detail_key);
            } else if ($input === 'var_dump') {
                var_dump($u->Get());
            } else if ($input === 'goto_victory') {
                $this->GotoState('Succeed');
            }
            return;
        }

        parent::DoOnState($userName, $input);
    }
}