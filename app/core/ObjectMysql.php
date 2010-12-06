<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ObjectMysql
 *
 * @author oscar
 */
abstract class core_ObjectMysql {

    
    private $_data = array();
    private $_data_expanded = array();
    private $_fields = array();

    const TYPE_STRING       = 'string';
    const TYPE_INT          = 'int';
    const TYPE_FLOAT        = 'float';
    const TYPE_DATE         = 'date';
    const TYPE_TIME         = 'time';
    const TYPE_COLLECTION   = 'collection';

    public function __construct($data=array()){
        $this->_data = $data;
        $this->_fields = $this->getFields();
        $this->_fields['_id'] = array('type'=>self::TYPE_STRING);

        foreach($this->_fields as $f=>$p){
            if (isset($this->_data[$f]) && isset($p['collection_of'])){
                if(!$p['unique'])  $this->_data[$f] = explode(',',$this->_data[$f]);
            }
        }

    }

    public function __get($name) {
        if (isset($this->_fields[$name])){
            $p = $this->_fields[$name];
        }else {
            throw new Exception('Field '.$name.' doesn\'t exist');
        }

        if ($p['type']==self::TYPE_COLLECTION){
            if(isset($p['collection_of'])) {

                if (!isset($this->_data_expanded[$name])){

                    $collection_name = $p['collection_of'];

                    if (!isset($this->_data[$name])){
                        if ($p['unique']) return NULL;
                        else return array();
                    }

                    $result = core_ModelMysql::find($collection_name, '_id', $this->_data[$name]);

                    if (is_array($this->_data[$name])) $this->_data_expanded[$name] = $result;
                    else $this->_data_expanded[$name] = current($result);
                }
                return $this->_data_expanded[$name];
            }else{
                throw new Exception('Field '.$name.' doesn\'t have a collection associated');
            }
        }else{
            return $this->_data[$name];
        }
    }


    public function __set($name, $value) {

        if (isset($this->_fields[$name])){
            $p = $this->_fields[$name];
        }elseif(preg_match("/_(.*)/", $name)){
            $p = array('type'=>self::TYPE_INT);
        }else{
            throw new Exception('Field '.$name.' doesn\'t exist');
        }
        if ($p['type']==self::TYPE_COLLECTION){

            if(isset($p['collection_of'])) {
                $collection_name = $p['collection_of'];
            }else{
                throw new Exception('Field '.$name.' doesn\'t have a collection associated');
            }

            if($p['unique']){
               if ("model_".$collection_name == get_class($value)){
                    $this->_data_expanded[$name] = $value;
               } else{
                    throw new Exception('Value must be '.$collection_name);
               }
            }else{
                if (is_array($value)){
                    $results = array();
                    foreach($value as $v){
                        if ("model_".$collection_name == get_class($v)){
                            $results[]=$v;
                        } else{
                           throw new Exception('Value in list must be '.$collection_name);
                        }
                    }
                    $this->_data_expanded[$name] = $results;
                }else{
                    throw new Exception('Value must be an array of '.$collection_name);
                }
            }
        }else{
            $this->_data[$name] = $value;
        }
    }


    public function deleteAssociation($field, $obj_field='', $value=''){

        if ($this->_fields[$field]['type'] != self::TYPE_COLLECTION ) return ;
       
        try{
            
            if ($this->_fields[$field]['unique']){
                unset($this->_data_expanded[$field]);
            }else{
                
                $pos = $this->getObjectPosition($field, $obj_field, $value);
                                
                if (!is_null($pos)){                    
                    $this->_data_expanded[$field][$pos]->delete();
                    if (!is_null($pos)) unset($this->_data_expanded[$field][$pos]);
                }
                
            }
        }catch(Exception $e){
            echo $e->getMessage();
            throw new Exception('Field '.$field.' doesn\'t exist or is not a collection list.');
        }
    }


    public function getObjectPosition($field, $obj_field, $value){
        
        if ($this->_fields[$field]['type'] != self::TYPE_COLLECTION ) return ;

        if ($this->_fields[$field]['unique']) return;

        if (!isset($this->_data_expanded[$field]) || !count($this->_data_expanded[$field])){
            $this->$field; //populate expanded
        }

        if (!isset($this->_data_expanded[$field]) || !count($this->_data_expanded[$field])) return;

        foreach ($this->_data_expanded[$field] as $k=>$child){
            if ($child->$obj_field == $value){
                return $k;
            }
        }
    }


    public function getObject($field, $obj_field, $value){
        $pos = $this->getObjectPosition($field, $obj_field, $value);
        if (!is_null($pos)) return $this->_data_expanded[$field][$pos];
    }

   
    public function  __toString() {
        return print_r($this->_data, TRUE);
    }

    protected function getCollectionName() {
        $class = get_class($this);
        eval('$ret = '.$class.'::$collection_name;');
        return $ret;
    }


    public function save(){
        $collection_name = $this->getCollectionName();
        $db = core_Model::getDB();

        $temp = array_slice($this->_data,0);

        if (isset($this->_data['_id'])){ //update            
            $this->_save_expanded();
            
            foreach ($this->_fields as $f=>$p){
                if ($p['type']==self::TYPE_COLLECTION) unset($temp[$f]);
            }

            $db->update($collection_name, $temp, "_id=".$this->_data['_id']);
            return $temp;
        }else{
            $this->_data['_id'] = $db->insert($collection_name, $temp);
            $this->_save_expanded();
            //print $this;
            return $this->_id;
        }

    }

    private function _save_expanded(){
        $collection_name = $this->getCollectionName();
        if (count($this->_data_expanded)){
                foreach ($this->_data_expanded as $f=>$v){
                    $f_key = "_".$collection_name."_id";
                    if (is_array($v)){
                        $new_children = array();
                        foreach ($v as $child){                           
                            $child->$f_key = $this->_data['_id'];
                            $r = $child->save();
                            $new_children[] = $r['_id'];
                        }
                    }else{
                        $v->$f_key = $this->_data['_id'];
                        $r = $v->save();
                    }
                }
            }
    }

    public function delete(){
        
        if ($this->_id){
            $collection_name = $this->getCollectionName();
            $db = core_Model::getDB();            
            $db->delete($collection_name, "_id='{$this->_id}'");
        }
    }

/*
    static public function __callStatic($name, $arguments) {
        try{
            $collection_name = static::$collection_name;
            array_unshift($arguments, $collection_name);
            return call_user_func_array('core_ModelMysql::'.$name, $arguments);
        }catch(Exception $e){
            throw new Exception('Function '.$name.' doesn\'t exist');
        }
    }
*/



    static function callStatic($name, $arguments){
        preg_match("/(.*)::(.*)/", $name, $match);
        $class = $match[1];
        $name = $match[2];
        try{
            eval('$collection_name = '.$class.'::$collection_name;');
            array_unshift($arguments, $collection_name);
            return call_user_func_array('core_ModelMysql::'.$name, $arguments);
        }catch(Exception $e){
            throw new Exception('Function '.$name.' doesn\'t exist');
        }
    }

}