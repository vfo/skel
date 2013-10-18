<?php

require_once('../app/config/config.php');
require_once('../app/tools/error.php');
require_once('../app/tools/functions.php');
require_once('../app/models/model.php');

$err = new Error();

if ($config['core']['env'] === 'dev')
  ini_set('display_errors', 1);
else
  {
    //@TODO verifier que cela empeche bien l'affichage des erreur en les putsant dans le fichier de log 'phpErrorLog-'.date('Y_m_d') . '.err'
    ini_set('display_errors', 0);
    ini_set('log_errors',1);
    ini_set('error_log', '../tmp/logs/PhpError-'.date('Y-m-d').'.err');
    set_error_handler('Error::prod_error_handler');
  }

require('../app/tools/trace.php');
require('../app/tools/db.php');

$mysqli_link = new xmysqli();


require('../app/vendors/Smarty/Smarty.class.php');
$smarty = New Smarty();
$smarty->caching = false;
$smarty->compile_check = true;
$smarty->debugging = false;

require_once('../app/tools/fct_smarty.php');

if ($config['core']['env'] === 'dev')
  $smarty->caching = false;

if ($config['core']['debug'] === true)
  {
    $smarty->debugging = true;
    $debug = array();
    $debug['db']['counter'] = 0;
    $debug['db']['timer'] = 0.0;
  }

$smarty->template_dir = $config['smarty']['templates'];
$smarty->compile_dir = $config['smarty']['compil'];
$smarty->cache_dir = $config['smarty']['cache'];
$smarty->config_dir = $config['smarty']['config'];


if (!empty($_GET['action']))
  $action = $_GET['action'];
else
  $action = "";
  
if(empty($action) || !in_array($action, $config['core']['not_logged_permitted_actions']))
      $action = $config['core']['default_action'];

if ($config['core']['logs'] === true)
  saveAccessLogs();


$layout_tpl = $config['tpls']['default_layout'];

include('../app/routes.php');

if (!empty($debug))
  $smarty->assign('debug', $debug);
if (!empty($json_output))
  echo json_encode($json_output);
else
$smarty->display('layouts/'.$layout_tpl);
?>