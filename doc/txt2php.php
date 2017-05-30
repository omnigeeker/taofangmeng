<?php
    $input = "cities.txt";
    $in_file = fopen($input, "r");//读取二进制文件时，需要将第二个参数设置成'rb'
    
    $output = "cities.php";
    $out_file = fopen($output, "w");//读取二进制文件时，需要将第二个参数设置成'rb'
    
    //通过filesize获得文件大小，将整个文件一下子读到一个字符串中
//    $contents = fread($handle, filesize ($filename));

    $buffer = fgets($in_file, 1024);
    $buffer = iconv('utf-8','gb2312',$buffer);
    $columns = preg_split ("/\s+/", $buffer);
	foreach ($columns as $column) {
	   print $column;
	   print " "; 
	}
	print "\n";
    
    fputs($out_file, "return array(\n");
    while(!feof($in_file)){
//    	
    	$buffer = fgets($in_file, 4096);
    	$buffer = iconv('utf-8','gb2312',$buffer);
    	print $buffer;
//    	
    	if (!strncmp($buffer, "**********", 10)) {
    		print "break";
    		break;
    	}
    	
    	/**
    	 * 
    'city010' => array(
        "name" => "帝都",
        "rate" => 1.1,
        "price" => 26000,
    ),
    	 * 
    	 * Enter description here ...
    	 * @var unknown_type
    	 */
    	$splits = preg_split ("/\s+/", $buffer);
    	
//    	foreach ($splits as $split1) {
//	   		print $split1;
//	   		print " ";
//		}
//		print "'$splits[0]' => array(\n";
//		$len=count($splits);
//		for ($i = 1; $i < $len; $i++) {
//			print "   \"$columns[$i]\" => \"$splits[$i]\",\n";			
//		}

    	$split_utf8 = iconv('utf-8','gb2312',$splits[0]);
		fprintf($out_file, "    '%s' => array(\n", $split_utf8) ;
		$len=count($splits);
		for ($i = 1; $i < $len; $i++) {
			$solumn_utf8 = iconv('gb2312','utf-8',$columns[$i]);
			$split_utf8 = iconv('gb2312','utf-8',$splits[$i]);
			fprintf($out_file, "        \"%s\" => \"%s\",\n", $solumn_utf8, $split_utf8) ;	
		}
		fprintf($out_file, "    ),\n") ;
    }
    fputs($out_file, ");\n");
    
    fclose($in_file);
    fclose($out_file);
?>