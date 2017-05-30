<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-7-27
 * Time: 上午11:10
 * To change this template use File | Settings | File Templates.
 */

define("LINE_MAX", 18);

class util {
	 protected static $spaces = array(
            	"",
            	" ",
            	"  ",
            	"   ",
            	"    ",
            	"     ",
            	"      ",
            	"       ",
            	"        ",
                "         ",
                "          ",
                "           ",
            	"            ",
            	"             ",
            	"              ",
            	"               ",
            	"                ",
            	"                 ",
            	"                  ",
            );
    static public function create_guid() {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = "" //chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        //    .chr(125);// "}"
        return $uuid;
    }
    
    public static function FormatLine($name, $value)
    {
    	//": " take 2 spaces
    	//1 Chinese char take 3 spaces
    	//1 digital char take 1 space
    	$value_len = strlen(''.$value);
    	$len = strlen($name) * 2 / 3 + 2 + $value_len;

    	if ($len > LINE_MAX) {
    		$prefix = LINE_MAX - $value_len;
    		/**
    		 * 理财产品理财：   
    		 *         1234
    		 * 理财产品理财：   
    		 *         1234
    		 */
    		return $name.": \n".util::$spaces[$prefix].$value;
    	} else {
    		$prefix = LINE_MAX - $len;
    		/**
    		 * 理财产品：     1234   
    		 * 理财产品：        234
    		 * 理财产品：112234     
    		 */
    		
//    		var_dump($me["spaces1"][$prefix]);
//    		exit;
    		return $name.": ".util::$spaces[$prefix].$value;
    	}
    }
}