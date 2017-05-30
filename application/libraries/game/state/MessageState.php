<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午10:19
 * To change this template use File | Settings | File Templates.
 */

class MessageState extends State {

    static public function CreateMessage($title, $context, $next=NULL)
    {
        if ($next == NULL) {
            return array(
                "title" => $title,
                "context" => $context,
            );
        }

        return array(
            "title" => $title,
            "context" => $context,
            "next" => $next,
        );
    }

    public function DoGotoState() {
        Log::info("MessageState::DoGotoState");

        $u = $this->u;
        $m = $this->m;

        if ($u->GetMessageCount() == 0)
        {
            $m->GotoState('Base');
            return true;
        }

        $msg = $u->PopMessage();

        $this->Output($msg["title"]);
        $this->Output($msg["context"]);
        $this->Output(Config::get("info.common.split"));
        if (empty($msg["next"]))
            $this->Output("【a】我知道了，继续吧");
        else $this->Output("【a】".$msg['next']);
        return false;
    }
}