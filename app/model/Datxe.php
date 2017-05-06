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
	public static function action_step3()
	{
		$param = self::get_param(array(
			'diadiem_don',
			'noi_den',
			'diadiem_ve'
		));
		$info = ACWSession::get('dat_xe');
		$merg = array_merge($info,$param);
		ACWSession::set('dat_xe',$merg);
		$param['step'] = 4;
		return ACWView::template('datxe.html',$param );
	}
	public static function action_step4()
	{
		$param = self::get_param(array(
			'ho_ten',
			'dien_thoai',
			'dia_chi',
			'cmnd',
			'gioi_tinh',
			'ngay_sinh',
			'email'
		));
		$info = ACWSession::get('dat_xe');
		$merg = array_merge($info,$param);
		$merg['step'] = 5;
		ACWSession::set('dat_xe',$merg);
		
		return ACWView::template('datxe.html',$merg );
	}
	public static function action_step5()
	{
		$param = self::get_param(array(
			'ghi_chu'			
		));
		$info = ACWSession::get('dat_xe');
		$info['ghi_chu']= $param['ghi_chu'];
		$info['chieu_di']="";
		//$merg = array_merge($info,$param);
		$merg['step'] = 6;
		//ACWSession::set('dat_xe',$merg);
		$model = new Datxe_model();
		if($model->_insert($info)){
			$model->sendmail($info);
			ACWSession::set('dat_xe',NULL);
		}		
		return ACWView::template('datxe.html',$merg );
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
	public static function action_update(){
		$param = self::get_param(array(
			'form'
		));
		
	    $url_ref = $_SERVER['HTTP_REFERER'];
		
		$model = new Datxe_model();
		
		$pa = array('ho_ten' => NULL ,
								  'gioi_tinh'  => NULL ,
								  'ngay_sinh'   => NULL,
								  'cmnd'   => NULL,
								  'dia_chi'   => NULL,
								  'dien_thoai'   => NULL,
								  'email' =>null ,
								  'kieu_xe' => NULL  ,
								  'hieu_xe'   => NULL,
								  'ngay_di'   => NULL,
								  'gio_di'   => NULL,
								  'ngay_ve'   => NULL,
								  'gio_ve'   => NULL,
								  'diadiem_don'   => NULL,
								  'noi_den'   => NULL,
								  'diadiem_ve'  => NULL ,
								  'so_km'   => NULL,								  
								  'gia_km'   => NULL,
								  'phi_vuotngay'   => NULL,
								  'phucap_taixe'   => NULL,
								  'tong_tien'  => NULL ,
								  'ghi_chu'=>null  );
		$pa_insert = array_merge($pa,$param['form']);
		if($model->_insert($pa_insert)){
			ACWSession::set('datxe_update','ok');	
			$model->sendmail($pa_insert);
		}
		//ACWSession::set('dat_xe',NULL);
		//return ACWView::template('datxe.html',$merg );
		return ACWView::redirect($url_ref);
	}
	public function _insert($param){
		$sql="insert into orders(
				  ho_ten  ,
				  gioi_tinh  ,
				  ngay_sinh  ,
				  cmnd  ,
				  dia_chi  ,
				  dien_thoai  ,
				  email  ,
				  kieu_xe  ,
				  hieu_xe  ,
				  ngay_di  ,
				  gio_di  ,
				  ngay_ve  ,
				  gio_ve  ,
				  diadiem_don  ,
				  noi_den  ,
				  diadiem_ve  ,
				  so_km  ,
				  add_date  ,
				  upd_date  ,
				  gia_km  ,
				  phi_vuotngay  ,
				  phucap_taixe  ,
				  tong_tien  ,
				  ghi_chu, 
				  chieu_di
			  )
			  values(
			  	  :ho_ten  ,
				  :gioi_tinh  ,
				  :ngay_sinh  ,
				  :cmnd  ,
				  :dia_chi  ,
				  :dien_thoai  ,
				  :email  ,
				  :kieu_xe  ,
				  :hieu_xe  ,
				  :ngay_di  ,
				  :gio_di  ,
				  :ngay_ve  ,
				  :gio_ve  ,
				  :diadiem_don  ,
				  :noi_den  ,
				  :diadiem_ve  ,
				  :so_km  ,
				  now()  ,
				  now()  ,
				  :gia_km  ,
				  :phi_vuotngay  ,
				  :phucap_taixe  ,
				  :tong_tien  ,
				  :ghi_chu,
				  :chieu_di
			  ) ";
		$sql_pa = ACWArray::filter($param, array('ho_ten'  ,
								  'gioi_tinh'  ,
								  'ngay_sinh'  ,
								  'cmnd'  ,
								  'dia_chi'  ,
								  'dien_thoai'  ,
								  'email'  ,
								  'kieu_xe'  ,
								  'hieu_xe'  ,
								  'ngay_di'  ,
								  'gio_di'  ,
								  'ngay_ve'  ,
								  'gio_ve'  ,
								  'diadiem_don'  ,
								  'noi_den'  ,
								  'diadiem_ve'  ,
								  'so_km'  ,								  
								  'gia_km'  ,
								  'phi_vuotngay'  ,
								  'phucap_taixe'  ,
								  'tong_tien'  ,
								  'ghi_chu' ,
								  'chieu_di' ));
		if(isset($param['phut_di'])){
			$sql_pa['gio_di'] = $param['gio_di'].':'.$param['phut_di'].' '.$param['ampm_di'];
		}else{
			$sql_pa['gio_di']="";
		}
		if(isset($param['phut_ve'])){
			$sql_pa['gio_ve'] = $param['gio_ve'].':'.$param['phut_ve'].' '.$param['ampm_ve'];
		}else{
			$sql_pa['gio_ve'] = "";
		}
		if(!isset($param['chieu_di'])){
			$param['chieu_di']="";
		}
		$this->execute($sql,$sql_pa);
		return TRUE;
	}
	public function sendmail($param){
		$email = new Mail_lib();
		
		//$email->setSubject('Thông tin khách hàng đặt xe');
		
		$body_tmp = $this->get_body($param);
		/*ACWLog::debug_var('body',$body_tmp);
		$email->setBody($body_tmp) ;
		$email->AddMailTo(PHPMAIL_ADMIN_EMAIL,PHPMAIL_ADMIN_NAME);
		return $email->Send();*/
		
		$replacements['HEADER'] = '<h3><strong>Thông tin khách hàng đặt xe </strong></h3>';
		$replacements['BODY'] = $body_tmp;
		$mail_to[]['mail_address']= PHPMAIL_ADMIN_EMAIL;	
			//if(count($mail_to) > 0 && $dont_send == 0) {
			    $email->AddListAddress($mail_to);
                
			    $email->Subject = 'Thông tin khách hàng đặt xe - '.date('d/m/Y H:i:s');                
			    $email->loadbody('template_mail.html');
			    $email->replaceBody($replacements);
			    $result = $email->send();
               // ACWLog::debug_var('---don----','result: '.$result);
			//}
	}
	public function get_body($param){
		$html="<table>";
		if(strlen($param['ho_ten']) > 0){
			$html .= "<tr><td><strong>Họ tên: </strong></td><td>Nguyễn Văn A</td></tr>"."\r\n";
		} 
		if(strlen($param['gioi_tinh']) > 0){
			$html .= "<tr><td><strong>Giới tính: </strong></td><td>".$param['gioi_tinh']."</td></tr>"."\r\n";
		}
		if(strlen($param['ngay_sinh']) > 0){
			$html .= "<tr><td><strong>Ngày sinh: </strong></td><td>".$param['ngay_sinh']."</td></tr>"."\r\n";
		}
		if(strlen($param['cmnd']) > 0){
			$html .= "<tr><td><strong>CMND: </strong></td><td>".$param['cmnd']."</td></tr>"."\r\n";
		}
		if(strlen($param['dia_chi']) > 0){
			$html .= "<tr><td><strong>Địa chỉ: </strong></td><td>".$param['dia_chi']."</td></tr>"."\r\n";
		}
		
		$html .= "<tr><td><strong>Điện thoại: </strong></td><td>".$param['dien_thoai']."</td></tr>"."\r\n";
		$html .= "<tr><td><strong>Email: </strong></td><td>".$param['email']."</td></tr>"."\r\n";
		$html .= "<tr><td><strong>Kiểu xe: </strong></td><td>".$param['kieu_xe']."</td></tr>"."\r\n";
		$html .= "<tr><td><strong>Hiệu xe: </strong></td><td>".$param['hieu_xe']."</td></tr>"."\r\n";
		$html .= "<tr><td><strong>Ngày đi: </strong></td><td>".$param['ngay_di']."</td></tr>"."\r\n";
		if(strlen($param['gio_di']) > 0){
			$param['gio_di'] = $param['gio_di'].':'.$param['phut_di'].' '.$param['ampm_di'];
			$html .= "<tr><td><strong>Giờ đi: </strong></td><td>".$param['gio_di']."</td></tr>"."\r\n";
		}
		$html .= "<tr><td><strong>Ngày về: </strong></td><td>".$param['ngay_ve']."</td></tr>"."\r\n";
		if(strlen($param['gio_ve']) > 0){
			$param['gio_ve'] = $param['gio_ve'].':'.$param['phut_ve'].' '.$param['ampm_ve'];
			$html .= "<tr><td><strong>Giờ về: </strong></td><td>".$param['gio_ve']."</td></tr>"."\r\n";
		}
		if(strlen($param['diadiem_don']) > 0){
			$html .= "<tr><td><strong>Địa điểm đón: </strong></td><td>".$param['diadiem_don']."</td></tr>"."\r\n";
		}
		if(strlen($param['noi_den']) > 0){
			$html .= "<tr><td><strong>Nơi đến: </strong></td><td>".$param['noi_den']."</td></tr>"."\r\n";
		}
		if(strlen($param['diadiem_ve']) > 0){
			$html .= "<tr><td><strong>Địa điểm về: </strong></td><td>".$param['diadiem_ve']."</td></tr>"."\r\n";
		}
		if(strlen($param['so_km']) > 0){
			$html .= "<tr><td><strong>Số KM đi: </strong></td><td>".$param['so_km']." Km</td></tr>"."\r\n";
		}
		if(strlen($param['gia_km']) > 0){
			$html .= "<tr><td><strong>Giá theo Km: </strong></td><td>".self::currency_format($param['gia_km'])." VNĐ</td></tr>"."\r\n";
		}
		if(strlen($param['phi_vuotngay']) > 0){
			$html .= "<tr><td><strong>Phí vượt ngày: </strong></td><td>".self::currency_format($param['phi_vuotngay'])." VNĐ</td></tr>"."\r\n";
		}
		if(strlen($param['phucap_taixe']) > 0){
			$html .= "<tr><td><strong>Phụ cấp tài xế: </strong></td><td>".self::currency_format($param['phucap_taixe'])." VNĐ</td></tr>"."\r\n";
		}
		if(strlen($param['tong_tien']) > 0){
			$html .= "<tr><td><strong>Tổng tiền: </strong></td><td>".self::currency_format($param['tong_tien'])." VNĐ</td></tr>"."\r\n";
		}
		if(strlen($param['ghi_chu']) > 0){
			$html .= "<tr><td><strong>Ghi chú: </strong></td><td>".$param['ghi_chu']."</td></tr>"."\r\n";
		}
		$html .="</table>";
		return $html;
	}
    
}
