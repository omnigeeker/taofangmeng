<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 下午3:52
 * To change this template use File | Settings | File Templates.
 */

class MeState extends State {

    public function DoGotoState() {
        Log::info("BaseState::DoGotoState");

        $u = $this->u;
        $this->Output($u);

        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("BaseState::DoOnState");

        $u = $this->u;

        switch ($input)
        {
            case 'a':
                $this->Output($u->GetDetailString());
                $this->Output(Config::get("info.common.split"));
                $this->Output(Config::get("game.me"));
                break;
            case 'e':
                if ($u->GetCertificateCount() == 0) {
                    $this->Output("你目前还没有获得证书，没法挂证。");
                } else {
                    $this->Output("你挂证每月收入如下");
                    $certificates = $u->GetCertificates();
                    foreach($certificates as $name => $certificate) {
                        $this->Output($name.":".$certificate["income"]);
                    }
                }
                $this->Output(Config::get("info.common.split"));
                $this->Output(Config::get("game.me"));
            default:
                $this->Output("输入错误，请重新输入");
                break;
        }

        return false;
    }

}