<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午2:58
 * To change this template use File | Settings | File Templates.
 */

class State {

    protected $u;
    protected $m;

    public function __construct()
    {
        $this->u = new UserProfile();
    }

    public function setMachine($machine, &$detail)
    {
        $this->m = $machine;
        $this->u->CreateFromValue($detail);
    }

    protected function SaveToCache()
    {
        $this->u->SaveToCache($this->m->detail_key);
    }

    protected function Output($str)
    {
        $this->m->Output($str);
    }

    public function DoOnState($userName, $input)
    {
        return false;
    }

    public function DoGotoState() {
        return false;
    }

    public function __toString()
    {
        return "State";
    }
}