<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

namespace Sync;

class Order extends Master
{
	protected $table='ordersh';
	protected $file='OrdersH';
	
  public function clear()
  {
    if (!parent::clear()) return;
    Master::export_sync();
  }
	
	public function sync_bridge()
	{
		// ua enhmervsei kai ta orderdetails
		$export_date = date('Y').'/'.date('m').'/'.date('d');
		$export_no = $this->get_max_export_no($export_date);
		
		$detModule=new OrderDetail();
		
		$cmp_map=State::get_state_cmps();    
    $retail_state_code = fn_my_custom_parameters_get_setting_field('retail_customer_state_code');  
    $retail_customer = fn_my_custom_parameters_get_setting_field('retail_customer');
		/*    $cust_lian_map=array(
			'GP'=>fn_my_custom_parameters_get_setting_field('gp_customer'),
			'NG'=>fn_my_custom_parameters_get_setting_field('ng_customer')
		);
		*/    $delivery_notes_field_id=fn_my_users_get_setting_field('s_delivery_notes');
		$pricegroup_ids=Pricegroup::get_pricegroup_ids();
		
		$order_ids=$this->get_unsynced_order_ids();
		
		foreach ($order_ids as $id){
			$o=fn_get_order_info($id);

			// nikosgkil
			$u_id = db_get_field("SELECT user_id FROM ?:orders WHERE order_id = ?i", $id);
			$fmail = db_get_field("SELECT fmail FROM ?:users WHERE user_id = ?i", $u_id);
			$o['email'] = $fmail;
			// ??? a.GIATI CAXNOYME TO USER_ID AFOY YPARXEI HDH STHN $o ??? 
			// ??? b.DOYLEYEI AYTO GIA LIANIKHS ??? 
			// nikosgkil

			$export_no++;
			$insert_data=array(
				'AA'=>$o['order_id'],
				'Date'=> date("Y-m-d H:i:s", $o['timestamp']),
				'Lian'=>$o['tim_lian'],
				'Sum'=>$o['total'],
				'Paralhpths'=>trim($o['s_firstname'].' '.$o['s_lastname']),
				'email'=>$o['email'],
				'TelParalhpth'=>$o['s_phone'],
				'SendName'=>trim($o['firstname'].' '.$o['lastname']),
				'SendAddr'=>$o['s_address'],
				'SendTown'=>$o['s_city'],
				'SendCountry'=>$o['s_state'],
				'SendZip'=>$o['s_zipcode'],
				'SendTel'=>$o['s_phones'],
				'Notes'=>$this->clean_line_feeds($o['notes']),
				'Payment'=>(empty($o['payment_method']['erp_code'])) ? $o['payment_method']['payment'] : $o['payment_method']['erp_code'],
				'SendTime'=>(empty($o['fields'][$delivery_notes_field_id])) ? '' : $this->clean_line_feeds($o['fields'][$delivery_notes_field_id]),
				'export_no'=>$export_no,
				'export_date'=>$export_date
			);
			
			if (!empty($o['payment_info']['transaction_id'])) 
				$insert_data['TransactionId'] = $o['payment_info']['transaction_id'];
			
			if (!empty($o['user_id'])) {
				$cust=$this->get_customer_ypok($o['profile_id']);
				if (empty($cust)) continue;
				$insert_data['Customer']=$cust['Customer'];
				$insert_data['Ypok']=$cust['Ypok'];
			} else //if ($retail_customer_state_code == $o['s_state'])
				{       
				$insert_data['Customer'] = $retail_customer;      
        
			} /*else {
				$insert_data['Customer']=$cust_lian_map[$cmp_map[$o['s_state']]];
      }*/
			
			//if (empty($insert_data['Paralhpths'])) $insert_data['Paralhpths'] =  trim($o['firstname'].' '.$o['lastname']);
			
			$this->insert_shop($insert_data);
			// bgale ap thn oura toy sygxronismoy
			db_query("DELETE FROM ?:order_sync_queue WHERE order_id=?i", $id);
			
			$detModule->csv_data = $o['products'];
			$detModule->sync_bridge($export_no,$export_date);
			/*
			//pame gia ta details
			foreach ($o['products'] as $p) {
				$detail_data = array(
					'AA'=>$o['order_id'],
					'No'=>$p['item_id'],
					'wh'=>$p['product_code'],
					'WhName'=>$p['product'],
					'Quan'=>$p['amount'],
					'TLian'=>$p['extra']['retail_data']['taxed_price'],
					'Timk'=>$p['extra']['pricegroup'] ,
					'Tmon'=>$p['base_price']+$p['tax_value']-$p['extra']['discount'],
					'Sum'=>$p['subtotal'],
					'Notes'=>$p['extra']['notes']				
				);
				
				$detModule->insert_shop($detail_data);
			}
			*/
		}
	}
	
	public function clear_shop_id($id)
	{
		$data['shop_updated']=-1;
		$data['shop_timestamp']=time();
		$data['AA']=$id*(-1);
		$this->update_bridge(array('AA'=>$id),$data);
	}

	protected function get_unsynced_orders()
	{
		return db_get_array("SELECT order_id, FROM_UNIXTIME(timestamp) as order_date, tim_lian, total, user_id, profile_id, firstname, lastname,
		s_firstname, s_lastname, s_state, s_address, s_city, s_country, s_zipcode, s_phone, s_phones, notes, delivery_notes FROM ?:orders where order_id NOT IN (?n)
		", $this->get_synced_order_ids());
	}
	
	protected function get_unsynced_order_ids()
	{
		//return db_get_fields("SELECT order_id FROM ?:orders WHERE order_id NOT IN (?n) AND status in ('O','P')", $this->get_synced_order_ids());
		return db_get_fields("SELECT order_id FROM ?:order_sync_queue WHERE order_id IN (SELECT order_id FROM ?:orders WHERE status IN ('O','P'))");
	}
	
	protected function get_synced_order_ids() 
	{
		$data='';
		try {
			Db::use_bridge();
			$data=db_get_fields("SELECT AA FROM {$this->table}");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
	protected function get_max_export_no($date)
	{
		try {
			Db::use_bridge();
			return db_get_field("SELECT IFNULL(MAX(export_no),0) FROM {$this->table} WHERE export_date='$date'");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		
		return null;
	}
	
	protected function get_customer_ypok($profile_id) 
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_row("SELECT Customer,Ypok FROM custypok cy	WHERE shop_profile_id=?i",$profile_id);
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
	protected function update_csv($where_data,$data=array())
	{
		$where_data=array(
			'AA'=>$where_data['AA']
		);
		parent::update_csv($where_data,$data);
	}
	
	protected function save_csv()
	{
		$csv = new parseCSV();
		$csv->delimiter = $this->delimiter;
		
		if (!empty($this->csv_data)) {
			$header=array_keys($this->csv_data[0]);
			array_pop($header);
			array_pop($header);
			array_pop($header);
			array_pop($header);
			array_pop($header);
			array_pop($header);
			foreach ($this->csv_data as $cd) {
				$date = date_create($cd['export_date']);
				$name = date_format($date, 'Ymd') . '_' . $cd['export_no'] . '_'. $this->file . '.csv';
			
				unset($cd['shop_updated']);
				unset($cd['shop_timestamp']);
				unset($cd['erp_updated']);
				unset($cd['erp_timestamp']);
				unset($cd['export_date']);
				unset($cd['export_no']);
				
				$csv->save(BRIDGE_ROOT.$name, array($cd), false, $header);
				$this->update_csv($cd,$cd);
			}
		
		}
		
	}
}
