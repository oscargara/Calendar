<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller
 *
 * @author oscar
 */
class core_Controller {

    public $config;
    public $view;
    
    function  __construct() {
        $app = Application::getInstance();
        $this->config = $app->getConfig();
        $this->view = new core_View();
    }
}
?>
