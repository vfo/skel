<?php

$layout_tpl = 'empty.tpl';
$smarty->assign('main_content_tpl', 'do_generator.tpl');

if (empty($_GET['pass']) || $_GET['pass'] !== $config['tools']['gen_pwd'])
  $err->set('illegal access');

if (empty($_GET['table']))
  $err->set('do_generator: table name missing');
else
  $table = $_GET['table'];

// recuperation de la liste des champs
$result = $mysqli_link->fetch_all("SHOW COLUMNS FROM `".$table."`");

$code = "class ".$_GET['name']." extends Model
{
  public function __construct()
  {
    \$this->tableName = '".$_GET['table']."';
    \$this->primaryKey = 'id';

    parent::__construct();
  }
";
// genertaion des attributs
$code .= '// attributs
';

foreach($result as $column)
  {
    $code .= '
protected $'.$column['Field'].';';
  }

// genertaion des setters
$code .= '

// setters

';

foreach($result as $column)
  {
    $code .= 
'public function set_'.$column['Field'].'($'.$column['Field'].')
{
$this->'.$column['Field'].' = $'.$column['Field'].';
}

';
  }

// genertaion des getters
$code .= '// getters

';

foreach($result as $column)
  {
    $code .= 
'public function get_'.$column['Field'].'()
{
return $this->'.$column['Field'].';
}

';
  }
$code .= "}";
$smarty->assign('content', $code);
?>