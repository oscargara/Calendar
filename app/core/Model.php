<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 */

/**
 * Description of Model
 *
 * @author oscar
 */
final class core_Model{

    static $_db;

    static function initDB($masters_dsn, $salves_dsn){
        if (is_null(self::$_db)) self::$_db = new core_ModelMysql($masters_dsn, $salves_dsn);
        else throw new Exception('You can instance more than one DB.');
    }

    static function getDB(){
        return self::$_db;
    }

}