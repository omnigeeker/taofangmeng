<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-5-2
 * Time: 下午11:34
 * To change this template use File | Settings | File Templates.
 */

use \Laravel\Response;

class Test_Controller extends Base_Controller {

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

    public function action_index()
    {
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

            return $content;
        }
        else
        {
            return Response::error('404');
        }

    }
}