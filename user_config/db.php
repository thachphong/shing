<?php
/** 
 * @author	   Phong
 * @copyright  2016 
 * @version    1.0
*/
function acwork_db($target)
{
	$param = array();
	
	$param[''] = array(
		//'dsn' => 'mysql:dbname=qlfile1;host=localhost',
		'dsn' => 'mysql:dbname=shopping;host=localhost',
		'username' => 'root',
		'password' => '',
		'driver_options' => array(PDO::ATTR_PERSISTENT => false)
		);
	/*$param[''] = array(
		//'dsn' => 'mysql:dbname=qlfile1;host=localhost',
		'dsn' => 'pgsql:dbname=shopframe;host=localhost',
		'username' => 'postgres',
		'password' => 'postgres',
		'driver_options' => array(PDO::ATTR_PERSISTENT => false)
		);*/
	
	return $param[$target];
}

