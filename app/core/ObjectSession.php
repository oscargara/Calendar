<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 * __construtor($data)
Calendar->id,name, year...
Calendar->months (COLLECTION) lazy var
Calendar->save() //load //delete
Calendar::find() (Model::find())
 */

/**
 * Description of ObjectSession
 *
 * @author oscar
 */
abstract class  core_ObjectSession {

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

                    $result = core_ModelSession::find($collection_name, '_id', $this->_data[$name]);

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
        }else {
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
                if (!is_null($pos)) unset($this->_data_expanded[$field][$pos]);               
            }
        }catch(Exception $e){
            throw new Exception('Field '.$field.' doesn\'t exist or is not a collection list.');
        }
    }


    public function getObjectPosition($field, $obj_field, $value){
        if ($this->_fields[$field]['type'] != self::TYPE_COLLECTION ) return ;

        if ($this->_fields[$field]['unique']) return;

        if (!isset($this->_data_expanded[$field]) || !count($this->_data_expanded[$field])){
            $this->$field;
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

    static public function __callStatic($name, $arguments) {
        try{
            $collection_name = static::$collection_name;
            array_unshift($arguments, $collection_name);
            return call_user_func_array('core_ModelSession::'.$name, $arguments);
        }catch(Exception $e){
            throw new Exception('Function '.$name.' doesn\'t exist');
        }
    }

    public function  __toString() {
        return print_r($this->_data, TRUE);
    }

    protected function getCollectionName() {
        return static::$collection_name;        
    }


    public function save(){
        $collection_name = $this->getCollectionName();
        $db = self::getDB();

        $temp = $this->_data;

        if (isset($this->_data['_id'])){ //update
            if(!isset($db[$collection_name]))  $db[$collection_name] = array();

            if (count($this->_data_expanded)){
                foreach ($this->_data_expanded as $f=>$v){
                    if (is_array($v)){
                        $new_children = array();
                        foreach ($v as $child){
                            $r = $child->save();
                            $new_children[] = $r['_id'];
                        }
                        $temp[$f] = $new_children;
                    }else{
                        $r = $v->save();
                        $temp[$f] = $r['_id'];
                    }
                }
            }
             Application::setSession(array($collection_name, $this->_data['_id']), $temp);
            return $temp;
        }else{
            $this->_data['_id'] = $collection_name.'-'.time().sprintf("%05s",rand(0,1000));
            return $this->save();
        }

    }

    public function delete(){
        if (isset($this->data['_id'])){
            $db = self::getDB();
            Application::deleteSession(array($collection_name, $this->_data['_id']));
        }
    }


}
