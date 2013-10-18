<?php

//array de linkage action/controller
$controllers = array(
		 "home" => "home",
		 );

if (!array_key_exists($action, $controllers))
  $err->set('illegal action: '.$action);

if (file_exists('../app/controllers/'.$controllers[$action].'.php'))
  include('../app/controllers/'.$controllers[$action].'.php');


?>