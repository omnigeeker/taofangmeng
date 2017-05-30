<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-8
 * Time: 下午11:52
 * To change this template use File | Settings | File Templates.
 */

class ShopProfile extends RealEstateProfile
{

    public function CreateFromShop($focusShop, $step)
    {
        parent::CreateNew($focusShop, $step);
    }
}