<?php

//@TODO radio
function smarty_function_make_forms($params)
{
  $out = '';
  $forms = $params['form'];
  $action = $params['action'];
  $method = $params['method'];
  $data = (!empty($params['data']))?$params['data']:array();
  $errors = (!empty($params['error']))?$params['error']:array();
  $submit_label = (!empty($params['submit_label']))?$params['submit_label']:'Envoyer';
  $inputs = array('hidden', 'password','text');
  $vchk = function($val)
    {
      //      my_dump($val, false);
      $arr = array();
      foreach ($val AS $k=>$v)
	{
	  if (is_object($v))
	    {
	      $arr[$v->get_id()] = $v->get_name();
	    }
	  else
	    $arr[$k]=$v;
	}
      return $arr;
    };
  $values = function($array)
    {
      $arr = array();
      foreach ($array AS $kkey=> $value)
	{
	  if (is_array($value))
	    {
	      foreach ($value AS $k=>$v)
		{
		  $val = $v;
		  $key = $k;
		}
	    }
	  if (is_object($value))
	    {
	      $val = $value->get_name();
	      $key = $value->get_id(); 
	    }
	  else
	    {
	      /*	      $key = $value;
	      //	      my_dump($kkey, false);
	      if (is_string($kkey))*/
		$key = $kkey;
		
	      $val = $value;
	    }
	  $arr[$key]=$val;
	}
      return $arr;
    };
  $out .='<form  method="'.$method.'" action="'.$action.'"><table>';
  foreach ($forms AS $form => $fields)
    {
      foreach ($fields AS $field)
	{
	  
	  $id = str_replace('_','%%',$form).'-'.str_replace('_','%%',$field['field']);
	  $name = $id.'_'.$field['check'].'_'.(($field['mandatory'])?"y":"n");
	  if ($field['type'] === 'hidden')
	      if (!empty($data[$form][$field['field']]) AND $data[$form][$field['field']])
		  $val = $data[$form][$field['field']];
	      else
		  $val = $field['values'];
	  else if (!empty($data[$form][$field['field']]) AND $data[$form][$field['field']])
	    $val = $data[$form][$field['field']];
	  else
	    $val = "";
      $js = '';//'onblur="C.validateField(this.id);return false;"';

      $out .= '<tr '.(($field['type'] === 'hidden')?'class="hidden"':'').'>';      

      $out .= '<th id="label_'.$form.'-'.$field['field'].'" class="label '.(($field['mandatory'] AND empty($data[$form][$field['field']])) ? " mandatory ":"").((!empty($errors[$form][$field['field']]) AND $errors[$form][$field['field']])?" error ":"").'">'.(empty($field['label'])?'':$field['label'].': ') . '</th><td id="field_'.$form.'-'.$field['field'].'">';
      if (in_array($field['type'], $inputs))
	$out .= '<input type="'.$field['type'].'" name="'.$name.'" class="'.((!empty($field['class']))?($field['class']):"").'" id="'.$id.'" '.$js.' placeholder="'.$field['placeholder'].'" value="'.$val.'" / '.(($field['mandatory'])?"required":"").' '.((!empty($field['disabled']))?"disabled=\"disabled\"":"").'>';
 if ($field['type'] == 'select')
	{
	  $out .='<select name="'.$name.'" id="'.$id.'" '.$js.' '.(($field['mandatory'])?"required":"").'  '.((!empty($field['disabled']))?"disabled=\"disabled\"":"").'><option value="">Choisissez une option</option>';
	  $Values = $values($field['values']);
	    foreach ($Values AS $key => $val)
	    $out .= '<option value="'.$key.'" '.((!empty($data[$form][$field['field']]) AND $data[$form][$field['field']] == $key)?"selected=\"selected\"":"").' >'.$val.'</option>';
	  $out .='</select>';
	}
      if ($field['type'] == 'textarea')
	$out .='<textarea class="'.((!empty($field['class']))?($field['class']):"").'" name="'.$name.'" id="'.$id.'" '.$js.' '.(($field['mandatory'])?"required":"").' '.((!empty($field['disabled']))?"disabled=\"disabled\"":"").'>'.((!empty($data[$form][$field['field']]) AND $data[$form][$field['field']])?$data[$form][$field['field']]:"").'</textarea>';
      if ($field['type'] == 'multiple')
	{
	  $out .='<div><select style="width:300px;" multiple name="'.$name.'[]" id="'.$id.'" '.$js.' class="mulsel populate select2-offscreen'.((!empty($field['class']))?($field['class']):"").'" '.(($field['mandatory'])?"required":"").'  '.((!empty($field['disabled']))?"disabled=\"disabled\"":"").' data-placeholder="'.((!empty($field['placeholder']))?$field['placeholder']:"Choisissez une option").'">';
	  $Values = $values($field['values']);
	    foreach ($Values AS $key => $val)
	      $out .= '<option value="'.$key.'" '.(((!empty($data[$form][$field['field']]) AND array_key_exists($key,$data[$form][$field['field']])) OR (!empty($data[$form][$field['field']]) AND in_array($key,$data[$form][$field['field']])) OR (!empty($data[$form][$key]) AND $key == $data[$form][$key]))?"selected=\"selected\"":"").' >'.$val.'</option>';
	  $out .='</select></div>';
	}
      if ($field['type'] == 'checkbox')
	{
	  $Values = $vchk($field['values']);//$values($field['values']);
	    foreach ($Values AS $k=>$v)
	    {
	      //	      my_dump($data, false);
	      $out .= '<input type="checkbox"  name="'.$name.'[]" id="'.$id.'" value="'.$k.'" '.$js.' '.(((!empty($data[$form][$field['field']]) AND array_key_exists($k,$data[$form][$field['field']])) OR (!empty($data[$form][$field['field']]) AND in_array($k,$data[$form][$field['field']])) OR (!empty($data[$form][$k]) AND $k == $data[$form][$k]))?"checked=\"checked\"":""). '/>'.$v.'<br />';
	    }
	}
      $out .= '</td>';
      if (!empty($field['err']))
	  $out .='<td id="err_'.$id.'" class="'.((empty($errors[$form][$field['field']]))?"fhint":"ferr").' "><i class="'.((empty($errors[$form][$field['field']]))?"icon-lightbulb":"icon-warning-sign").'" style="padding:0 10px;"></i><small>'.$field['err'].'</small></td></tr>';
      else
	$out .='<td></td></tr>';
	}
    }
  $out .='<tr><td colspan="3"><input type="submit" class="btn btn-success" name="do" value="'.$submit_label.'" /></td></tr>
  </table>
  </form>';
  return $out;
}

$smarty->registerPlugin("function","make_forms", "smarty_function_make_forms");

?>