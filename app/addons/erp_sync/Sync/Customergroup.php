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

class Customergroup extends Master
{
    protected $table='customergroup';
	protected $file=''; // No File enhmervnetai apo Customer kai whCateg
	
	public function set_csv_data($data)
	{
		$this->csv_data=$data;
	}
	
	public function sync_bridge()
	{
		if (!empty($this->csv_data)) {
			echo '.. Customergroup data loaded ..<br>';
			foreach($this->csv_data as $no=>$cd) {
				$where=array('CustomerGroup'=>$cd['CustomerGroup']);
				if($row=$this->get_bridge_row($where)){
					$this->update_csv($where, $cd);
				}else{ 
					$this->insert_csv($cd);
				}
			}
			echo '.. Customergroup bridge synced ..<br>';
		} else echo '.. NO data for Customergroup bridge ..<br>';
		
	}
	
	public function sync_shop()
	{
		$unsynced=$this->get_shop_unsynced_data();
		echo '.. got Customergroup data for sync ..<br>';
		
		foreach ($unsynced as $un) {
			$shop_data=array(
				'usergroup'=>'Customergroup'.$un['CustomerGroup'],
				'status'=>'A',
				'type'=>'C',
				'company_id'=>1,
				'privileges'=>array()
			);		
			
			if ('R' == $un['CustomerGroup']) {
				$un['shop_usergroup_id'] = USERGROUP_GUEST;
			} else if(empty($un['shop_usergroup_id'])) { //create
				$un['shop_usergroup_id']=fn_update_usergroup($shop_data, 0);
			} 
			$this->update_shop(array('CustomerGroup'=>$un['CustomerGroup']), $un);
		}
		echo '.. shop synced Customergroup ..<br>';
	}
	
	public static function get_customergroup_ids()
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_hash_single_array("SELECT CustomerGroup,shop_usergroup_id FROM customergroup 
											WHERE shop_updated <> -1 AND shop_usergroup_id IS NOT NULL",array('CustomerGroup','shop_usergroup_id'));
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
