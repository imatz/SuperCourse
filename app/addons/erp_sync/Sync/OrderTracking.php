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

class OrderTracking extends Master
{
	protected $table='ordertracking';
	protected $file='OrderTracking';
	protected $shop_id='order_id';

  
	public function sync_bridge()
	{
		$this->load_csv();
		echo '.. OrderTracking csv loaded ..<br>';
		foreach($this->csv_data as $no=>$cd) {
			//if($cd['Cmp']!='SC') {
				$where=array('AA'=>$cd['AA']);
				if($row=$this->get_bridge_row($where)){
					$this->update_csv($where, $cd);
				}else{ 
					$this->insert_csv($cd);
				}
			//} else echo "~ skipping country {$cd['Name']}<br>";
		}
		$this->mark_csv();
		echo '.. OrderTracking bridge synced ..<br>';
	}
	
	public function sync_shop()
	{
		$unsynced=$this->get_shop_unsynced_data();
		echo '.. got OrderTracking data for sync ..<br>';
		
		foreach ($unsynced as $un) {			
			
			db_query('UPDATE ?:orders SET tracking_code=?s WHERE order_id=?i', $un['tracking_code'], $un['AA']);
			
			$this->update_shop(array('AA'=>$un['AA']), $un);		
		}
		echo '.. shop synced OrderTracking ..<br>';
	}
}
