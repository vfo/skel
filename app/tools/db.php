<?php

class Xmysqli extends mysqli
{
  public function __construct($dataDB = array())
  {
    global $config, $debug, $err;
    
    if (empty($dataDB['host']))
      $dataDB['host'] = $config['db']['host'];
    if (empty($dataDB['user']))
      $dataDB['user'] = $config['db']['user'];
    if (empty($dataDB['pass']))
      $dataDB['pass'] = $config['db']['pass'];
    if (empty($dataDB['base']))
      $dataDB['base'] = $config['db']['base'];
    
    parent::__construct($dataDB['host'],$dataDB['user'],$dataDB['pass'],$dataDB['base']);
    if (mysqli_connect_error())
      {
	$error_message = "mysqli::connect(): Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . ";
	if ($config['core']['debug'])
	  $debug['db']['connect'] = $error_message;
	else
	  $err->set($error_message);
      }
    if(!$this->set_charset("utf8"))
      $err->set('mysqli::set_charset(): Error while loading utf8 charset');
  }

  public function query($q)
  {
    global $config, $debug, $err;
    $start = microtime(true);
    $res = parent::query($q);
//    my_dump($q,false);
    $end = microtime(true);
    if ($config['core']['debug'])
      {
	$time = ($end - $start);
	$debug['db']['counter']++;
	$debug['db']['timer'] += $time;
	$debug['db']['qry'][$debug['db']['counter']]['query'] = $q;
	$debug['db']['qry'][$debug['db']['counter']]['time'] = $time;
	$debug['db']['qry'][$debug['db']['counter']]['rows'] = $this->affected_rows;
      }
    if (!$res)
      {
	$error = $this->error;
	$iid = $_SERVER["REMOTE_ADDR"]."-".$_SERVER["REMOTE_PORT"]."-".$_SERVER["SERVER_ADDR"]."-".substr(md5($error), 0, 8);
	
	$name = 'SqlLog-'.date('Y_m_d') . '.err';
	$errtxt = "\n$iid\n\n".
	  "QUERY: $q\n".
	  "Error: $error\n".
	  "Date: ".date("r")."\n".
	  "path: ".$_SERVER["REQUEST_URI"]."\n".
	  "backtrace: ".backtrace().
	  "-------------\n";
	
	if ($fd = fopen('../tmp/logs/' . $name, 'a'))
	  {
	  fputs($fd,$errtxt);
	  fclose($fd);
	  }
	else
	  saveAccessLog("ERR", "Echec ouverture fichier " .$name);
	//die($errtxt);
	$err->set("An error occured please send this code to  <a href='mailto:".$config['core']['contact']."'>".$config['core']['contact']."</a>#".$iid);
      }
    return ($res);
  }
  public function prepare($data)
  {
    return $this->real_escape_string($data);
  }

  public function fetch_all($query)
  {
    $res = $this->query($query);
    $all = array();

    if (!is_object($res))
      die('Query ERROR: ');

    while($row = $res->fetch_array(MYSQLI_ASSOC))
      {
      	$all[] = $row;
      }
    return $all;
  }

  public function fetch_array($query)
  {
    $res = $this->query($query);
    return $res->fetch_array(MYSQLI_ASSOC);
  }

  public function num_rows($q)
  {
    $res = $this->query($q);
    return $res->num_rows;
  }
}

?>