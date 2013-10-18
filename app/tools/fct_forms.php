<?php
function ChkRxp($type)
{
  switch ($type)
    {
    case 'montant'  : return ('/[0-9]+\.?[0-9]*/');
    case 'valide': return ('/.{2,}/');
    case 'phone' : return ('/^[0-9\s.+()-\[\]]+$/');
    case 'txt' : return ('/[a-zA-Z0-9]+/');
    case 'nbr' : return ('/[0-9]+/');
    case 'date' : return ('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/');
    case 'datetime' : return ('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/');
    case 'civility' : return ('/^(Monsieur|Madame|Mademoiselle|1|2|3)$/');
    case 'effectif' : return ('/^(0-5|5-20|20-50|50-200|\+200)$/');
    case 'minNum' : return ('/[0-9]{1,}/');
    case 'email' : return ("/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[a-z]{2,4}|museum|travel)\s*$/i");
    case 'all' : return('/.*/');
    default : return('/.*/');
    }
}
  function checkAndExtractData(&$data)
  {
    $exclude = array('do');
    $err = array();
	$error = array();
    foreach ($_POST AS $k => $v)
      if (!in_array($k, $exclude))
	$post[$k] = $v;
    //Check

    foreach ($post AS $key => $value)
      {
	$tmp = explode('_', $key);
	//	my_dump($tmp,false);
	if (!is_array($value))
	  if (!empty($value))
	    if (!preg_match(ChkRxp($tmp[1]), $value))
	      $err[$tmp[0]] = 'Ce champs est invalide';
/*		if ($tmp[1] == 'date')
		{
			$temp = explode('/', $value);
			if ($temp[0] && $temp[1] && $temp[2])
				if (!checkdate($temp[1], $temp[0], $temp[2]))
			$err[$tmp[0]] = 'Cette date n\'est pas valide';
		}*/
	if ($tmp[2] == 'y')
	  if (empty($value) AND $value !== '0')
	    $err[$tmp[0]] = 'Ce champs est obligatoire';
	if (empty($err[$tmp[0]]))
	  {
	    $data_tmp[$tmp[0]] = $value;
	  }
      }
    //Extract and format

    foreach ($data_tmp AS $k => $v)
      {
        $tmp = explode('-', $k);
        $data[$tmp[0].'-'.str_replace('%%', '_',$tmp[1])][str_replace('%%', '_',$tmp[2])] = $v;
      }
    foreach ($err AS $k => $v)
      {
        $tmp = explode('-', $k);
        $error[$tmp[0].'-'.str_replace('%%', '_',$tmp[1])][str_replace('%%', '_',$tmp[2])] = $v;
      }
    return $error;
  }

function hydrateAndSave($data)
{
    
  foreach($data AS $form=>$field)
    {
      $tmp = explode('-', $form);
      require_once('../app/models/'.$tmp[1].'.php');
      if (class_exists($tmp[1], false))
	$input = new $tmp[1]();
      foreach ($field AS $k=>$v)
	{
	  if (is_array($v))
	    foreach ($v AS $vv)
	      {
		if (method_exists($input, 'set_'.$vv))
		  $input->{'set_'.$vv}(1);
		elseif (method_exists($input, 'set_'.$k.'s_id'))
		  $input->{'set_'.$k.'s_id'}($v); 
	      }
	  if (method_exists($input, 'set_'.$k))
	    $input->{'set_'.$k}($v); 
	  elseif (method_exists($input, 'set_'.$k.'_id'))
	    $input->{'set_'.$k.'_id'}($v); 
	}
    }
  //  my_dump($input);
  return $input->save();  
}

?>