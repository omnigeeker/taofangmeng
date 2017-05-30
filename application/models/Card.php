<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-9
 * Time: 上午9:37
 * To change this template use File | Settings | File Templates.
 */

abstract class Card {
    protected $me = NULL;

    public function __construct($me)
    {
        $this->me = $me;
    }

    public function Get()
    {
        return $this->me;
    }

    abstract public function GetName();
    abstract public function GetType();
    abstract public function GetDescribe();


    public function __get($varName)
    {
        if (!$this->me)
            return NULL;

        if (!isset($this->me[$varName]))
            return NULL;

        return $this->me[$varName];
    }
}