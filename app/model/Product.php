<?php

class Product_model extends ACWModel
{	
	public static function init()
	{
		Login_model::check();	
	}
		
	public static function action_index()
	{
		$param = self::get_param(array(
			'search_user_name'
		));
		$model = new Product_model();
		$rows = $model->get_product_all($param);		
		return ACWView::template_admin('product.html', array(
			'products' => $rows			
		));
	}
	public function get_product_all(){
		$sql="select p.pro_id,
				p.pro_name,
				p.hieu_xe,
				p.mau_xe,
				p.doi_xe,
				p.kieu_xe,
				c.ctg_name,
				p.del_flg
				FROM
				product p
				INNER JOIN category  c on c.ctg_id = p.ctg_id 
				";
		return $this->query($sql);
	}
	public static function action_new()
	{
		$param = self::get_param(array('parent_id'));
		$ctg = new Category_model();
		$param['pro_id'] = null;
		$param['pro_name'] = null;
		$param['del_flg'] = 0;
		$param['ctg_id'] = NULL;
		$param['hieu_xe'] = "";
		$param['doi_xe'] = "";
		$param['mau_xe'] = "";
		$param['kieu_xe'] = "";
		$param['giathue_ngay'] = null;
		$param['giathue_thang'] = null;
		$param['xedi_noithanh'] = null;
		$param['phutroi_ngoaigio'] = null;
		$param['phutroi_quakm'] = null;
		$param['songay_sudung'] = null;
		$param['sudung_cuoituan'] = null;
		$param['sudung_le'] = null;
		$param['img_path'] = null;
		$param['video_path'] = null;
		$param['content'] = null;
		$param['ctg_list'] = $ctg->get_category_rows(0);//addslashes(json_encode($ctg->get_category_rows()));
		
		/*if(strlen($param['parent_id'])==0){
			$param['parent_id'] = 0;
		}*/
		//$param['sort'] = 1;
		return ACWView::template_admin('product/edit.html', $param);
		//return ACWView::template_admin('product/edit.html', $param,FALSE);
	}
	public static function action_update(){
		$param = self::get_param(array(
			  'pro_id',			  
			  'pro_name',
			  'ctg_id' ,
			  'hieu_xe' ,
			  'doi_xe',
			  'mau_xe' ,
			  'kieu_xe' ,
			  'giathue_ngay',
			  'giathue_thang',
			  'xedi_noithanh',
			  'phutroi_ngoaigio',
			  'phutroi_quakm',
			  'songay_sudung',
			  'sudung_cuoituan',
			  'sudung_le',
			  'img_path',
			  'video_path',
			  'del_flg'	,
			  'content'		 
			));
		
		$result = array('status' => 'OK');
		$result['status'] = 'OK';	
		$result['msg'] = 'Cập nhật thành công!';		
		$db = new Product_model();
		$msg = $db->check_validate_update($param);
		if($msg == ""){
			if(strlen($param['pro_id'])==0){
				$db->insert($param);
			}else{
				$db->update($param);
			}
		}else{
			$result['status'] = 'ER';	
			$result['msg'] = $msg;
		}
		return ACWView::json($result);
	}
	public function check_validate_update(&$param){
		if(strlen($param['pro_name'])== 0){
			return "Bạn chưa nhập tên sản phẩm !";
		}
		$param['pro_no'] =str_replace(' ','-', ACWModel::convert_vi_to_en($param['pro_name']));
		$param['giathue_ngay'] = ACWModel::convert_string_to_number($param['giathue_ngay']);
		$param['giathue_thang'] = ACWModel::convert_string_to_number($param['giathue_thang']);
		$param['xedi_noithanh'] = ACWModel::convert_string_to_number($param['xedi_noithanh']);
		$param['phutroi_ngoaigio'] = ACWModel::convert_string_to_number($param['phutroi_ngoaigio']);
		$param['phutroi_quakm'] = ACWModel::convert_string_to_number($param['phutroi_quakm']);
		$param['songay_sudung'] = ACWModel::convert_string_to_number($param['songay_sudung']);
		$param['sudung_cuoituan'] = ACWModel::convert_string_to_number($param['sudung_cuoituan']);
		$param['sudung_le'] = ACWModel::convert_string_to_number($param['sudung_le']);
		$pro_no =  $param['pro_no'];
		if(strlen($param['pro_id']) == 0){
			$sql = "select pro_id from product	where pro_no = :pro_no";
			$row = $this->query($sql, array ('pro_no' => $param['pro_no'] ));
			//$row = $this->get_ctg_byno($param['pro_no']);
			$i = 0;
			while($row != null){
				$i++;
				$param['pro_no'] = $pro_no.'-'.$i;
				$row = $this->query($sql, array ('pro_no' => $param['pro_no'] ));
			}
		}else{
			$sql = "select pro_id from product	where pro_no = :pro_no	and pro_id <> :pro_id";
			$res = $this->query($sql, array ('pro_no' => $param['pro_no'] , 'pro_id'=>$param['pro_id']));
			$i = 0;
			while(count($res) > 0){
				$i++;
				$param['pro_no'] = $pro_no.'-'.$i;
				$res = $this->query($sql, array ('pro_no' => $param['pro_no'] , 'pro_id'=>$param['pro_id']));
			}
		}
		return "";
	}
	private function insert($param){
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];
		//$param['ctg_no'] =str_replace(' ','-', ACWModel::convert_vi_to_en( $param['ctg_name']));
		$sql = "INSERT INTO product
					(
					  pro_name,					
					  pro_no,
					  ctg_id ,
					  hieu_xe ,
					  doi_xe,
					  mau_xe ,
					  kieu_xe ,
					  giathue_ngay,
					  giathue_thang,
					  xedi_noithanh,
					  phutroi_ngoaigio,
					  phutroi_quakm,
					  songay_sudung,
					  sudung_cuoituan,
					  sudung_le,
					  img_path,
					  video_path,
					  add_date,
					  add_user,
					  upd_date,
					  upd_user,
					  del_flg,
					  content
					)
				VALUES
					(
					  :pro_name,					
					  :pro_no,
					  :ctg_id ,
					  :hieu_xe ,
					  :doi_xe,
					  :mau_xe ,
					  :kieu_xe ,
					  :giathue_ngay,
					  :giathue_thang,
					  :xedi_noithanh,
					  :phutroi_ngoaigio,
					  :phutroi_quakm,
					  :songay_sudung,
					  :sudung_cuoituan,
					  :sudung_le,
					  :img_path,
					  :video_path,
					  now(),
					  :user_id,
					  now(),
					  :user_id,
					  :del_flg,	
					  :content				
					)
				";
		
 
		$this->execute($sql, ACWArray::filter($param, array(					  		  
					  'pro_name',
					  'pro_no',
					  'ctg_id' ,
					  'hieu_xe' ,
					  'doi_xe',
					  'mau_xe' ,
					  'kieu_xe' ,
					  'giathue_ngay',
					  'giathue_thang',
					  'xedi_noithanh',
					  'phutroi_ngoaigio',
					  'phutroi_quakm',
					  'songay_sudung',
					  'sudung_cuoituan',
					  'sudung_le',
					  'img_path',
					  'video_path',
					  'del_flg'	,
					  'user_id',
					  'content'
					)));
		$this->commit();
		return TRUE;
	}
	protected function update($param){
		
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];		
		$sql = "update product
					set pro_name= :pro_name ,					
					  pro_no = :pro_no,
					  ctg_id = :ctg_id,
					  hieu_xe = :hieu_xe,
					  doi_xe = :doi_xe,
					  mau_xe = :mau_xe,
					  kieu_xe = :kieu_xe,
					  giathue_ngay = :giathue_ngay,
					  giathue_thang = :giathue_thang,
					  xedi_noithanh = :xedi_noithanh,
					  phutroi_ngoaigio = :phutroi_ngoaigio,
					  phutroi_quakm = :phutroi_quakm,
					  songay_sudung = :songay_sudung,
					  sudung_cuoituan = :sudung_cuoituan,
					  sudung_le = :sudung_le,
					  img_path = :img_path,
					  video_path = :video_path,					
					  upd_date =now(),
					  upd_user =:user_id,
					  del_flg =:del_flg,
					  content =:content
					where pro_id = :pro_id
				";
		
 		$sql_par = ACWArray::filter($param, array(
					'pro_id',
					'pro_name',
					  'pro_no',
					  'ctg_id' ,
					  'hieu_xe' ,
					  'doi_xe',
					  'mau_xe' ,
					  'kieu_xe' ,
					  'giathue_ngay',
					  'giathue_thang',
					  'xedi_noithanh',
					  'phutroi_ngoaigio',
					  'phutroi_quakm',
					  'songay_sudung',
					  'sudung_cuoituan',
					  'sudung_le',
					  'img_path',
					  'video_path',
					  'del_flg'	,
					  'user_id',
					  'content'
					));
		$this->execute($sql,$sql_par );			
		
		$this->commit();
		return TRUE;	
	}
	public function get_product_byno($pro_no){
		$sql=" select pro_name,
			  pro_id,
			  pro_no,
			  ctg_id ,
			  hieu_xe ,
			  doi_xe,
			  mau_xe ,
			  kieu_xe ,
			  giathue_ngay,
			  giathue_thang,
			  xedi_noithanh,
			  phutroi_ngoaigio,
			  phutroi_quakm,
			  songay_sudung,
			  sudung_cuoituan,
			  sudung_le,
			  img_path,
			  video_path
			  from product where pro_no = :pro_no";
		$res = $this->query($sql ,array('pro_no'=>$pro_no));
		if(count($res)> 0){
			return $res[0];
		}
		return null;
	}
	
	public static function action_delete()
	{
		$param = self::get_param(array(
			'acw_url'
		));
		$model = new Product_model();
			
		$result['status']="OK";
		if(!isset($param['acw_url'][0]) || strlen($param['acw_url'][0])== 0 ){
			$result['status']="NOT";
			$result['msg']= $msg;
		}else
		{
			$model->delete($param['acw_url'][0]);	
		}
		
		return ACWView::json($result);
	}
	private function delete($pro_id){
		$sql ="delete from product where pro_id = :pro_id"; 
		return $this->execute($sql,array('pro_id' =>$pro_id));
		
	}
	public static function action_edit()
	{
		$param = self::get_param(array('acw_url'));	
		$db = new Product_model();
		$result = $db->get_product_info($param['acw_url'][0]);
		$ctg = new Category_model();
		$result['ctg_list'] = $ctg->get_category_rows(0);
		return ACWView::template_admin('product/edit.html', $result);
	}
	public function get_product_info($pro_id){
		$sql=" select pro_name,
			  pro_id,
			  pro_no,
			  ctg_id ,
			  hieu_xe ,
			  doi_xe,
			  mau_xe ,
			  kieu_xe ,
			  giathue_ngay,
			  giathue_thang,
			  xedi_noithanh,
			  phutroi_ngoaigio,
			  phutroi_quakm,
			  songay_sudung,
			  sudung_cuoituan,
			  sudung_le,
			  img_path,
			  video_path,
			  del_flg,
			  content
			  from product where pro_id = :pro_id";
		$res = $this->query($sql ,array('pro_id'=>$pro_id));
		if(count($res)> 0){
			return $res[0];
		}
		return null;
	}
	public static function action_upload()
	{
		
		$file_name="abc.jpg";
		return ACWView::template_admin('product/upload.html', array(
			'file_name' => $file_name			
		));
	}
}
