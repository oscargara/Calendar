<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Application
 *
 * @author oscar
 */
class Application {

    private $config = array();
    private $_routes = array();

    public static function getInstance(){
        static $_instance = NULL;
        if (is_null($_instance)) $_instance = new Application();
        return $_instance;
    }

    public function init(){
        $this->config = parse_ini_file(dirname(__FILE__)."/../config/config.ini");
        $this->_routes = parse_ini_file(dirname(__FILE__)."/../config/routes.ini");

        $this->config['domain'] = trim($this->config['domain'],"/");
        if ($this->config['baseUrl']!='') $this->config['baseUrl'] = "/" . trim($this->config['baseUrl'],"/");

        spl_autoload_register("Application::autoload");
    }

    public function route() {
        $url = $_GET['url'];
        $config = $this->config;
        $baseUrl = str_replace("/","\/", $config['baseUrl']);
        $real_url = preg_replace("/^(".$baseUrl.")(.*)/", "$2", $url,1);

        $args = explode("/", $real_url);
        $controller_class = array_shift ($args);
        $method = array_shift ($args);

        $page_not_found = FALSE;
        if ($controller_class!=''){
            $controller_class = "controller_" . ucfirst($controller_class);

            $controller = new $controller_class();
            if ($method == '') $method = 'index';
            if (method_exists($controller, $method)){
                call_user_func_array(array($controller, $method), $args);
                //$controller->$method($args);
            }else{
                $page_not_found = TRUE;
            }
        }else{
            $page_not_found = TRUE;
        }

        if ($page_not_found) echo "PAGE NOT FOUND";

    }

    static function autoload($className){
        $folders['model'] = "models";
        $folders['controller'] = "controllers";
        $folders['core'] = "core";
        $folders['tool'] = "tool";

        try {
            @list($f,$file) = explode("_", $className);
            if (isset($folders[$f]) && $file!=''){
                $folder = $folders[$f];
                require_once dirname(__FILE__)."/../" . $folder ."/". $file . ".php";
            }
         }catch(Exception $e){
             echo "Classname: $classname\n";
             echo $e->getMessage();
         }
        
    }

    public function getConfig(){
        return $this->config;
    }

    public function setConfig($key, $val){
        $this->config[$key] = $val;
    }

    public static function getSession(){
        static $is_started = FALSE;
        if (!$is_started){
            session_start();
            //$_SESSION = array();
            $_SESSION['session_id'] = session_id();
            $is_started = TRUE;
        }
        
        return $_SESSION;
    }

    public static function setSession($keys, $value){

        if (!is_array($keys)) $keys = array($keys);
        $s = &$_SESSION;
        foreach ($keys as $k) {
            if (!isset($s[$k])) $s[$k] = array();
            $s = &$s[$k];
        }
       
        $s = $value;
    }


    public static function deleteSession($keys){
        if (!is_array($keys)) $keys = array($keys);
        $s = &$_SESSION;
        foreach ($keys as $k) {
            if (!isset($s[$k])) return;
            $s = &$s[$k];
        }
        unset($s);
    }
}