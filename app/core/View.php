<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of View
 *
 * @author oscar
 */
class core_View {

    public $config;
    private $data = array();
    private $layout_path = '';
    private $regions = array();
    private $headers = array();

    public function __construct() {
        $app = Application::getInstance();
        $this->config = $app->getConfig();
    }

    public function render($file, $region='') {
        ob_start();
        include $this->config['appRoot'] . "/views/" . $file;
        $buffer = ob_get_contents();
        ob_end_clean();
        if ($region != '')
            $this->regions[$region] = $buffer;
        return $buffer;
    }

    public function setLayout($file) {
        $this->layout_path = $file;
    }

    public function renderLayout() {
        ob_start();
        include $this->config['appRoot'] . "/views/" . $this->layout_path;
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }

    public function renderLayoutByInclude(){
        include $this->config['appRoot'] . "/views/" . $this->layout_path;
    }

    public function renderCssLine($propierties = array()) {
        $propierties['rel'] = isset($propierties['rel']) ? $propierties['rel'] : "stylesheet";
        $propierties['type'] = isset($propierties['type']) ? $propierties['type'] : "text/css";

        return $this->renderHeaderTagsList(array('link' => array($propierties)));
    }

    public function renderScriptLine($propierties = array()) {
        $propierties['type'] = isset($propierties['type']) ? $propierties['type'] : "text/javascript";
        return $this->renderHeaderTagsList(array('script' => array($propierties)));
    }

    public function renderHeaderTagsList($header, $only_tags = array()) {

        if (is_null($header))
            return '';

        $tags = array('title', 'base', 'meta', 'link', 'script', 'style');
        if (count($only_tags))
            $tags = $only_tags;

        $output = "";
        foreach ($tags as $tag_name) {
            if (isset($header[$tag_name]) && is_array($header[$tag_name])) {
                foreach ($header[$tag_name] as $properties) {
                    $output .= "<{$tag_name} ";
                    $text_content = '';
                    foreach ($properties as $p => $v) {
                        if ($p == 'href' || $p == 'src') $v = $this->staticPath($v);
                        if ($p == 'textContent') {
                            $text_content = $v;
                            continue;
                        }
                        
                        if ($v != '') $output .= " {$p}=\"{$v}\"";
                    }
                    if (in_array($tag_name, array('meta', 'link', 'base')))
                        $output .= "/>\n";
                    else
                        $output .= ">{$text_content}</{$tag_name}>\n";
                }
            }
        }
        return $output;
    }

    public function setHeadersTag($tag, $properties) {
        if (isset($this->headers[$tag]))
            $this->headers[$tag] = array();
        $this->headers[$tag][] = $properties;
    }

    public function renderHeadersTag($only_tag = '') {
        if ($only_tag=='') $tags = array();
        else $tags = array($only_tag);
        return $this->renderHeaderTagsList($this->headers, $tags);
    }

    public function staticPath($path) {
        if (preg_match("/^http/", $path)) {
            return $path;
        } else {
            return $this->config['baseUrl'] . $path;
        }
    }

    public function __get($name) {
        return $this->data[$name];
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

}

?>
