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

class Pricegroup extends Master
{
    protected $table='pricegroup';
	protected $file=''; // No File enhmervnetai apo Customer kai WHouse
	
	public function set_csv_data($data)
	{
		$this->csv_data=$data;
	}
	
	public function sync_bridge()
	{
		if (!empty($this->csv_data)) {
			echo '.. Pricegroup data loaded ..<br>';
			foreach($this->csv_data as $no=>$cd) {
				$where=array('Timok'=>$cd['Timok']);
				if($row=$this->get_bridge_row($where)){
					$this->update_csv($where, $cd);
				}else{ 
					$this->insert_csv($cd);
				}
			}
			echo '.. Pricegroup bridge synced ..<br>';
		} else echo '.. NO data for Pricegroup bridge ..<br>';
		
	}
	
	public function sync_shop()
	{
		$unsynced=$this->get_shop_unsynced_data();
		echo '.. got Pricegroup data for sync ..<br>';
		
		foreach ($unsynced as $un) {
			$shop_data=array(
				'usergroup'=>'Pricegroup'.$un['Timok'],
				'status'=>'A',
				'type'=>'C',
				'company_id'=>1,
				'privileges'=>array()
			);		
			
			if(empty($un['shop_usergroup_id'])) { //create
				$un['shop_usergroup_id']=fn_update_usergroup($shop_data, 0);
			} 
			$this->update_shop(array('Timok'=>$un['Timok']), $un);
		}
		echo '.. shop synced Pricegroup ..<br>';
	}
	
	public static function get_pricegroup_ids()
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_hash_single_array("SELECT Timok,shop_usergroup_id FROM pricegroup",array('Timok','shop_usergroup_id'));
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
	public function clear_shop_id($id)
	{
		$data['shop_updated']=-1;
		$data['shop_timestamp']=time();
		$data['shop_usergroup_id']=null;
		$this->update_bridge(array('shop_usergroup_id'=>$id),$data);
	}
	
}
