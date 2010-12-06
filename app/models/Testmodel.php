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
class model_Testmodel extends core_ObjectSession{

    static $collection_name = 'Testmodelxxx';

    public function getFields(){
        $fields['month_day']    = array('type'=>core_ObjectSession::TYPE_INT);
        $fields['note']         = array('type'=>core_ObjectSession::TYPE_STRING);
        $fields['tests']        = array('type'=>core_ObjectSession::TYPE_COLLECTION, 'collection_of'=>'Testmodel', 'unique'=>FALSE);
        return $fields;
    }

}