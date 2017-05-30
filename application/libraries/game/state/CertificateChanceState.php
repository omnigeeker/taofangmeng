<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-16
 * Time: 下午5:53
 * To change this template use File | Settings | File Templates.
 */

class CertificateChanceState extends State {

    public function DoGotoState() {
        Log::info("CertificateChanceState::DoGotoState");

        $u = $this->u;

        $this->Output(Config::get("game.certificatechance_0"));
        $certificate = new Certificate($u->GetFocusCertificate());
        $this->Output($certificate);

        $certificates = $u->GetCertificates();
        if (isset($certificates[$certificate->name])) {
            $this->Output(Config::get("game.certificatechance_q"));
            return true;
        }
        $this->Output("回复\"meng\"梦梦提示");
        return false;
    }

    public function DoOnState($userName, $input) {
        Log::info("CertificateChanceState::DoOnState $input");

        $u = $this->u;
        $m = $this->m;

        if ($input == "meng" || $input == "梦梦") {
            $this->Output("[梦梦提示]挂证产生的收入属于不工作的收入，将纪录为被动收入,另外挂证收入很受国家政策影响。");
            $this->Output("回复\"now\"继续游戏");
            return true;
        }

        $certificate = new Certificate($u->GetFocusCertificate());

        if ($input === 'b')
        {
            $this->Output(Config::get("game.certificatechance_cannot_loan"));
            return true;
        }
        else if ($input === 'a')
        {
            if ($u->GetCash() < $certificate->first)
            {   //钱不够
                $this->Output(Config::get("game.certificatechance_cannot_by"));
                return true;
            }

            $u->StartStudy($certificate);

            $m->GotoState('Base');
            return false;
        }
    }
}