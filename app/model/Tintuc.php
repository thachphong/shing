<?php

class Tintuc_model extends ACWModel
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
	public static function action_ds()
	{
		$param = self::get_param(array(
			'acw_url'
		));
		$model = new Tintuc_model();
		$rows = $model->get_tintuc_byctgno($param['acw_url'][0],10);		
		return ACWView::template('tintuclist.html', array(
			'tintucs' => $rows		
		));
	}
	public function get_tintuc_byctgno($ctg_no,$limit = 1){
		$sql="select news_no,news_name,des,img_path from news  where del_flg =0
				and ctg_id in (select ctg_id from category where ctg_no = :ctg_no and del_flg = 0)
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
		$model = new Tintuc_model();
		$pro_no = $param['acw_url'][0];
		$rows = $model->get_tintuc_info($pro_no);		
		return ACWView::template('tintuc.html', array(
			'post' => $rows	
			//,'relates'=>$model->get_sanpham_lienquan($pro_no)	
		),FALSE);
	}
	public function get_tintuc_info($news_no){
		$sql=" select news_no,news_name,des,content from news  where del_flg =0
				and news_no = :news_no";
		$res = $this->query($sql ,array('news_no'=>$news_no));
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
