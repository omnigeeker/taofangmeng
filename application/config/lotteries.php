<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-6-10
 * Time: 下午5:36
 * To change this template use File | Settings | File Templates.
 */

return array(
    // 小房子
    //  mode- normal 一般罚现金，cashflow 现金流型，house 房子
    array(
        "name" => "买双颜球彩票",
        "describe" => "双颜球彩票是天朝最流行的彩票之一，只需要[first]元买50注，就有机会获得[award]元的\n",
        "first" => 100,
        "award" => 100000000,
        "rate" => 2000000
    ),
    array(
        "name" => "买足球彩票",
        "describe" => "足球彩票是天朝最流行的彩票之一，只需要[first]元买50注，就有机会获得[award]元的\n",
        "first" => 200,
        "award" => 100000000,
        "rate" => 1000000
    ),
    array(
        "name" => "买慈善彩票",
        "describe" => "慈善彩票的本质是大家集资为慈善做贡献，只需要[first]元买50注，就有机会获得[award]元的\n",
        "first" => 1000,
        "award" => 100000000,
        "rate" => 1000000
    ),
    array(
        "name" => "百货公司抽奖",
        "describe" => "某百货公司为了促销，做抽奖活动，只要购物达[first]元，就有机会参加抽奖[award]元，据说几率为1:5",
        "first" => 5000,
        "award" => 10000,
        "rate" => 5
    ),
    array(
        "name" => "百货公司抽奖",
        "describe" => "某百货公司为了促销，做抽奖活动，只要购物达[first]元，就有机会参加抽奖[award]元，据说几率为1:10",
        "first" => 5000,
        "award" => 20000,
        "rate" => 20,
    ),
    array(
        "name" => "超市抽奖",
        "describe" => "某超市做抽奖活动，只要购物达[first]元，就有机会参加抽奖[award]元，几率不大",
        "first" => 500,
        "award" => 10000,
        "rate" => 100,
    ),

    array(
        "name" => "足球下注",
        "describe" => "在国外某足球网站猜比分，你很看好A队，只要下注[first]元给A队用于猜测A队胜利，如果A队如果赢得了比赛你就能获得[award]元，",
        "first" => 5000,
        "award" => 15000,
        "rate" => 3,
    ),

    array(
        "name" => "足球冠军下注",
        "describe" => "在国外某足球网站猜冠军，你很看好A队，只要下注[first]元给A队用于猜测A队夺冠，如果A队如果夺得了冠军你就有机会获得[award]元，",
        "first" => 5000,
        "award" => 20000,
        "rate" => 6,
    ),

    array(
        "name" => "选秀冠军下注",
        "describe" => "在米国某选秀活动中，你看好一位未来之星A，只要下注[first]元给A用于夺冠，如果A最终夺冠，你就有获得[award]元，",
        "first" => 3000,
        "award" => 10000,
        "rate" => 5,
    ),
);