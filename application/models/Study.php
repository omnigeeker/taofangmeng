<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-16
 * Time: 下午4:59
 * To change this template use File | Settings | File Templates.
 */

abstract class Study
{
    protected $me = NULL;

    public function __construct($me)
    {
        $this->me = $me;
    }

    public function Get()
    {
        return $this->me;
    }

    public function __get($varName)
    {
        if (!$this->me)
            return NULL;

        if (!isset($this->me[$varName]))
            return NULL;

        return $this->me[$varName];
    }

    abstract public function GetName();
    abstract public function GetFirst();
    abstract public function GetMonths();


    static public function CreateStudyByType($type, $body) {
        switch ($type)
        {
            case "certificate":
                return new Certificate($body);
            default:
                return NULL;
        }
    }
}