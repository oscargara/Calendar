<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Picture
 *
 * @author oscar
 */
class model_DayNotes extends core_ObjectMysql{

    static $collection_name = 'DayNotes';

    public function getFields(){
        $fields['month_day']    = array('type'=>core_ObjectMysql::TYPE_INT);
        $fields['note']         = array('type'=>core_ObjectMysql::TYPE_STRING);
        return $fields;
    }


    static function find(){$arg_list = func_get_args(); return parent::callStatic(__METHOD__, $arg_list);}
    static function findByIndex(){$arg_list = func_get_args(); return parent::callStatic(__METHOD__, $arg_list);}

}