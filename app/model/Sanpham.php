<?php

class Sanpham_model extends ACWModel
{		
		
	public static function action_index()
	{
		$param = self::get_param(array(
			'search_user_name'
		));
		$model = new Sanpham_model();
		$rows = $model->get_sanpham_all($param);		
		return ACWView::template_admin('sanpham.html', array(
			'products' => $rows,
			'lang_list'=>'',
			'search_user_name'=>$param['search_user_name']
		));
	}
	public function get_sanpham_byctgno($ctg_no,$limit = 1){
		$sql="select p.pro_name,					
					  p.pro_no,					  
					  p.hieu_xe ,
					  p.doi_xe,
					  p.mau_xe ,
					  p.kieu_xe ,
					  p.giathue_ngay,
					  p.giathue_thang,
					  p.xedi_noithanh,
					  p.phutroi_ngoaigio,
					  p.phutroi_quakm,
					  p.songay_sudung,
					  p.sudung_cuoituan,
					  p.sudung_le,
					  p.img_path
				from product p
				INNER JOIN category c on c.ctg_id = p.ctg_id and c.del_flg= 0
				where p.del_flg = 0
				and c.ctg_id in ((select ctg_id from category where ctg_no = :ctg_no
				union 
				select ctg_id from category where parent_id = 
				( select ctg_id from category where ctg_no = :ctg_no
				)
				))
				limit $limit
				";
		return $this->query($sql,array('ctg_no'=>$ctg_no));
	}
	public static function get_sanpham_byctg($ctg_no){
		$db= new Sanpham_model();
		return $db->get_sanpham_byctgno($ctg_no,4);
	}
	public static function action_v()
	{
		$param = self::get_param(array(
			'acw_url'
		));
		$model = new Sanpham_model();
		$pro_no = $param['acw_url'][0];
		$rows = $model->get_sanpham_info($pro_no);		
		return ACWView::template('sanpham.html', array(
			'pro' => $rows	
			,'relates'=>$model->get_sanpham_lienquan($pro_no)	
		),FALSE);
	}
	public function get_sanpham_info($pro_no){
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
			  content
			  from product where del_flg = 0
			  and pro_no = :pro_no";
		$res = $this->query($sql ,array('pro_no'=>$pro_no));
		if(count($res)> 0){
			return $res[0];
		}
		return null;
	}
	public function get_sanpham_lienquan($pro_no){
		$sql="select pro_name,
			  pro_id,
			  pro_no
			  from product where del_flg = 0
			  and pro_no <>:pro_no 
			  /*and ctg_id in (
									select ctg_id
									from product 
									where del_flg = 0
									and pro_no = :pro_no
				)*/
				limit 6";
		$res = $this->query($sql ,array('pro_no'=>$pro_no));		
		return $res;
	}
}
