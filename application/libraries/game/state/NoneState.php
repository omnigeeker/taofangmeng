<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午3:40
 * To change this template use File | Settings | File Templates.
 */

class NoneState extends State {

    public function DoGotoState() {
        $this->Output(Config::get("game.none_0"));
        return false;
    }

}