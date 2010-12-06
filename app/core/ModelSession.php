<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 *
 * MODEL::setDB
Model::find() return list of Objects
Model::findByIndex() return Object
Model::getDB
 */


/**
 * Description of ModelDB
 *
 * @author oscar
 */

class core_ModelSession {

    static private $_db;
    static $fields = array();

    static function setDB(&$db){
        self::$_db = $db;
    }

    static function getDB(){
        return Application::getSession();
    }

    static function find($obj_type, $field, $keyword='*'){

        if ($keyword=='') return array();

        if (!is_array($keyword)) $keywords = array($keyword);
        else $keywords = $keyword;

        $class_name = 'model_'.$obj_type;
        if (!isset(self::$fields[$obj_type])){            
            $extended_class =  new $class_name();
            self::$fields[$obj_type] = $extended_class->getFields();
            self::$fields[$obj_type]['_id'] = array('type'=>core_ObjectSession::TYPE_STRING);
        }

        if (!isset(self::$fields[$obj_type][$field])) return NULL;

        $db = self::getDB();

        $results = array();
        if (isset($db[$obj_type])){
            $collection = $db[$obj_type];

            foreach ($collection as $row){
                if (!in_array("*", $keywords)){
                    if (isset($row[$field]) && in_array($row[$field],$keywords)) $temp = $row;
                    else continue;
                }else{
                    $temp = $row;
                }

                $results[] = new $class_name($temp);
            }
        }

        return $results;
    }

    static function findByIndex($obj_type, $obj_id){
        $db = self::getDB();
        $obj = NULL;

        $class_name = 'model_'.$obj_type;

        if (isset($db[$obj_type]) && isset($db[$obj_type][$obj_id])){
            $obj = $db[$obj_type][$obj_id];
        }
        return new $class_name($obj);
    }
}