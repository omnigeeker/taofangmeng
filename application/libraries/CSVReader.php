<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-7-25
 * Time: 下午9:48
 * To change this template use File | Settings | File Templates.
 */

class CSVReader {

    public static function isTrueFloat($val)
    {
        ////$pattern = '/^[-+]?(((\\\\d+)\\\\.?(\\\\d+)?)|\\\\.\\\\d+)([eE]?[+-]?\\\\d+)?$/';
        $pattern = '/^[+|-]?[0-9]*[.][0-9]+$/';
        return preg_match($pattern, trim($val));
    }

    public static function isTrueInt($val)
    {
        ////$pattern = '/^[-+]?(((\\\\d+)\\\\.?(\\\\d+)?)|\\\\.\\\\d+)([eE]?[+-]?\\\\d+)?$/';
        $pattern = '/^[+|-]?[0-9]+$/';
        return preg_match($pattern, trim($val));
    }

    public static function GetMapFromCSV($csv_file_name) {
        $csvPath = $GLOBALS["laravel_paths"]["app"]."csv/";
        $fileName = $csvPath.$csv_file_name;

        $row = 0;
        $header = array();
        $num = 0;
        $result = array();
        if (($handle = fopen($fileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row == 0) {
                    $num = count($data);
                    for ($i=0; $i < $num; $i++) {
                        $header[$i] = trim($data[$i]);
                    }
                    $header[0] = "key";
                } else {
                    if ($num != count($data)) {
                        continue;
                    }
                    $key = trim($data[0]);
                    if ($key == "") {
                        continue;
                    }
                    $item = array();
                    for ($i = 0; $i < $num; $i++) {
                        $value = $data[$i];
                        if (CSVReader::isTrueInt($value))
                            $value = intval($value);
                        else if (CSVReader::isTrueFloat($value))
                            $value = floatval($value);
                        $item[$header[$i]] = $value;
                    }
                    $result[$key] = $item;
                }
                $row++;
            }
            fclose($handle);
            return $result;
        } else {
            return array();
        }
    }

    public static function GetArrayFromCSV($csv_file_name) {
        $csvPath = $GLOBALS["laravel_paths"]["app"]."csv/";
        $fileName = $csvPath.$csv_file_name;

        $row = 0;
        $header = array();
        $num = 0;
        $result = array();
        if (($handle = fopen($fileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $key = trim($data[0]);
                if ($key == "") {
                    continue;
                }
                if ($row == 0) {
                    $num = count($data);
                    for ($i=0; $i < $num; $i++) {
                        $header[$i] = trim($data[$i]);
                    }
                    //暂时写死
                    $header[0] = "key";
                } else {
                    if ($num != count($data)) {
                        continue;
                    }
                    $item = array();
                    for ($i = 0; $i < $num; $i++) {
                        $value = $data[$i];
                        if (CSVReader::isTrueInt($value))
                            $value = intval($value);
                        else if (CSVReader::isTrueFloat($value))
                            $value = floatval($value);
                        $item[$header[$i]] = $value;
                    }
                    array_push($result, $item);
                    //$result[$key] = $item;
                }
                $row++;
            }
            fclose($handle);
            return $result;
        } else {
            return array();
        }
    }

    public static function GetYearsMapFromCSV($csv_file_name) {
        $csvPath = $GLOBALS["laravel_paths"]["app"]."csv/";
        $fileName = $csvPath.$csv_file_name;

        $is_first = true;
        $result = array();
        if (($handle = fopen($fileName, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($is_first == true) {
                    // 地0行葫芦额
                    $is_first = false;
                    continue;
                } else {
                    $num = count($data);
                    if ($num != 31) {
                        continue;
                    }
                    $key = trim($data[0]);
                    if ($key == "") {
                        continue;
                    }
                    $item = array();
                    for ($i = 1; $i < $num; $i++) {
                        array_push($item, floatval($data[$i]));
                    }
                    $result[$key] = $item;
                }
            }
            fclose($handle);
            return $result;
        } else {
            return array();
        }
    }
}