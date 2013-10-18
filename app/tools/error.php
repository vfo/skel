<?php

class Error
{
  protected $errors;

  public function __construct()
  {
    $this->errors = array();
  }

  public function prod_error_handler($error_level, $error_message, $error_file, $error_line, $error_context)
  {
    $name = 'phpErrorLog-'.date('Y_m_d') . '.err';
    $error = "{$error_level}, {$error_message}, {$error_file}, {$error_line}, {$error_context}\n";
    if ($fd = fopen('../tmp/logs/'.$name, 'a+'))
      {
	fputs($fd, $error);
	fclose($fd);
      }
  }

  public function set($msg, $level = 'critical')
  {
    $error = array('lvl' => $level, 'msg' => $msg);
    $this->errors[] = $error;
    
    if ($level === 'critical')
      $this->critical_error();
  }
  
  public function critical_error()
  {
    global $smarty;
    global $config;
    
    $last = array_pop($this->errors);
    if (isset($smarty) AND is_object($smarty))
    {   
   	$smarty->assign('errormsg', $last['msg']);
    	$smarty->display($config['tpls']['error']);
    }
    exit();
  }  
}

?>