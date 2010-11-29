<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller_calendar
 *
 * @author oscar
 */
class controller_Calendar extends core_Controller{

    function index(){
       $this->view->year = 2011;
       $this->view->month = 5;
       $this->view->setLayout('layout.tpl');
       $this->view->render("calendar_list.tpl", "content");
       echo $this->view->renderLayout();
    }

}
?>
