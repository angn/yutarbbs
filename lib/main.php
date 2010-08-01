<?php

function main($controller = null, $action = null, $params = null)
{
	$controller = $controller or $controller = 'main';
	$action                   or $action     = 'index';
	# require_once "./app/controllers/$controller.php";
	$c = new $controller($action);
	call_user_func_array(array(&$c, $action), $params);
	$v = new View($controller, $action);
	$v->render((array)$c);
}
