<?php

if(defined('ACW_PROJECT') == FALSE)
{
	define('ACW_PROJECT', 'qlfile'); // xxx
}
// Core
require ACW_SYSTEM_DIR . '/class/ACWCore.php';
// Controller
require ACW_SYSTEM_DIR . '/class/ACWController.php';
// Log
require ACW_SYSTEM_DIR . '/class/ACWLog.php';
// DB
require ACW_SYSTEM_DIR . '/class/ACWDB.php';
require ACW_SYSTEM_DIR . '/class/ACWModel.php';
// View
require ACW_SYSTEM_DIR . '/class/ACWView.php';
// 配列用
require ACW_SYSTEM_DIR . '/class/ACWArray.php';
// Session
require ACW_SYSTEM_DIR . '/class/ACWSession.php';
// Error
require ACW_SYSTEM_DIR . '/class/ACWError.php';
// Mailer
//require ACW_SYSTEM_DIR . '/class/Mailer.php';

// Smarty
require ACW_SYSTEM_DIR . '/smarty/Smarty.class.php';

if (DIRECTORY_SEPARATOR == '\\') {	
	define('ACW_WINDOWS', true);
} else {
	define('ACW_WINDOWS', false);
}


ACWCore::init();

ACWSession::init();	//session_start()

header('Expires: -1');
header('Cache-Control:');
header('Pragma:');

date_default_timezone_set('Asia/Ho_Chi_Minh');	// Smarty


ACWCore::set_config('controller_class_name', 'ACWController');

ACWCore::set_config('url_cmd', 'acw_url_cmd');

ACWCore::set_config('smarty_left_delimiter', '<%');
ACWCore::set_config('smarty_right_delimiter', '%>');

ACWCore::set_config('smarty_debug', 0);

//error_reporting(E_ALL & ~E_NOTICE);


require ACW_USER_CONFIG_DIR . '/routes.php';

require ACW_USER_CONFIG_DIR . '/config.php';
require ACW_USER_CONFIG_DIR . '/define.php';

require ACW_USER_CONFIG_DIR . '/db.php';

