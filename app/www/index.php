<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once dirname(__FILE__).'/../core/Application.php';

$app = Application::getInstance();
$app->init();
$app->setConfig('webRoot', dirname(__FILE__));
$app->setConfig('appRoot', dirname(__FILE__)."/..");
$config = Application::getInstance()->getConfig();
core_Model::initDB($config['dsn_master'], $config['dsn_slave']);
$app->route();
