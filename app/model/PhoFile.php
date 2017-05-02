<?php
/**
 * ユーザ管理
 *
*/
class Phofile_model extends ACWModel
{
	public static function init()
	{
		Login_model::check(); 
	}
	public static function action_upload(){
		$param = $_FILES['file'];
		$name = $param['name'];
		$size = $param['size'];
		$file_tmp = $param['tmp_name'];
		$file_lb = new FilePHPDebug_lib();
		$folder_name = "data/product/".date('Ym');
		if(is_dir($folder_name)==false){
			 @mkdir($folder_name, 0777, true);
		}
		$file_name =$folder_name.'/'.uniqid().'.'.$file_lb->GetExtensionName($name);
		$file_lb->CopyFile($file_tmp,ACW_ROOT_DIR.'/'.$file_name);
		$file_lb->DeleteFile($file_tmp);
		$result['link'] = ACW_BASE_URL.$file_name;
		return ACWView::json($result);
	}
	public static function action_uploadslides(){
		$param = $_FILES['file'];
		$name = $param['name'];
		$size = $param['size'];
		$file_tmp = $param['tmp_name'];
		$file_lb = new FilePHPDebug_lib();
		$folder_name = "data/slides";
		if(is_dir($folder_name)==false){
			 @mkdir($folder_name, 0777, true);
		}
		$file_name =$folder_name.'/'.uniqid().'.'.$file_lb->GetExtensionName($name);
		$file_lb->CopyFile($file_tmp,ACW_ROOT_DIR.'/'.$file_name);
		$file_lb->DeleteFile($file_tmp);
		$result['link'] = ACW_BASE_URL.$file_name;
		return ACWView::json($result);
	}
	
}
