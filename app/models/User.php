<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author oscar
 */
class model_User extends core_ObjectMysql{

    static $collection_name = 'User';

    public function getFields(){
        $fields['firstname'] = array('type'=>core_ObjectMysql::TYPE_STRING);
        $fields['lastname']  = array('type'=>core_ObjectMysql::TYPE_STRING);
        $fields['username']  = array('type'=>core_ObjectMysql::TYPE_DATE);
        $fields['password']  = array('type'=>core_ObjectMysql::TYPE_STRING);
        $fields['calendar']  = array('type'=>core_ObjectMysql::TYPE_COLLECTION, 'collection_of'=>'Calendar', 'unique'=>TRUE);
        return $fields;
    }

    static public function getCurrentUser(){

        $session = Application::getSession();

        $user_id = $session['session_id'];

        $user_data = model_User::find('username', $user_id);

        if (count($user_data)) return current($user_data);
        else return NULL;
    }



    static function find(){$arg_list = func_get_args(); return parent::callStatic(__METHOD__, $arg_list);}
    static function findByIndex(){$arg_list = func_get_args(); return parent::callStatic(__METHOD__, $arg_list);}

}