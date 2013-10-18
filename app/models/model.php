<?php

abstract class Model
{
  protected $link; // refernece du link de la connexion mysqli a utiliser
  public $insert_id; // id du dernier instert via le link mysqli
  protected $affected_rows; // nombre de lignes affectees par la derniere requete via le link mysqli
  protected $fields; // arrays des champs de la table
  protected $primaryKey; // nom du champ de la cle primaire 
  protected $tableName; // nom de la table
  protected $listfields; // array permettant de setter la liste des champs manuellement dans une classe fille

  public function __construct()
  {
	global $mysqli_link, $err;

	$this->link = $mysqli_link;

	if (empty($this->tableName))
		$err->set('Cannot create new Model: $this->tableName is not defined');
	if (empty($this->primaryKey))
		$err->set('Cannot create new Model: $this->primaryKey is not defined');

	$this->setFields();
  }

  protected function setFields()
  {
	global $err;

	if (!empty($this->listfields))
		$this->fields = $this->listfields;
	else
	{
		$q = "SHOW COLUMNS FROM `".$this->tableName."`";

		$result = $this->link->fetch_all($q);
		foreach($result as $column)
		{
			if($column['Key'] == 'PRI')
				$this->primaryKey = $column['Field'];
			
			$this->fields[] = $column['Field'];
		}
	}
	
	if (empty($this->fields))
		$err->set('setFields(): no fields defined for table '.$this->tableName);
  }

  protected function format_opt($opt)
  {
	$clauses = '';
	$nb_clauses = 0;
	if (isset($opt['related']) AND is_array($opt['related']))
	{
		foreach ($opt['related'] AS $clause)
		{
			if (!empty($clause['type']))
				$clauses .= ' '.strtoupper($clause['type']). ' JOIN ';
			else
				$clauses .= ' LEFT JOIN ';
			$clauses .= '`'.$clause['table_to'].'`';
			$clauses .= ' ON `'.$clause['table_to'].'`.`'.$clause['field_to'].'` = `'.$clause['table_from'].'`.`'.$clause['field_from'].'`';
		}
	}
	$nb_clauses = 0;
	if (isset($opt['where']) AND is_array($opt['where']))
	{
		foreach ($opt['where'] AS $clause)
		{
			if ($nb_clauses == 0)
				$clauses .= ' WHERE ';
			else
				$clauses .= ' AND ';
			if (!empty($clause['table']))
				$clauses .= "`".$clause['table']."`.";
			else
				$clauses .= "`".$this->tableName."`.";
			$clauses .= "`".$clause['field']."` ". $clause['op'];
			$clauses .= ($clause['op'] !== 'NOT IN' AND $clause['op'] !== 'IN' AND $clause['value'] !== 'NOW()') ? " '".$this->link->prepare($clause['value'])."'" : " ".$this->link->prepare($clause['value']);
			$nb_clauses++;
		}
	}
	if (isset($opt['groupby']) AND is_array($opt['groupby']))
	{
		$clauses .= ' GROUP BY ';
		if (!empty($opt['groupby']['table']))
			$clauses .= '`'.$opt['groupby']['table'].'`';
		else
			$clauses .= '`'.$this->tableName.'`';
		$clauses .= '.`'.$opt['groupby']['field'].'`';
	}
	$nb_clauses = 0;
	if (isset($opt['orderby']) AND is_array($opt['orderby']))
	{
		foreach ($opt['orderby'] AS $clause)
		{
			if ($nb_clauses == 0)
				$clauses .= ' ORDER BY ';
			else
				$clauses .= ', ';
			if (!empty($clause['table']))
				$clauses .= "`".$clause['table']."`.";
			else
				$clauses .= "`".$this->tableName."`.";	    
			$clauses .= "`".$clause['field']."`";
			if (!empty($clause['sort']))
				$clauses .= " ".$clause['sort'];	      

			$nb_clauses++;
		}
	}
	if (isset($opt['limit']) AND is_array($opt['limit']))
	{
		$clauses .= ' LIMIT ';
		if (!empty($opt['limit']['start']))
			$clauses .= $opt['limit']['start'].',';
		$clauses .= $opt['limit']['len'];
	}
	return $clauses;
  }

  public function hydrate()
  {
	global $err;

	if (empty($this->{$this->primaryKey}))
		$err->set(get_called_class().': cannot hydrate without primary key value');

	$q = "SELECT * FROM `".$this->tableName."` WHERE `".$this->primaryKey."` = ".intval($this->{$this->primaryKey});
	$data = $this->link->fetch_array($q);
	
	foreach ($this->fields as $key => $name)
	{
		$setter = 'set_'.$name;
		$this->$setter($data[$name]);
	}
  }

