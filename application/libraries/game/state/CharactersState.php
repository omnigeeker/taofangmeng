<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午4:02
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Config;
use \Laravel\Log;

class CharactersState extends State {

    public function DoOnState($userName, $input)
    {
        Log::info("CharactersState::DoOnState $input");

        $c = new Character();
        $c->GetFromSelect($input);
        if ($c->IsNULL())
        {
            $this->Output(Config::get('game.character_no'));
            $this->m->GotoSameState();
            return true;
        }
        $u = $this->u;
        $u->CreateFromUser($this->m->GetUserName(), $c);
        $this->SaveToCache();
        //$this->detail = $userProfile->Get();

        // 建立存档
        $this->m->GotoState("City");
        return true;
    }

    public function DoGotoState() {
        Log::info("CharactersState::DoGotoState");

        $this->Output(Config::get("game.character_0"));
        $this->Output(Character::GetGotoMessage());
        return false;
    }

}