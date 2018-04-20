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

class Parameter extends Master
{
    protected $table='parameter';
	protected $file='Parameter';
	
	public function sync_bridge()
	{
		$this->load_csv();
		echo '.. Parameters csv loaded ..<br>';
		foreach($this->csv_data as $no=>$cd) {
			$where=array('Param'=>$cd['Param']);
			if($row=$this->get_bridge_row($where)){
				$this->update_csv($where, $cd);
			}else{  
				$this->insert_csv($cd);
			}
		}
		$this->mark_csv();
		echo '.. Parameters bridge synced ..<br>';
	}
	
	public function sync_shop()
	{
		$unsynced=$this->get_shop_unsynced_data();

		echo '.. got Parameter data for sync ..<br>';
		
		foreach ($unsynced as $un) {
			if(!fn_my_custom_parameters_update_parameter ($un['Param'],$un['Value']))
				throw new \Exception('Error syncing Parameters {'.var_export($un,true).'}');
				
			$this->update_shop(array('Param'=>$un['Param']));
		}				
		
		fn_my_custom_parameters_apply_parameters();
		
		echo '.. shop synced Parameters ..<br>';
	}
	
}