  public function delete($opt = array())
  { 
	if (empty($opt)) // delete base sur l'instance courante
	{
		$q = "DELETE FROM `".$this->tableName."`";
		$count = 0;
		foreach ($this->fields as $k =>$name)
		{
			$getter = 'get_'.$name;
			$value = $this->$getter();
			
			if (!empty($value))
			{
				if ($count == 0)
					$q .= " WHERE ";
				else
					$q .= " AND ";
				$q .= "`".$this->tableName."`.`".$name."` = '".$value."'";
				$count++;
			}
		}// WHERE `".$this->primaryKey."` = ".intval($this->{$this->primaryKey});
		
	}
	else // delete base sur les opts passess en parametres
		$q = "DELETE FROM `".$this->tableName."`" . $this->format_opt($opt);
	$this->link->query($q);
	$this->affected_rows = $this->link->affected_rows;
}

public function save($opt = array())
{
	if (empty($opt)) // save base sur l'instance courante
	{
		$count = 0;
		$nb_fields = count($this->fields);
		if($this->{$this->primaryKey} != null)
		{
			$q = "UPDATE `".$this->tableName."` SET ";
			foreach ($this->fields as $key => $name)
			{
				if($count < ($nb_fields - 1))
					$q .= "`".$name."` = '".$this->link->prepare($this->$name)."',";
				else
					$q .= "`".$name."` = '".$this->link->prepare($this->$name)."'";
				
				$count++;
			}
			$q .= " WHERE `".$this->primaryKey."` = '".intval($this->{$this->primaryKey})."'";
			$this->link->query($q);
			$this->affected_rows = $this->link->affected_rows;
		}
		else
		{
			$q = "INSERT INTO `".$this->tableName."` (";
				foreach ($this->fields as $key => $name) 
				{
					if($count < ($nb_fields - 1))
						$q .= "`".$name."`,";
					else
						$q .= "`".$name."`)";

$count++;
}

		//remise du compteur a 0
$count = 0;

$q .= " VALUES (";
	foreach ($this->fields as $key => $name) 
	{
		if($count < ($nb_fields - 1))
			$q .= "'".$this->link->prepare($this->$name)."',";
		else
			$q .= "'".$this->link->prepare($this->$name)."')";

$count++;
}
$this->link->query($q);
$this->insert_id = $this->link->insert_id; 
}
}

}
public function format_select($opt)
{
	$out = '';
	if (!empty($opt['constraint']))
		$out .= ' ' .strtoupper($opt['constraint']);
	if (!empty($opt['fields']))
	{
		$nb_field = 0;
		foreach ($opt['fields'] AS $field)
		{
			if ($nb_field !== 0)
				$out .= ', ';
			if (!empty($field['function']))
				$out .= $field['function'].'(';
					if (!empty($field['table']))
						$out .= ' `'.$field['table'].'`.';
					$out .= $field['field'];
					if (!empty($field['function']))
						$out .= ')';
if (!empty($field['as']))
	$out .= ' AS "'.$field['as'].'"';
$nb_field++;
}
}
return $out;
}
public function getAll($opt = array(), $first_only = FALSE)
{
	if (empty($opt)) // getAll base sur l'instance courante
	{
		$q = "SELECT `".$this->tableName."`.`id` FROM `".$this->tableName."`";
		$count = 0;
		foreach ($this->fields as $k =>$name)
		{
			$getter = 'get_'.$name;
			$value = $this->$getter();
			
			if (!empty($value))
			{
				if ($count == 0)
					$q .= " WHERE ";
				else
					$q .= " AND ";
				$q .= "`".$this->tableName."`.`".$name."` = '".$value."'";
				$count++;
			}
		}
		if ($first_only === TRUE)
			$q .= " LIMIT 1";
	}
	else // getAll base sur les opts passees en parametres
	if (!empty($opt['select']))
		$q = "SELECT ".$this->format_select($opt['select'])." FROM `".$this->tableName."`" . $this->format_opt($opt);
	else
		$q = "SELECT `".$this->tableName."`.`id` FROM `".$this->tableName."`" . $this->format_opt($opt);
	//pour printer les requetes sans quitter
	//     my_dump($q, FALSE);
	//    my_dump($q, false);
	$ids = $this->link->fetch_all($q);
	$objs = array();
	if (!empty($opt['select']))
		return $ids;
	else
	{
		$child_class_name = get_called_class();
		
		foreach($ids as $id)
		{
			$obj = new $child_class_name();
			$obj->set_id($id['id']);
			$obj->hydrate();
			if ($first_only === TRUE) 
				return $obj;
			$objs[] = $obj;
		}
	}
	return $objs;
}


}  

?>