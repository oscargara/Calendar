<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 */


/**
 * Description of ModelDB
 *
 * @author oscar
 */

class core_ModelMysql {
    
    private $_config;

    static $db;

    static $fields = array();

    public function __construct($masters_dsn, $salves_dsn) {
        self::$db = $this;
        $this->_config['master'] = $masters_dsn;
        $this->_config['slave'] = $salves_dsn;

    }

    static function find($obj_type, $field, $keyword='*'){

        if ($keyword=='') return array();

        if (!is_array($keyword)) $keywords = array($keyword);
        else $keywords = $keyword;

        self::setFields($obj_type);

        if (!isset(self::$fields[$obj_type][$field])) return NULL;
        
        $keys = implode("','", $keywords);
        
        if (!in_array("*", $keywords)) $where = "main.{$field} IN ('{$keys}')";
        else $where = '1';

        list($joins, $joins_fields) = self::_createJoins($obj_type);

        $results = array();

        $query = "SELECT main.* $joins_fields FROM {$obj_type} as main {$joins} WHERE {$where} GROUP BY main._id";

        $resource = self::$db->select($query);

        $class_name = 'model_'.$obj_type;
        $result = array();
        while ($resource && $row = mysql_fetch_assoc($resource)) {
             $results[] = new $class_name($row);
        }

        return $results;

    }

    static function findByIndex($obj_type, $obj_id){

        self::setFields($obj_type);
        
        $obj = NULL;

        list($joins, $joins_fields) = self::_createJoins($obj_type);

        $results = array();

        $where = "main._id='{$obj_id}'";

        $resource = self::$db->select("SELECT main.* $joins_fields FROM {$obj_type} as main {$joins} WHERE {$where} GROUP BY main._id");

        $obj = mysql_fetch_assoc($resource);

        $class_name = 'model_'.$obj_type;

        return new $class_name($obj);
    }


    static private function _createJoins($obj_type){
        $joins_a = array();
        $joins_fields_a = array();

        foreach(self::$fields[$obj_type] as $f => $p){
            if ($p['type'] == core_ObjectMysql::TYPE_COLLECTION){
                $foreing_key = "{$p['collection_of']}._{$obj_type}_id";
                $joins_a[] = "LEFT JOIN {$p['collection_of']} ON main._id =  {$foreing_key}";
                $joins_fields_a[] = "GROUP_CONCAT({$p['collection_of']}._id SEPARATOR ',') as {$f}";
            }
        }
        $joins = implode(" \n",$joins_a);
        $joins_fields = implode(',', $joins_fields_a);
        if ($joins_fields!='') $joins_fields = ','.$joins_fields;

        return array($joins, $joins_fields);

    }

    static private function setFields($obj_type){
        $class_name = 'model_'.$obj_type;
        if (!isset(self::$fields[$obj_type])){
            $extended_class =  new $class_name();
            self::$fields[$obj_type] = $extended_class->getFields();
            self::$fields[$obj_type]['_id'] = array('type'=>core_ObjectMysql::TYPE_STRING);
        }
    }

    public function select($sql){
        return $this->_request($sql);
    }

    public function update($obj_type, $values, $where){
        $sql = "UPDATE ".$obj_type. " SET ";

        $sep = '';
        foreach($values as $f=>$v){
            $sql .= "{$sep}{$f}='$v' ";
            $sep = ',';
        }

        $sql .= " WHERE ".$where;

        return $this->_request($sql, TRUE);
    }

    public function insert($obj_type, $values){
        $sql = "INSERT ".$obj_type. " SET ";

        $sep = '';
        foreach($values as $f=>$v){
            $sql .= "{$sep}{$f}='$v' ";
            $sep = ',';
        }

        $this->_request($sql, TRUE);

        return mysql_insert_id();
    }

    public function delete($obj_type, $where){
         $sql = "DELETE FROM ".$obj_type. " WHERE " . $where;
         return $this->_request($sql, TRUE);
    }


    private function _getConfDSN($pos, $master = FALSE){
        if ($master) $dsn = $this->_config['master'][$pos];
        else $dsn = $this->_config['slave'][$pos];
        //"mysql://root:moises@localhost/calendar"
        preg_match("/(.*)\:\/\/(.*)\:(.*)\@(.*)\/(.*)/", $dsn, $m);

        $conf['username'] = $m[2];
        $conf['password'] = $m[3];
        $conf['server']   = $m[4];
        $conf['database'] = $m[5];

        return $conf;
    }

    private function _request($sql, $write = FALSE){
        $conf = $this->_getConfDSN(0, $write);

        $link = mysql_connect($conf['server'], $conf['username'], $conf['password']);
        if (!$link) {
            die('Could not connect: ' . mysql_error());
        }

        mysql_select_db($conf['database']);

        //echo $sql."\n";

        return mysql_query($sql, $link);
        
    }

}
