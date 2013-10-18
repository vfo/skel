<?php
function check_dir($chroot, $rep)
{
  $dir = realpath("{$chroot}/{$rep}");
  return ($dir);
}


function get_dir($dir)
{}

  $res = Array();
  $fp = opendir($dir);
  if ($fp)
    {
      while ($d = readdir($fp))
	{
	  if ($d != '.' && $d != '..' && $d != '.svn')
	  $res[] = array("name" => $d, "folder" => (is_dir("{$dir}/{$d}")) ? 1 : 0);
	}
      closedir($fp);
    }
  return ($res);
}


function get_protected_properties($obj, $form, $inside=false)
{
  $obj_dump  = print_r($obj, 1);
  foreach ($form AS $field)
    $fields[] = $field['field'];
  preg_match_all('/^\\s+\\[(\\w+):protected\\]/m', $obj_dump, $matches);
  if ($inside)
    {
      $output = array();
      foreach ($matches[1] as $property)
	{
	  if (in_array($property, $fields))
	    if (method_exists($obj, 'get_'.$property))
	      $output[$property] = $obj->{'get_'.$property}();
	}
      return $output;
    }
  else return $matches[1];
}

function my_dump($var, $kill = TRUE)
{
  echo "<pre>";
  var_dump($var);
  echo "</pre>";
  if ($kill)
    exit();
}



function removeAccents($str)
    {
     $string= strtr($str,
   "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ",
   "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn");

     return $string;
    } 

function genPWD($length = 8)
{
  global $config;
  $error = array();
  $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789[]#(){}@\-&?!<>%$*+=';
  $count = mb_strlen($chars);
  for ($i = 0, $result = ''; $i < $length; $i++)
    {
      $index = rand(0, $count - 1);
      $result .= mb_substr($chars, $index, 1);
    }
  foreach ($config['core']['pwd_require'] AS $req)
    if (!preg_match($req['regex'],$result))
      $error[] = $req['msg'];
  if (!empty($error))
    genPWD();
  return $result;


}

/************************************ 
 *    Allows sorting multi-dimensional 
 *    arrays by a specific key and in 
 *    asc or desc order 
 **/ 
class multiSort 
{ 
  var $key;    //key in your array 

  //runs the sort, and returns sorted array 
  function run ($myarray, $key_to_sort, $type_of_sort = '') 
  { 
    $this->key = $key_to_sort; 
        
    if ($type_of_sort == 'desc') 
      uasort($myarray, array($this, 'myreverse_compare')); 
        else 
	  uasort($myarray, array($this, 'mycompare')); 
            
    return $myarray; 
  } 
    
  //for ascending order 
  function mycompare($x, $y) 
  { 
    if ( $x[$this->key] == $y[$this->key] ) 
      return 0; 
    else if ( $x[$this->key] < $y[$this->key] ) 
      return -1; 
        else 
	  return 1; 
  } 
    
  //for descending order 
  function myreverse_compare($x, $y) 
  { 
    if ( $x[$this->key] == $y[$this->key] ) 
      return 0; 
    else if ( $x[$this->key] > $y[$this->key] ) 
      return -1; 
        else 
	  return 1; 
  } 
} 

?>