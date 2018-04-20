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

class State extends Master
{
  protected $table='country';
	protected $file='Country';
  protected $shop_id='shop_state_id';
	  
  public function clear()  
  {    
    if (!parent::clear()) return;    
    $state_ids = $this->get_shop_clear_data();
    foreach($state_ids as $sid) {
      db_query("DELETE FROM ?:states WHERE state_id = ?i", $sid);
      db_query("DELETE FROM ?:state_descriptions WHERE state_id = ?i", $sid);
      $this->clear_shop_id($sid);
    }
    $this->clear_bridge();  
  }
  
	public function sync_bridge()
	{
		$this->load_csv();
		echo '.. States csv loaded ..<br>';
		foreach($this->csv_data as $no=>$cd) {
			//if($cd['Cmp']!='SC') {
				$where=array('code'=>$cd['code']);
				if($row=$this->get_bridge_row($where)){
					$this->update_csv($where, $cd);
				}else{ 
					$this->insert_csv($cd);
				}
			//} else echo "~ skipping country {$cd['Name']}<br>";
		}
		$this->mark_csv();
		echo '.. States bridge synced ..<br>';
	}
	
	public function sync_shop()
	{
		$unsynced=$this->get_shop_unsynced_data();
		echo '.. got State data for sync ..<br>';
		
		foreach ($unsynced as $un) {
			if($un['Cmp']!='SC') {
				$shop_data=array(
					'country_code'=>'GR',
					'code'=>$un['code'],
					'cmp'=>$un['Cmp'],
					'state'=>$un['Name'],
					'status'=>'A'
				);		
				
				$un['shop_state_id']=fn_my_states_update_state($shop_data, $un['shop_state_id']);
				if(empty($un['shop_state_id'])) throw new \Exception('Error syncing state {'.var_export($un,true).'}');
				
				$this->update_shop(array('code'=>$un['code']), $un);
				
			} else {
				echo "~ skipping country {$un['Name']}<br>";
				$this->update_shop(array('code'=>$un['code']), $un);
			}
		}
		echo '.. shop synced States ..<br>';
	}
  
	public static function get_state_cmps()
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_hash_single_array("SELECT Code,Cmp FROM country",array('Code','Cmp'));
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
		$data['shop_state_id']=null;
		$this->update_bridge(array('shop_state_id'=>$id),$data);
	}
	
}
