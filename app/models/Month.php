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
class model_Month extends core_ObjectMysql{

    static $collection_name = 'Month';

    public function getFields(){
        $fields['date']         = array('type'=>core_ObjectMysql::TYPE_DATE);
        $fields['name']         = array('type'=>core_ObjectMysql::TYPE_STRING);
        $fields['filepath']     = array('type'=>core_ObjectMysql::TYPE_STRING);
        $fields['creation_date']= array('type'=>core_ObjectMysql::TYPE_DATE);
        $fields['url']          = array('type'=>core_ObjectMysql::TYPE_STRING);
        $fields['date_notes']   = array('type'=>core_ObjectMysql::TYPE_COLLECTION, 'collection_of'=>"DayNotes", 'unique'=>FALSE);
        return $fields;
    }

    public function addNote(model_DayNotes $note){
        $this->deleteAssociation("date_notes", "month_day", intval($note->month_day));
        $temp = $this->date_notes;
        array_push($temp, $note);
        $this->date_notes = $temp;        
    }

    public function getNote($month_day){
        $pos = $this->getObjectPosition("date_notes", 'month_day', intval($month_day));
        if (!is_null($pos)) return $this->date_notes[$pos];
    }

    public function getMonthsDays(){

        $cMonth = preg_replace("/(.*)\-(.*)\-(.*)/", "$2", $this->date);

        $cYear = preg_replace("/(.*)\-(.*)\-(.*)/", "$3", $this->date);

        $prev_year = $cYear;
        $next_year = $cYear;

        $prev_month = $cMonth - 1;
        $next_month = $cMonth + 1;

        if ($prev_month == 0) {
            $prev_month = 12;
            $prev_year = $cYear - 1;
        }
        if ($next_month == 13) {
            $next_month = 1;
            $next_year = $cYear + 1;
        }

        $lastmaxday = date("t", mktime(0, 0, 0, $prev_month, 1, $prev_year));
        $nextmaxday = date("t", mktime(0, 0, 0, $next_month, 1, $next_year));

        $timestamp = mktime(0, 0, 0, $cMonth, 1, $cYear);
        $maxday = date("t", $timestamp);

        $thismonth = getdate($timestamp);
        $startday = $thismonth['wday'];

        $num_boxes = ($maxday + $startday)%7==0 ? ($maxday + $startday): intval(($maxday + $startday)/7 + 1)*7;

        $data = array();
        $r = 0;

        for ($i = 0; $i < $num_boxes; $i++) {

            if (($i % 7) == 0) ++$r;

            $txt = array('day'=>'', 'outday'=>'', 'note'=>' ');

            $note = "";
            if($i >= $startday && $maxday>($i-$startday)){
                $j = ($i - $startday + 1);
                $txt['day'] = $j;
                $n = $this->getNote($j);
                if (!is_null($n) && $n->note!='') $txt['note'] = $n->note;

            }elseif($i<$startday && $r<=5){
                $txt['outday'] =($lastmaxday+($i-$startday)+1);
            }elseif($r<=5){
                $txt['outday'] = (($i-$startday) - $maxday)+1;
            }

           if ($r>5 && $txt['day']!=''){
                $data[5][$i % 7]["day"] .= "/".$txt['day'];
                $data[5][$i % 7]["note"] .= "\n".$txt['note'];
            }elseif($r>5){
                continue;
            }
            else{
                $data[$r][$i % 7] = $txt;
            }
        }
        return $data;
    }


    static function find(){$arg_list = func_get_args(); return parent::callStatic(__METHOD__, $arg_list);}
    static function findByIndex(){$arg_list = func_get_args(); return parent::callStatic(__METHOD__, $arg_list);}
    
}


