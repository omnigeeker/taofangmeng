<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elnino
 * Date: 13-8-10
 * Time: 下午1:43
 * To change this template use File | Settings | File Templates.
 */

class Knowledge {

    public static function GetKnowledges()
    {
        $knowledges = CSVReader::GetArrayFromCSV("knowledge.csv");
        $knowledge_map = array();
        foreach ($knowledges as $knowledge) {
            $knowledge_map[$knowledge["name"]] = $knowledge;
        }
        return $knowledge_map;
    }

    public static function GetKnowledgeByName($name)
    {
        $knowledge_map = Knowledge::GetKnowledges();
        if (isset($knowledge_map[$name]))
            return $knowledge_map[$name];
        return NULL;
    }
}