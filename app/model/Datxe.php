<?php

class Datxe_model extends ACWModel
{		
	public static function action_index()
	{
		
		/*$param = self::get_param(array(
			'kieu_xe',
			'so_km',
			'ngay_id',
			'gio_di',
			'phut_di',
			'ampm_di',
			'ngay_ve',
			'gio_ve',
			'phut_ve',
			'ampm_ve',
			'taixe_annghi'
		));*/
		$param['kieu_xe']='';
		$param['so_km']='';		
		$param['taixe_annghi']='';
			
		$param['ngay_di'] = $param['ngay_ve']= date('d/m/Y');
		$param['gio_di'] = $param['gio_ve'] = date('g');
		$param['phut_di'] = $param['phut_ve'] = date('i');
		$param['ampm_di'] = $param['ampm_ve'] = date('A');
		$param['step'] = 1;
		//$model = new Datxe_model();
		//$rows = $model->get_news_all();		
		return ACWView::template('datxe.html',$param );
	}
	public static function action_step1()
	{
		
		$param = self::get_param(array(
			'kieu_xe',
			'so_km',
			'ngay_di',
			'gio_di',
			'phut_di',
			'ampm_di',
			'ngay_ve',
			'gio_ve',
			'phut_ve',
			'ampm_ve',
			'taixe_annghi'
		));		
		$param['step'] = 2;
		$model = new Datxe_model();
		$row = $model->get_product($param['kieu_xe']);
		$ngay_di =  DateTime::createFromFormat('d/m/Y',$param['ngay_di']);
		$ngay_ve =  DateTime::createFromFormat('d/m/Y',$param['ngay_ve']);
		$diff = date_diff($ngay_di, $ngay_ve);
		$param['hieu_xe']= $row['hieu_xe'];
		$param['phi_vuotngay'] = 0;
		$param['phucap_taixe'] = 0;
		$param['gia_km'] = 1400000;
		if($diff->d > 0){
			$param['phi_vuotngay'] = $diff->d * 800000 ; 
			if($param['taixe_annghi'] =='no'){
				$param['phucap_taixe'] = $diff->d * 300000 ; 
			}
			
		}
		$param['tong_tien'] = $param['phi_vuotngay'] + $param['phucap_taixe'] + $param['gia_km'];
		ACWSession::set('dat_xe',$param);
		//$rows = $model->get_news_all();		
		return ACWView::template('datxe.html',$param );
	}
	public static function action_step2()
	{
		$param = ACWSession::get('dat_xe');
		$param['step'] = 3;
		
		return ACWView::template('datxe.html',$param );
	}
	public function get_product($kieu_xe){
		$sql="select * from product where  kieu_xe = :kieu_xe and del_flg = 0
				limit  1";
		$res = $this->query($sql,array('kieu_xe'=>$kieu_xe));
		if(count($res)>0){
			return $res[0];
		}
		return NULL;
	}
	
}
