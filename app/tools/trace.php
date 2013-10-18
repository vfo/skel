<?php

function getLoginRec($sid)
{
  $tmp ='';
  $session = new Session();
  $session->set_sid($sid);
  $session = $session->getAll(array(), true);
  if (!empty($session))
    {
      $uid = $session->get_uid();
      if (!empty($uid))
	{
	  $usr = new User();
	  $usr->set_id($session->get_uid());
	  $usr->hydrate();
	  $tmp = $usr->get_login();
	  $psid = $session->get_psid();
	  if (!empty($psid))
	    $tmp .= ' FROM:'.getLoginRec($session->get_psid());
	}
    }
  return $tmp;
}

function saveAccessLogs($level = "log", $msg = "")
{
  global $config, $user, $session_id;
  if (empty($_COOKIE['f_c']))
  setcookie("f_c",md5(1 + rand() * rand()), time() + (3600*365*24), "/", $config['core']["servername"], $config['core']["secure_cookie"]);
  $name = 'AccessLog-'.date('Y_m_d') . '.csv';
  $handle = fopen('../tmp/logs/' . $name, 'a+');

  $date = date('Y-m-d H:i:s');
  $post = "";
  $get = "";

  if (!empty($session_id))
    {
      $session = new Session();
      $session->set_id($session_id);
      $session->hydrate();
    }
  if (!empty($_COOKIE[$config['core']['ssid_cookie_name']]))
    $login = getLoginRec($_COOKIE[$config['core']['ssid_cookie_name']]);
  else
    $login = 'NOT LOGGED';
  $uid = "";
  if (is_object($user))
    $uid = $user->get_id();
  foreach ($_POST as $key => $var)
    {
      if ($var)
	if (is_array($var))
	  $post .= $key . '=' . var_export($var,true) . ' ';
	else
	  if ($key == 'pwd')
	    $post .= $key . '=*SECRET* ';
	  else
	  $post .= $key . '=' . $var . ' ';
      else
	$post = "";
    }
  if (!$post)
    $post = "";
  foreach ($_GET as $key => $var)
    {
      if ($var)
	if (is_array($var))
	  $get .= $key . '=' . var_export($var,true) . ' ';
      else
	$get .= $key . '=' . $var;
            else
	      $get = "";
    }
  if (!$get)
    $get = "";

  $msglog =  $date                                . ","
    . $_SERVER['SERVER_ADDR']            . ","
    . strtoupper($level)                 . ","
    . $_SERVER['REMOTE_ADDR']            . ","
    . $login                             . ","
    . $uid                             . ","
    . 'URI:'  . $_SERVER['REQUEST_URI']  . ","
    . 'POST:' . urlencode($post)         . ","
    . 'GET:'  . urlencode($get)          . ","
    . 'UNID:' . ((empty($_COOKIE['f_c']))?'':$_COOKIE['f_c']). ","
    . $msg                               . "\n";
  $ret = fputs($handle, $msglog);

  fclose($handle);
  //@TODO INSERER LES LOGS EN BASE DE DONNEES POUR FACILITER LEUR ANALYSE
}

 
function backtrace()
{
  $output = "/---- backtrace ----\n";
  $backtrace = debug_backtrace();
  foreach ($backtrace as $id => $bt)
    {
      if ($id == 0)
	continue ;
      $args = '';
      if (is_array($bt['args']))
	foreach ($bt['args'] as $a)
	  {
	    if (!empty($args))
	      {
		$args .= ', ';
	      }
	    switch (gettype($a))
	      {
	      case 'integer':
	      case 'double':
		$args .= $a;
		break;
	      case 'string':
		$a = htmlspecialchars(substr($a, 0, 64)).((strlen($a) > 64) ? '...' : '');
		$args .= "\"$a\"";
		break;
	      case 'array':
		$args .= 'Array('.count($a).')';
		break;
	      case 'object':
		$args .= 'Object('.get_class($a).')';
		break;
	      case 'resource':
		$args .= 'Resource('.strstr($a, '#').')';
		break;
	      case 'boolean':
		$args .= $a ? 'True' : 'False';
		break;
	      case 'NULL':
		$args .= 'Null';
		break;
	      default:
		$args .= 'Unknown';
	      }
	  }
      $line = "line %5s - {$bt['file']}\t - ";
      if (!empty($bt['class']))
	$line .="{$bt['class']}";
      if (!empty($bt['type'])) 
	$line .="{$bt['type']}";
      $line .= "{$bt['function']}($args)\n";
      $output .= sprintf($line, $bt['line']);
    }
  $output .= "---- backtrace ----/\n";
  return $output;
}


?>