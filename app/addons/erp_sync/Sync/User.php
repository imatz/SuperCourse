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

class NoMailException extends \Exception{}

use Tygh\Registry;

class User extends Master
{
  protected $table='customer';
  protected $file='Customer';
  protected $shop_id='shop_user_id';
  protected $dependents = array('Order','Profile');
  
  public function clear()
  {
    if (!parent::clear()) return;
    $user_ids = $this->get_shop_clear_data();
    foreach ($user_ids as $uid)
      fn_delete_user($uid);
      
    $this->clear_bridge();  
    //echo 'clearing '.$this->table."\n";
  }
	
	public function sync_bridge()
	{
		$this->load_csv();
		echo '.. Users csv loaded ..<br>';
		foreach($this->csv_data as $no=>$cd) { 
			if($row=$this->get_bridge_row(array('Code'=>$cd['Code']))){
				$this->update_csv(array('Code'=>$cd['Code']), $cd);
			}else{ 
				$this->insert_csv($cd);
			}
		}
		$this->mark_csv();
		
		// ok usergroups
		$priceGroup_bridge= new Pricegroup();
		$group_data=$this->get_unsynced_price_usergroups();
		$priceGroup_bridge->set_csv_data($group_data);
		$priceGroup_bridge->sync_bridge();
		
		$customerGroup_bridge= new Customergroup();
		$group_data=$this->get_unsynced_customer_usergroups();
		$customerGroup_bridge->set_csv_data($group_data);
		$customerGroup_bridge->sync_bridge();
		
		echo '.. Users bridge synced ..<br>';
	}
	
