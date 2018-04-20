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

class Phone extends Master
{
  protected $table='tel';
	protected $file='Tel';

  public function clear()  
  {    
    if (!parent::clear()) return;
    $data = $this->get_shop_clear_data();
    foreach ($data as $dt)
      fn_my_users_delete_user_profile_phones ('S',$dt['shop_user_id'],$dt['shop_profile_id']);
    
    $this->clear_bridge();    
  }
	
	public function sync_bridge()
	{
		$this->load_csv();
		echo '.. Phones csv loaded ..<br>';
		foreach($this->csv_data as $no=>$cd) {
			$where=array('Customer'=>$cd['Customer'],'Ypok'=>$cd['Ypok']);
			if($row=$this->get_bridge_row($where)){
				$this->update_csv($where, $cd);
			}else{  
				$this->insert_csv($cd);
			}
		}
		$this->mark_csv();
		echo '.. Phones bridge synced ..<br>';
	}
	
	public function sync_shop()
	{
		$unsynced=$this->get_shop_unsynced_data();

		echo '.. got Phone data for sync ..<br>';
		
		foreach ($unsynced as $user) {
			
			foreach ($user['profiles'] as $profile) {
				if (empty($profile['shop_profile_id'])) {
					echo '.. Profile is NOT PROVIDED ..';
					print_r($user);
					echo'<br>';
				} else {
					if(!fn_my_users_update_user_profile_phones ($profile['phones'],'S',$user['shop_user_id'],$profile['shop_profile_id']))
						throw new \Exception('Error syncing user phones {'.var_export($profile,true).'}');
						
					$this->update_shop(array('Customer'=>$user['Customer'],'Ypok'=>$profile['Ypok']));
				}
				
			}
		}
		echo '.. shop synced Phones ..<br>';
	}
	
	
	protected function get_shop_unsynced_data() 
	{
		$data=array();
		try {		
			Db::use_bridge();
			
			$data = db_get_array("SELECT t.Customer, cu.shop_user_id
				FROM {$this->table} t
				LEFT JOIN customer cu ON t.Customer = cu.Code
				WHERE IFNULL(t.shop_updated,0)=0 AND t.erp_updated=1");
				
			foreach ($data as &$user) {
				$user['profiles']=db_get_array("SELECT t.Ypok, cy.shop_profile_id 
					FROM {$this->table} t 
					LEFT JOIN custypok cy ON t.Customer=cy.Customer AND t.Ypok = cy.Ypok
					WHERE t.Customer=?i AND IFNULL(t.shop_updated,0)=0 AND t.erp_updated=1",$user['Customer']
				);
				foreach ($user['profiles'] as &$profile) {
					$profile['phones']=db_get_fields("SELECT tel 
						FROM {$this->table} 
						WHERE Customer=?i AND Ypok=?i AND IFNULL(shop_updated,0)=0 AND erp_updated=1",$user['Customer'],$profile['Ypok']
					);
				}
			}	
			
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
  
  protected function get_shop_clear_data() 
	{
		$data=array();
		try {		
			Db::use_bridge();
			
			$data = db_get_array("SELECT cu.shop_user_id, cy.shop_profile_id
				FROM {$this->table} t
				INNER JOIN customer cu ON t.Customer = cu.Code AND cu.shop_user_id IS NOT NULL
				INNER JOIN custypok cy ON t.Customer=cy.Customer AND t.Ypok = cy.Ypok AND cy.shop_profile_id IS NOT NULL");
        
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
}
