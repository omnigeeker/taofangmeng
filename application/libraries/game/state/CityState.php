<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午4:03
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class CityState extends State {

    public function DoOnState($userName, $input) {
        Log::info("CityState::DoGotoState $input");

        $city = new City();
        if (false == $city->GetFromSelect($input))
        {
            $this->Output(Config::get("game.city_error"));
            return false;
        }
        $u = $this->u;
        $u->SetCity($city->Get());

        $this->Output("欢迎选择城市 ".$city->name."\n");
        $this->m->GotoState('Step');
        return true;
    }
}