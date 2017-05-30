<?php

use \Laravel\Response;

define("TOKEN", "elninowang");

class Weixin_Controller extends Base_Controller {

    /*
    |--------------------------------------------------------------------------
    | The Default Controller
    |--------------------------------------------------------------------------
    |
    | Instead of using RESTful routes and anonymous functions, you might wish
    | to use controllers to organize your application API. You'll love them.
    |
    | This controller responds to URIs beginning with "home", and it also
    | serves as the default controller for the application, meaning it
    | handles requests to the root of the application.
    |
    | You can respond to GET requests to "/home/profile" like so:
    |
    |		public function action_profile()
    |		{
    |			return "This is your profile!";
    |		}
    |
    | Any extra segments are passed to the method as parameters:
    |
    |		public function action_profile($id)
    |		{
    |			return "This is the profile for user {$id}.";
    |		}
    |
    */
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    public function action_index()
    {
        // valid
        //$this->valid();

        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = (string)$postObj->FromUserName;
            $toUsername = (string)$postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();

            $homeMachine = new HomeMachine();

            $homeMachine->OnState($fromUsername, $keyword);

            $content = $homeMachine->GetResult();

            $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>0</FuncFlag>
</xml>";

            $msgType = "text";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $content);

            return $resultStr;
        }
        else
        {
            return Response::error('404');
        }

    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

}