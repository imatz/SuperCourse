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

class Tax extends Master
{
    protected $table='fpa';
	protected $file='FPA';
	
	public function sync_bridge()
	{
		//ignore all but the last one
		$this->keep_last_csv();
		
		$this->load_csv();
		echo '.. Taxes csv loaded ..<br>';
		
		if (!empty($this->csv_data)) $this->reset_erp_updated();
		
		foreach($this->csv_data as $no=>&$cd) {
			// stelnei komma anti gia teleia gia mantissa
			foreach ($cd as &$c) $c=str_replace(',','.',$c);
			$cd['Code']=fn_gr_letter_to_num($cd['Syntelesths']);
			
			$where=array('Code'=>$cd['Code']);
			if($row=$this->get_bridge_row($where)){
				$this->update_csv($where, $cd);
			}else{
				$this->insert_csv($cd);
			}
		}
		$this->mark_csv();
		echo '.. Taxes bridge synced ..<br>';
	}
	
	public function sync_shop()
	{	
		if (!empty($this->csv_data)) { // an eixame csv
			//pame gia delete osvn den enhmervuhkan
			
			$del_data=$this->get_erp_unsynced_data();
			
			foreach($del_data as $del) {
				if (!empty($del['shop_tax_id'])) {
					fn_delete_taxes($del['shop_tax_id']);
				}
			}
			
			// vraia tvra update
			$destinations=fn_my_taxes_get_destination_ids();
		
			$unsynced=$this->get_shop_unsynced_data();
			echo '.. got Taxes data for sync ..<br>';
			
			foreach ($unsynced as $un) {
				
				$rates=array();
				
				foreach ($destinations as $field=>$dest) {
					$rates[$dest]=array(
						'rate_value'=>$un[$field],
						'rate_type'=>'P',
					);
				}
			
				$shop_data=array(
					'tax'=>"ΦΠΑ ({$un['Syntelesths']})",
					'priority'=>$un['Code'],
					'address_type'=>'S',
					'price_includes_tax'=>'N',
					'status'=>'A',
					'rates'=>$rates
				);		
				
				$action = 0;
				
				if(empty($un['shop_tax_id'])) { //create
					$action = $un['shop_tax_id']=fn_update_tax($shop_data, 0);
				} else { //updateS
					$action = fn_update_tax($shop_data, $un['shop_tax_id']);
				}
				
				if ($action) $this->update_shop(array('Code'=>$un['Code']), $un);
			}
			
			echo '.. shop synced Taxes ..<br>';
		} else echo '.. No data for Taxes ..<br>';
	}
	
	public function clear_shop_id($id)
	{
		$data['shop_updated']=-1;
		$data['shop_timestamp']=time();
		$data['shop_tax_id']=null;
		$this->update_bridge(array('shop_tax_id'=>$id),$data);
	}
	
}
