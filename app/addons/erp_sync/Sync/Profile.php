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

class Profile extends Master
{
  protected $table='custypok';
  protected $file='CustYpok';  
  protected $dependents=array('Order','Phone');
  protected $shop_id='shop_profile_id';
	
  public function clear()  
  {    
    if (!parent::clear()) return;    
    $data = $this->get_shop_clear_data();
    foreach ($data as $dt)
      fn_delete_user_profile($dt['shop_user_id'],$dt['shop_profile_id'],true);
      
    $this->clear_bridge();    
  }
  
	public function sync_bridge()
	{
		$this->load_csv();
		echo '.. Profiles csv loaded ..<br>';
		foreach($this->csv_data as $no=>$cd) {
			$where=array('Customer'=>$cd['Customer'],'Ypok'=>$cd['Ypok']);
			if($row=$this->get_bridge_row($where)){
				$this->update_csv($where, $cd);
			}else{  
				$this->insert_csv($cd);
			}
		}
		$this->mark_csv();
		echo '.. Profiles bridge synced ..<br>';
	}
	
	public function sync_shop()
	{
		$unsynced=$this->get_shop_unsynced_data();
		
		echo '.. got Profile data for sync ..<br>';
		
		$delivery_notes_field=fn_my_users_get_setting_field('s_delivery_notes');
		
		foreach ($unsynced as $un) {
			if (empty($un['shop_user_id'])) {
				echo '.. Customer is NOT PROVIDED ..';
				print_r($un);
				echo'<br>';
				continue;
			}
			$shop_data=array(
				'user_id'=>$un['shop_user_id'],
				'profile_id'=>((!empty($un['shop_profile_id']))?$un['shop_profile_id']:0),
				'profile_name'=>((0==$un['Ypok'])?'Κατάστημα':'Υποκατάστημα '.$un['Ypok']),
				's_address'=>$un['Address'],
				's_city'=>$un['Town'],
				's_zipcode'=>$un['Zip'],
				's_state'=>$un['state_code'],
				's_country'=>'GR',
				'fields'=>fn_my_users_get_user_profile_data($un['shop_user_id']) // ta sbhnei opote toy ta janadinv gia na ta perasei
			);		
			
			// an exei steilei delivery notes enhmervse mono an den exei hdh timh
			$current_notes='';
			if(!empty($un['XrPar']) && !empty($un['shop_profile_id'])) {
				$current_notes=db_get_field("SELECT value FROM ?:profile_fields_data 
				WHERE object_type='P' AND field_id=?i AND object_id=?i",
				$delivery_notes_field,$un['shop_profile_id']);
			}
			$shop_data['fields'][$delivery_notes_field]=(empty($current_notes))?$un['XrPar']:$current_notes;
			
			//an einai ejvteriko enhmervse th xvra 
			// an th breis xaxa 
			if ('SC'==$un['Cmp']) {
				$shop_data['s_state'] = '';
				$shop_data['s_country'] = $un['Country'];
			} 
		
			if(0==$un['Ypok'] && empty($un['shop_profile_id'])) { //main profile 
				$shop_data['profile_id']=$this->get_main_profile($un['shop_user_id']); 
			} 
			
			$un['shop_profile_id']=fn_update_user_profile($un['shop_user_id'], $shop_data);
			if (empty($un['shop_profile_id'])) throw new \Exception('Error syncing user profile {'.var_export($un,true).'}');
	
			$this->update_shop(array('Customer'=>$un['Customer'],'Ypok'=>$un['Ypok']), $un);
		}
		echo '.. shop synced Profiles ..<br>';
	}
	
	
	protected function get_shop_unsynced_data() //uelv to usergroup_id to shop
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_array("SELECT cy.*, cu.shop_user_id, co.shop_state_id, co.Cmp, co.Name , IFNULL(co.code,'') as state_code
				FROM {$this->table} cy
				LEFT JOIN customer cu ON cy.Customer = cu.Code 
				LEFT JOIN country co ON cy.Country = co.Name
				WHERE IFNULL(cy.shop_updated,0)=0 AND cy.erp_updated=1");
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
			$data=db_get_array("SELECT cy.shop_profile_id, cu.shop_user_id
				FROM {$this->table} cy
				INNER JOIN customer cu ON cy.Customer = cu.Code AND cu.shop_user_id IS NOT NULL
				WHERE cy.shop_profile_id IS NOT NULL");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
	private function get_country($name)
	{
		return db_get_field(" SELECT a.code
				FROM ?:countries as a 
				LEFT JOIN ?:country_descriptions as b ON b.code = a.code AND b.lang_code = 'el' 
				WHERE b.country LIKE ?l", '%' . $name . '%');
	}
	
	private function get_main_profile($user_id)
	{
		if (empty($user_id)) return null;
        return db_get_field("SELECT profile_id FROM ?:user_profiles WHERE profile_type='P' AND user_id = ?i", $user_id);
	}
	
	public function clear_shop_id($id)
	{
		$data['shop_updated']=-1;
		$data['shop_timestamp']=time();
		$data['shop_profile_id']=null;
		$this->update_bridge(array('shop_profile_id'=>$id),$data);
	}
	
	
	
	public function clear_shop_id_for_Customer($id)
	{
		$data['shop_updated']=-1;
		$data['shop_timestamp']=time();
		$data['shop_user_id']=null;
		$this->update_bridge(array('Customer'=>$id),$data);
	}
}