	public function sync_shop()
	{
		$auth=null;
		define('NO_UPDATE_PROFILE_MAIL',1);
		// sygxronise pvta ta usergroups
		$priceGroup_bridge= new Pricegroup();
		$priceGroup_bridge->sync_shop();		
		// pare ta ids tvn usergroups
		$pricegroup_ids=Pricegroup::get_pricegroup_ids();
		
		$customerGroup_bridge= new Customergroup();
		$customerGroup_bridge->sync_shop();		
		// pare ta ids tvn usergroups
		$customergroup_ids=Customergroup::get_customergroup_ids();
		
		$afm_field=fn_my_users_get_setting_field('afm');
		$profession_field=fn_my_users_get_setting_field('profession');
		$vat_class=fn_my_users_get_setting_field('vat_class');
		$account_type=fn_my_users_get_setting_field('account_type');
		$cmp=fn_my_users_get_setting_field('cmp');
		$tim_lian=fn_my_users_get_setting_field('tim_lian');
		
		$unsynced=$this->get_shop_unsynced_data();
		echo '.. got User data for sync ..<br>';
		//var_dump($unsynced);
		foreach ($unsynced as $un) 
		{
//--------------------------------------------------------------------------------------------------------------------------
//----------------------------------------- Sync twn emails ws edo <-- nikosgkil + farmakis + liampas ----------------------
//--------------------------------------------------------------------------------------------------------------------------
			//dilwsi metablitwn pou periexoun times apo ta asuxronista stoixeia tis bridge
			$fmail = $un['eShop_Mail'];
			$id = $un['shop_user_id'];	
			$code = $un['Code'];	
			$shop_status = $un['eShop_Status'];	
			$password = $un['password'];	
			$lastname_fake='.';
				
			//AN to status pou aposteletai einai energo=A  
			if ($shop_status=="A")
			{	
				//Den epitrepetai o xristis na einai energos xwris email H password
				if ( empty($fmail) || empty($password) )
				{
					throw new \Exception('Error: The sent shop_status is Active but the email or the password is Blank of bridge.customer.code{'.var_export($code,true).'}');
				}
				else
				{	
					/*
					 *Periptwsi UPDATE me to yparxon email: 
					 *efoson uparxei o sugkekrimenos xristis kai den exei keno to pedio email sto cscart_users
					*/
					//an uparxei idi o xristis tote user_exists=1
					$user_exists = db_get_field ("SELECT COUNT(*) FROM ?:users WHERE user_id='$id' AND fmail='$fmail'");
					//briskoume to email tou xristi me basi to id
					$user_email = db_get_field ("SELECT email FROM ?:users WHERE user_id='$id'");
					if($user_exists == 1 && $user_email!="")
					{
						//to email sto cscart_users prepei na einai monadiko			
						$users_same_email = db_get_field ("SELECT COUNT(*) FROM ?:users WHERE user_id!='$id' AND email='$user_email'"); 
						if($users_same_email == 0)
						{
							$email_to_add = $user_email;  
						}
						else
						{						
							$user_ids_with_same_email = db_get_array("SELECT user_id FROM ?:users WHERE email='$user_email'");
							throw new \Exception('Error: There are another eshop.users.id with the same email{'.$user_email.'} {'.var_export($user_ids_with_same_email,true).'}');
						}	
					}
					/*
					 *Periptwsi CREATE neou email: 
					 *dimourgei neo email, dinontas neo megalutero suffix
					*/
					else
					{		
						$max_email = db_get_field("SELECT max(email) FROM ?:users WHERE fmail='$fmail' AND fmail!=email");  	//pairnoyme to max email basi tou fmail pou stelnetai
						$explode_max_email = explode("@",$max_email); 								//spame to max email sta 2
						$max_suffix = substr($explode_max_email[0], -3); 							//kratame to max suffix			
						$suffix = $max_suffix+1;										//dimioyrgeitai to neo epithimito suffix
						$suffix = str_pad($suffix, 3, '0', STR_PAD_LEFT); 							//ginetai sti morfi pou theloyme
						$explode_fmail = explode("@",$fmail); 									//spame to fmail pou stelnetai sta 2	
						$email_to_add = join($suffix . '@', $explode_fmail);							//dimioyrgeitai to email
					}			
				}
			}
			//An o xristis einai anenergos=D tote den prepei na exei fmail, email,  password	
			else
			{
				$un['eShop_Mail']="";
				$email_to_add ="";
				$un['password']="";
			}
				
			$user_login = $shop_data['user_login'];
//--------------------------------------------------------------------------------------------------------------------------
//----------------------------------------- Sync twn emails ws edo <-- nikosgkil + farmakis + liampas ----------------------
//--------------------------------------------------------------------------------------------------------------------------
			$uid_tim_lian = $un['shop_user_id'];
			$epaggelma_tim_lian = db_get_field("SELECT value FROM cscart_profile_fields_data WHERE field_id = '43' AND object_id = '$uid_tim_lian'");
						
			if ($epaggelma_tim_lian == 'B')
			{
				$un['TIM_LIAN'] = 'T';
			}
			//	$Xname=explode(' ',$un['Name']);
			$shop_data=array(
			//	"firstname"=>array_pop($Xname), // to onoma synhuvs einai sto telos
			//	"lastname"=>implode(' ',$Xname),
				"firstname"=>$un['Name'], // to onoma synhuvs einai sto telos
				"lastname"=>$lastname_fake, //To lastname na fainetai ws teleia panta
				"password1"=>$un['password'],
				"password2"=>$un['password'],
				"email"=>$email_to_add,
				"fmail"=>$un['eShop_Mail'],
				'fields'=>array(
					$afm_field=>$un['afm'],
					$profession_field=>$un['epagg'],
					$account_type=>$un['eShop_GPermitions'],
					$cmp=>$un['CMP'],
					$tim_lian=>$un['TIM_LIAN'],
					$vat_class=>$un['kfpa']
				),
				'status'=>$un['eShop_Status'],
				'user_type'=>'C',
				'user_login'=>$un['Code'],
				'company_id'=>1
			);		
			if($shop_data['email'] == '')
			{
				$shop_data['email'] = $un['eShop_Mail'];
			}
			Registry::get('view')->assign('password',$un['password']);
			//try {
				list($user_id,$profile_id)=fn_update_user($un['shop_user_id'],$shop_data,$auth,false,true);
			//}catch (NoMailException $e) {echo 'didn t send mail for user '.$user_id; }
			
			if (empty($user_id)) throw new \Exception('Error syncing user {'.var_export($un,true).'}');
			$un['shop_user_id']=$user_id;
			
			//pame gia ta usergroups
			//disable ola prvta
			foreach ($pricegroup_ids as $pid) {
				if (!fn_change_usergroup_status('D', $un['shop_user_id'], $pid))
					throw new \Exception('Error disabling users group{'.$pid.'} {'.var_export($un,true).'}');
			}
			foreach ($customergroup_ids as $cid) {
				if (!fn_change_usergroup_status('D', $un['shop_user_id'], $cid))
					throw new \Exception('Error disabling users group{'.$cid.'} {'.var_export($un,true).'}');
			}
			// kai bale to diko toy
			if(fn_change_usergroup_status('A', $un['shop_user_id'], $un['shop_pricegroup_id']) && fn_change_usergroup_status('A', $un['shop_user_id'], $un['shop_customergroup_id']))
				$this->update_shop(array('Code'=>$un['Code']), $un);
			else 
				throw new \Exception('Error adding users group {'.var_export($un,true).'}');
				
				
				
			/***************************************************************************
			*                                                                          *
			* 		Dimiourgia kwdikwn QUICK synthesewn gia ta Frontistiria			   *
			*                                                                          *
			****************************************************************************/
			$uid = $un['shop_user_id'];
			$package_list_with_products = db_get_array("SELECT cscart_package_data.package_id, 'D' AS pack_status, (SELECT cscart_package_data.creation FROM cscart_package_data WHERE cscart_package_data.package_id = cscart_package_products.package_id	) AS creation, cscart_package_products.product_id, ( SELECT STATUS FROM cscart_products WHERE cscart_products.product_id = cscart_package_products.product_id	) AS prod_status FROM	cscart_package_data LEFT JOIN cscart_package_products ON cscart_package_data.package_id = cscart_package_products.package_id WHERE cscart_package_data.user_id = '$uid'");
			
			$epaggelma = db_get_field("SELECT value FROM cscart_profile_fields_data WHERE field_id = '43' AND object_id = '$uid'");


			if($un['eShop_Status'] == 'A' && $epaggelma == 'S')	
			{
				$lista_eidwn = db_get_array("SELECT product_id FROM cscart_products WHERE package = 'N' AND status = 'A'");
				
				$lista_eidwn_counter = count($lista_eidwn);
			
				for($i=0; $i<$lista_eidwn_counter; $i++)
				{
					$o = -1;
					$package_list_with_products_counter = count($package_list_with_products);
					for($k = 0; $k<$package_list_with_products_counter; $k++)
					{	
						if(($lista_eidwn[$i]['product_id'] == $package_list_with_products[$k]['product_id']) && ($package_list_with_products[$k]['creation'] == 'Q'))
						{
							$o = $k;
						}			
					}
					if($o > -1)
					{
						$package_list_with_products[$o]['pack_status'] = 'A';
					}
					else
					{
						$package_list_with_products[$package_list_with_products_counter]['package_id'] = '@@';
						$package_list_with_products[$package_list_with_products_counter]['pack_status'] = 'A';
						$package_list_with_products[$package_list_with_products_counter]['creation'] = 'Q';
						$package_list_with_products[$package_list_with_products_counter]['product_id'] = $lista_eidwn[$i]['product_id'];
						$package_list_with_products[$package_list_with_products_counter]['prod_status'] = 'A';
					}			
				}
				
				
				$package_list_with_products_counter = count($package_list_with_products);
				for($k = 0; $k<$package_list_with_products_counter; $k++)
				{
					if($package_list_with_products[$k]['creation'] == 'S')
					{
						$all_active = true;
						for($p = 0; $p<$package_list_with_products_counter; $p++)
						{
							if($package_list_with_products[$k]['package_id'] == $package_list_with_products[$p]['package_id'])
							{						
								if($package_list_with_products[$p]['prod_status'] == 'D')
								{
									$all_active = false;
								}
							}
						}
						
						if($all_active == true)
						{
							$package_list_with_products[$k]['pack_status'] = 'A';
						}
					}
				}
			}
			$package_list_with_products_counter = count($package_list_with_products);			
			for($s=0; $s<$package_list_with_products_counter; $s++)
			{
				if($package_list_with_products[$s]['package_id'] == '@@')
				{				
					// ta 5 prvta einai o kvdikos pelath
					$user_login_for_package = db_get_field("SELECT user_login FROM ?:users WHERE user_id = ?i", $uid);
					$user_code = $user_login_for_package;
					$user_code = str_pad($user_code, 5, "0", STR_PAD_LEFT);
					do 
					{
						$code = $user_code;
						// ta epomena 4 einai tyxaia
						for ($i=0; $i<4; $i++) 
						{
							$code.= mt_rand(0, 9);
						}
						// to teleytaio einai chfio elegxoy
						$code.= sum_till_one_digit($code);
						$code_exists=db_get_field("SELECT COUNT(product_id) FROM ?:products WHERE product_code=?s", $code);
					} 
					while ($code_exists);
		
					
					// gemise ton pinaka ?:products
					$company_id = '1';
					$shipping_params = 'a:5:{s:16:"min_items_in_box";i:0;s:16:"max_items_in_box";i:0;s:10:"box_length";i:0;s:9:"box_width";i:0;s:10:"box_height";i:0;}';
					$package = 'Y';
					$time = time();
					$timestamp = (int)$time;
					db_query("INSERT INTO ?:products (product_code, status, company_id, shipping_params, timestamp, updated_timestamp, package) VALUES ('$code', 'A', '$company_id', '$shipping_params', '$timestamp', '$timestamp', '$package')");
					$package_id = db_get_field("SELECT product_id FROM ?:products WHERE product_code = '$code'");
							
					$user_login = $user_login_for_package;
					$user_id = db_get_field("SELECT user_id FROM ?:users WHERE user_login = ?i", $user_login);
					// gemise ton pinaka ?:package_data
					db_query("INSERT INTO ?:package_data (package_id, user_id, creation) VALUES ('$package_id', '$user_id', 'Q')");
					
					// gemise ton pinaka ?:package_products
					$product_id = $package_list_with_products[$s]['product_id'];
					db_query("INSERT INTO ?:package_products (package_id, product_id) VALUES ('$package_id', '$product_id')");		
						
					// gemise ton pinaka ?:product_descriptions
					$product_desc = db_get_field("SELECT product FROM ?:product_descriptions WHERE product_id = ?i", $product_id);
					$perigrafi = addslashes($product_desc);
								
					//$perigrafi = stripslashes($product_desc);
					db_query("INSERT INTO ?:product_descriptions (product_id, lang_code, product) VALUES ('$package_id', 'el', '$perigrafi')");
							
					// gemise ton pinaka ?:product_prices
					db_query("INSERT INTO ?:product_prices (product_id, price, lower_limit, usergroup_id) VALUES ('$package_id', '0', '1', '0')");
							
					// gemise ton pinaka ?:products_categories
					db_query("INSERT INTO ?:products_categories (product_id, category_id, link_type, position) VALUES ('$package_id', '379', 'M', '0')");
				}
				else
				{
					$package_status = $package_list_with_products[$s]['pack_status'];
					$pid = $package_list_with_products[$s]['package_id'];
					db_query("UPDATE cscart_products SET status = '$package_status' WHERE product_id = '$pid'");
				}
			}
		}
		echo '.. shop synced Users ..<br>';
	}
	
