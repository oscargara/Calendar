<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Calendar
 *
 * @author oscar
 */
class model_Calendar extends core_ObjectMysql{

    static $collection_name = 'Calendar';

    public function getFields(){
        $fields['year']          = array('type'=>core_ObjectMysql::TYPE_DATE);
        $fields['creation_date'] = array('type'=>core_ObjectMysql::TYPE_TIME);
        $fields['months']        = array('type'=>core_ObjectMysql::TYPE_COLLECTION, 'collection_of'=>'Month', 'unique'=>FALSE);
        return $fields;
    }

    static public function getCanadianHolidays(){

        $list = array();
        $list[] = array('name'=>'New Year\'s Day',  'date'=>'01-01-2011');
        $list[] = array('name'=>'Good Friday',      'date'=>'22-03-2011');
        $list[] = array('name'=>'Easter Monday',    'date'=>'25-03-2011');
        $list[] = array('name'=>'Victoria Day',     'date'=>'23-05-2011');
        $list[] = array('name'=>'Canada Day',       'date'=>'01-07-2011');
        $list[] = array('name'=>'Labour Day',       'date'=>'05-08-2011');
        $list[] = array('name'=>'Thanksgiving Day', 'date'=>'10-10-2011');
        $list[] = array('name'=>'Remembrance Day',  'date'=>'11-11-2011');
        $list[] = array('name'=>'Christmas',        'date'=>'12-25-2011');
        $list[] = array('name'=>'Boxing Day',       'date'=>'12-26-2011');

        return $list;
    }

    public function putYearHolidays(){
        foreach ($this->months as $month){
            $this->putMonthHolidays($month);
        }
    }

    public function putMonthHolidays(&$month){

        $month_number = preg_replace("/(.*)\-(.*)\-(.*)/", "$2",$month->date);
        $year_number = preg_replace("/(.*)\-(.*)\-(.*)/", "$3",$month->date);

        foreach (self::getCanadianHolidays() as $holiday){
            $h_day = preg_replace("/(.*)\-(.*)\-(.*)/", "$1", $holiday['date']);
            $h_month = preg_replace("/(.*)\-(.*)\-(.*)/", "$2", $holiday['date']);
            $h_year = preg_replace("/(.*)\-(.*)\-(.*)/", "$3", $holiday['date']);

            if ($month_number==$h_month && $year_number==$h_year){
                $month->addNote(new model_DayNotes(array('month_day'=>$h_day, 'note'=>$holiday['name'])));
            }
        }
    }
    
    static function find(){$arg_list = func_get_args(); return parent::callStatic(__METHOD__, $arg_list);}
    static function findByIndex(){$arg_list = func_get_args(); return parent::callStatic(__METHOD__, $arg_list);}
}