	public function get_unsynced_price_usergroups()
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_array("SELECT DISTINCT Timok FROM {$this->table} WHERE IFNULL(shop_updated,0)=0 AND erp_updated=1");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
	public function get_unsynced_customer_usergroups()
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_array("SELECT DISTINCT eShop_GPermitions as CustomerGroup FROM {$this->table} WHERE IFNULL(shop_updated,0)=0 AND erp_updated=1");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
	protected function get_shop_unsynced_data() //uelv to usergroup_id to shop
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_array("SELECT u.*, g.shop_usergroup_id as shop_pricegroup_id, c.shop_usergroup_id as shop_customergroup_id FROM {$this->table} u 
								LEFT JOIN pricegroup g on u.Timok=g.Timok 
								LEFT JOIN customergroup c on u.eShop_GPermitions=c.CustomerGroup 
								WHERE IFNULL(u.shop_updated,0)=0 AND u.erp_updated=1");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
	public function get_customer_by_shop_id($id) 
	{
		$data='';
		try {
			Db::use_bridge();
			$data=db_get_field("SELECT Code FROM {$this->table} WHERE shop_user_id=?i",$id);
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
  public function get_password_by_shop_id($id) 
	{
		$data='';
		try {
			Db::use_bridge();
			$data=db_get_field("SELECT password FROM {$this->table} WHERE shop_user_id=?i",$id);
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
		$data['shop_user_id']=null;
		$this->update_bridge(array('shop_user_id'=>$id),$data);
	}
	
	public function sum_till_one_digit($number)
	{
		$number = (string) $number;
		while (strlen($number) > 1)
		{
			$tmp = 0;
			for ($i = 0, $j = strlen($number); $i < $j; $i++)
			{
				$tmp += $number[$i];
			}
		
			$number = (string) $tmp;
		}
		return $number;
	}
	
}