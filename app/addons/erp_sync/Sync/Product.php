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

use Tygh\Registry;
use Tygh\Mailer;

class Product extends Master
{
  	protected $table='whouse';
	protected $file='WHouse';
  	protected $shop_id='shop_product_id';
	protected $dependents=array('ProductPackage');
  
	private $taxes=array();
	private $category_map=array();

  public function clear()  
  {    
    if (!parent::clear()) return;    
    $product_ids = $this->get_shop_clear_data();
    
    foreach ($product_ids as $pid)
      fn_delete_product($pid);
          $this->clear_bridge();
  } 
  
	public function sync_bridge()
	{
		$this->fill_taxes_map();
		$this->fill_category_map();
		$this->load_csv();
		echo '.. Products csv loaded ..<br>';
		foreach($this->csv_data as $no=>$cd) {
			list($cd['shop_category_ids'],$cd['shop_main_category'],$cd['category_positions'])=$this->get_shop_product_categories($cd);
			if (empty($cd['shop_category_ids'])) continue;
			if($row=$this->get_bridge_row(array('Code'=>$cd['Code']))){
				$this->update_csv(array('Code'=>$cd['Code']), $cd);
			}else{ 
				$this->insert_csv($cd);
			}
	
			$this->update_bridge_prices($cd);
		}
		$this->mark_csv();
		// ok Productgroups
		$Pricegroup_bridge= new Pricegroup();
		$Pricegroup_bridge_data=$this->get_unsynced_Productgroups();
		$Pricegroup_bridge->set_csv_data($Pricegroup_bridge_data);
		$Pricegroup_bridge->sync_bridge();
		
		// ok Customergroups
		
		$Customergroup_bridge= new Customergroup();
		$Customergroup_bridge_data=$this->get_unsynced_Customergroups();
		$Customergroup_bridge->set_csv_data($Customergroup_bridge_data);
		$Customergroup_bridge->sync_bridge();
		
		
		echo '.. Products bridge synced ..<br>';
	}
	
	public function sync_shop()
	{
		// sygxronise pvta ta Productgroups
		$Pricegroup_bridge= new Pricegroup();
		$Pricegroup_bridge->sync_shop();	
		
		$Customergroup_bridge= new Customergroup();
		$Customergroup_bridge->sync_shop();	
		
		$Customergroup_map = Customergroup::get_Customergroup_ids();
		
		$bridge_fld=fn_erp_sync_get_setting_field('root');
		$images_fld=fn_erp_sync_get_setting_field('product_images');
		$base_image_url=Registry::get('config.http_location')."/$bridge_fld/$images_fld/";
		
		$unsynced=$this->get_shop_unsynced_data();
		echo '.. got Product data for sync ..<br>';

		foreach ($unsynced as $un) 
		{
			$categories = explode(',',$un['shop_category_ids']);
			$positions = explode(',',$un['category_positions']);
			
			//-------------------------------------------------------------------
			// liampas - elegxos gia to an uparxei to proion auto sto eshop
			//-------------------------------------------------------------------
			//pare to kwdiko proiontos apo tin bridge
			$code = $un['Code'];	
			//an uparxei to proion tote product_exists=1
			$product_exists = db_get_field ("SELECT COUNT(*) FROM cscart_products WHERE product_code='$code'");
			//pare to status toy eshop (?:products) toy proiontos pou erxetai sto csv
			$status = db_get_field("SELECT status FROM cscart_products WHERE product_id = ?i", $un['shop_product_id']);
			
			$shop_data=array(
				'status'=>$un['Active'],
				'product'=>$un['name'],
				'full_description'=>$un['perigrafh'],
				'short_description'=>'',
				'product_alt'=>$un['packageName'],
				'category_ids'=>$categories,
				'main_category'=>$un['shop_main_category'],
				'product_code'=>$un['Code'],
				'tax_ids'=>array($this->taxes[$un['SFPA']]['shop_tax_id']=>$this->taxes[$un['SFPA']]['shop_tax_id']),
				'price'=>$un['prices'][0]['price'],
				'prices'=>$un['prices'],
				'company_id'=>1,
			);		
			
			// usergroups
			$cGroup = trim($un['CustomerGroup']);
			
			if (!empty($cGroup)) {
				$tmp = explode(',', $cGroup);
				foreach ($tmp as $t) {
					if (!empty($Customergroup_map[$t])) {
						$shop_data['usergroup_ids'][] = $Customergroup_map[$t];
					}
				}
			} else { //noone
				$shop_data['usergroup_ids'][] = $Customergroup_map['N'];
			}
			
			
			$product_id=fn_update_product($shop_data,$un['shop_product_id']);
		
			if(empty($un['shop_product_id'])) {
				// 07/2018 enhmervse tis synueseis gia to neo product_id
				db_query('UPDATE ?:package_products SET product_id = ?i WHERE product_code = ?s', $product_id, $code);
			}
		
			if (empty($product_id)) throw new \Exception('Error syncing Product {'.var_export($un,true).'}');
			
			//------------------------------------------------------------------------------------------------------------------------------------------------
			// liampas - elegxos gia na ekteleitai o sygxronismos mono an den uparxei to proion auto sto eshop H an to status stis 2 baseis einai diaforetiko
			//------------------------------------------------------------------------------------------------------------------------------------------------
			if($product_exists == 0 || $un['Active']!= $status)
			{
			
			//----------------------------------------------------------------------------------------------------------------------
			// nikosgkil - elegxos gia apenergopoiimena proionta kai metepeita apenergopoiisi synthesewn an vrethoyn tetoia proionta
			//----------------------------------------------------------------------------------------------------------------------
					
			if($un['Active'] == 'D' &&  $status == 'A')
			{
				//$package_id = db_get_fields("SELECT package_id FROM cscart_package_products WHERE product_id = ?i", $product_id);
				$package_id = db_get_fields("SELECT package_id FROM cscart_package_products 
WHERE product_id = '$product_id' AND (SELECT creation FROM cscart_package_data WHERE cscart_package_data.package_id = cscart_package_products.package_id) = 'S'");
				$status_changed = 0;	
				$packages_counter = count($package_id);
				for($i=0; $i<$packages_counter; $i++)
				{
					if(db_get_field("SELECT status FROM cscart_products WHERE product_id = ?i", $package_id[$i]) == 'A')
					{
						db_query("UPDATE cscart_products SET status = 'D', flag = '1' WHERE product_id = ?i", $package_id[$i]);
						$status_changed = 1;
						$user_ids[] = db_get_field("SELECT user_id FROM cscart_package_data WHERE package_id = ?i", $package_id[$i]);
					}
					
					$email_to = db_get_field("SELECT fmail FROM cscart_users WHERE user_id = ?i", $user_ids[$i]);
					$product_code = db_get_field("SELECT product_code FROM cscart_products WHERE product_id = ?i", $package_id[$i]);
					$package_name = db_get_field("SELECT product FROM cscart_product_descriptions WHERE product_id = ?i", $package_id[$i]);
					$product_status = db_get_field("SELECT status FROM cscart_products WHERE product_id = ?i", $package_id[$i]);
					$product_creation = db_get_field("SELECT creation FROM cscart_package_data WHERE package_id = ?i", $package_id[$i]);
					
					// steile email enimerwsis ston pelati gia apenergopoiisi tis synthesis toy anaferontas poio proion apenergopoiithike
					if($status_changed == 1 && $product_creation == 'S')
					{
						Mailer::sendMail(array(
							'to' => $email_to,
							'from' => 'company_users_department',
							'reply_to' => $un['shop_user_id'],
							'data' => array(
								'user_data' => $user_data,
								'product_name' => $un['name'],
								'syntheseis' => $product_code,
								'package_name' => $package_name,
								'product_status' => $product_status,
								'email_to' => $email_to[$i],
							),
							'tpl' => 'profiles/disabled_product.tpl',
							'company_id' => $shop_data['full_description']
						), 'A', Registry::get('settings.Appearance.backend_default_language'));
					}
					// enimerwtiko email
				}
			}
						
			// lanthasmeni allagi se disabled to proion
			if(($un['Active'] == 'A' &&  $status == 'D')|| ($un['Active'] == 'A' &&  $status == 'A'))
			{
				$package_id = db_get_fields("SELECT package_id FROM cscart_package_products 
WHERE product_id = '$product_id' AND (SELECT creation FROM cscart_package_data WHERE cscart_package_data.package_id = cscart_package_products.package_id) = 'S'");
				$packages_counter = count($package_id);		
				//$customer_list1 = db_get_array("SELECT Code FROM supercou_bridge26s.customer WHERE eShop_GPermitions = 'S' AND eShop_Status = 'A'");
				//$customer_list_counter1 = count($customer_list1);		
				
				if($packages_counter > 0)
				{
					for($i=0; $i<$packages_counter; $i++)
					{
						$user_ids[] = db_get_field("SELECT user_id FROM cscart_package_data WHERE package_id = ?i", $package_id[$i]);
						$email_to[] = db_get_field("SELECT fmail FROM cscart_users WHERE status = 'A' AND user_id = ?i", $user_ids[$i]);
					}
				
					// vres ta proionta kathe synthesis
					for($i=0; $i<$packages_counter; $i++)
					{
						$prods_in_package[$i] = db_get_fields("SELECT product_id FROM cscart_package_products WHERE package_id = ?i", $package_id[$i]);	
					}
		
					for($i=0; $i<$packages_counter; $i++)
					{
						$in_pack_counter[] += count($prods_in_package[$i]);
					}
		
					for($i=0; $i<$packages_counter; $i++)
					{
						for($j=0; $j<$in_pack_counter[$i]; $j++)
						{
							$statuses[$i][] = db_get_field("SELECT status FROM cscart_products WHERE product_id = ?i", $prods_in_package[$i][$j]);
						}
					}
		
					for($i=0; $i<$packages_counter; $i++)
					{
						$disabled_counter = 0; 
						for($j=0; $j<$in_pack_counter[$i]; $j++)
						{
							if($statuses[$i][$j] == 'D')
							{
								$disabled_counter = $disabled_counter + 1;
							}
						}
								
								
					  //------------------------------------------------------------------------------------------------------------------------
					  //------------------------------------------------------------------------------------------------------------------------
					  //------------------------------------------------------------------------------------------------------------------------
					  //-----------prepei na ginei check oti stelnei ta email stis swstes dieuthinseis!!!!!!!!!!!!!!!!!!!!!!!!!-----------------
					  //------------------------------------------------------------------------------------------------------------------------
					  //------------------------------------------------------------------------------------------------------------------------
					  //------------------------------------------------------------------------------------------------------------------------
					  
					  $product_code = db_get_field("SELECT product_code FROM cscart_products WHERE product_id = ?i", $package_id[$i]);
					  $package_name = db_get_field("SELECT product FROM cscart_product_descriptions WHERE product_id = ?i", $package_id[$i]);
					  $product_creation = db_get_field("SELECT creation FROM cscart_package_data WHERE package_id = ?i", $package_id[$i]);
					  
					  if($disabled_counter == 0 && db_get_field("SELECT flag FROM cscart_products WHERE product_id = ?i", $package_id[$i]) == '1')
					  {
						  if(db_get_field("SELECT status FROM cscart_users WHERE user_id = ?i", $user_ids[$i]) == 'A')
						  {
							  db_query("UPDATE cscart_products SET status = 'A', flag = '0' WHERE product_id = ?i", $package_id[$i]);
							  $product_status = db_get_field("SELECT status FROM cscart_products WHERE product_id = ?i", $package_id[$i]);
							  // steile email enimerwsis ston pelati gia ek neou energopoiisi tis synthesis toy anaferontas to lathos kai ean i synthesi einai "S"
							  if($product_creation == 'S')
							  {
								  Mailer::sendMail(array(
									  'to' => $email_to[$i],
									  'from' => 'company_users_department',
									  'reply_to' => $un['shop_user_id'],
									  'data' => array(
										  'user_data' => $user_data,
										  'product_name' => $un['name'],
										  'syntheseis' => $product_code,
										  'package_name' => $package_name,
										  'product_status' => $product_status,
										  'email_to' => $email_to[$i],
										  ),
									  'tpl' => 'profiles/re_enabled_product.tpl',
									  'company_id' => $shop_data['full_description']
									  ), 'A', Registry::get('settings.Appearance.backend_default_language'));
								  // enimerwtiko email
							  } 
						  }
					  }
					}
				}
			}
			//----------------------------------------------------------------------------------------------------------------------
			//<-- nikosgkil---------------------------------------------------------------------------------------------------------
			//----------------------------------------------------------------------------------------------------------------------
			
		
		
		$package_list_with_product = db_get_fields("SELECT package_id FROM cscart_package_products 
WHERE product_id = '$product_id' AND (SELECT creation FROM cscart_package_data WHERE cscart_package_data.package_id = cscart_package_products.package_id) = 'Q'");
		$package_list_with_product_counter = count($package_list_with_product);
		for($i=0; $i<$package_list_with_product_counter; $i++)
		{
			$package_data_list[] = db_get_array("SELECT package_id, user_id, 'D' AS status FROM ?:package_data WHERE package_id = ?i", $package_list_with_product[$i]);
		}
		$lista_code_front = db_get_array("SELECT object_id,user_id,status FROM cscart_profile_fields_data,cscart_users WHERE field_id = '43' AND value = 'S' AND status = 'A' AND object_id=user_id GROUP BY object_id");
		$lista_code_front_counter = count($lista_code_front);
		if($un['Active'] == 'A')
		{
			for($j = 0; $j<$lista_code_front_counter; $j++)
			{
				$kwdikos = (int)$lista_code_front[$j]['object_id'];
				
				$o = -1;
				$package_data_counter = count($package_data_list);
				for($k = 0; $k<$package_data_counter; $k++)
				{	
					$uid = (int)$package_data_list[$k][0]['user_id'];
					if($kwdikos == $uid)
					{	
						$o = $k;
					}
				}
				
				if($o > -1)
				{
					$package_data_list[$o][0]['status'] = 'A';
				}
				else
				{
					$package_data_list[$package_data_counter][0]['package_id'] = '@@';
					$package_data_list[$package_data_counter][0]['user_id'] = $kwdikos;
					$package_data_list[$package_data_counter][0]['status'] = 'A';
				}			
			}
		}
		
		$package_list_counter = count($package_data_list);
		
		for($m=0; $m<$package_list_counter; $m++)
		{
			if($package_data_list[$m][0]['package_id'] == '@@')
			{				
					// ta 5 prvta einai o kvdikos pelath
					$user_login_for_package = db_get_field("SELECT user_login FROM ?:users WHERE user_id = ?i", $package_data_list[$m][0]['user_id']);
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
					$package_status = $package_data_list[$m][0]['status'];
					$pid = $package_data_list[$m][0]['package_id'];
					db_query("UPDATE cscart_products SET status = '$package_status' WHERE product_id = ?i", $pid);
				}
			}
			
			//uesh sthn kathgoria
      		foreach ($categories as $i=>$cid)
        		db_query("UPDATE ?:products_categories SET position = ?i WHERE category_id = ?i AND product_id = ?i", 
          		$positions[$i], $cid, $product_id);
				
				
     	 	//foto
			if (!empty($un['ico1']) && !empty($product_id)) 
			{
				$url=$base_image_url.$un['ico1'];
				$this->updateImagePair($product_id, $url, 'M', $un['name']);
			}
			
			$un['shop_prices']=json_encode($un['prices']);
			$un['shop_product_id']=$product_id;
			$this->update_shop(array('Code'=>$un['Code']), $un);
			
		   }
		}
		echo '.. shop synced Products ..<br>';

	}
	
	public function get_unsynced_Productgroups()
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_array("SELECT DISTINCT Timok FROM whouseprice p 
			INNER JOIN {$this->table} w on p.Code=w.Code
			WHERE IFNULL(w.shop_updated,0)=0 AND w.erp_updated=1");
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
	public function get_unsynced_Customergroups()
	{
		$data=array();
		$data['R'] = array('CustomerGroup'=>'R'); // Lianikh einai standard
		$data['Ν'] = array('CustomerGroup'=>'N'); // Noone an den steilei kati
		try {
			Db::use_bridge();
			$result=db_get_array("SELECT DISTINCT CustomerGroup FROM {$this->table} WHERE IFNULL(shop_updated,0)=0 AND erp_updated=1 AND CustomerGroup IS NOT NULL AND CustomerGroup <> ''");
			Db::use_shop();			
			
			foreach ($result as $res) {
				$tmp = explode(',', $res['CustomerGroup']);
				foreach ($tmp as $t) {
					if (empty($data[$t])) {
						$data[$t]=array('CustomerGroup'=>$t);
					}
				}
			}
			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
	
	
	protected function get_shop_unsynced_data() //uelv to Productgroup_id to shop
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_array("SELECT * FROM {$this->table} WHERE IFNULL(shop_updated,0)=0 AND erp_updated=1");
			// bale tis times
			foreach ($data as &$d) $d['prices']=$this->get_shop_prices($d['Code']);
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
		return $data;
	}
  
	protected function get_shop_prices($code) 
	{
		$data=array();
		try {
			Db::use_bridge();
			$data=db_get_array("SELECT g.shop_usergroup_id as usergroup_id, p.Timh as price, 'A' as type, 1 as lower_limit
			FROM whouseprice p LEFT JOIN pricegroup g on p.Timok=g.Timok WHERE p.Code=?s ORDER BY p.Timok",$code);
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
		$data['shop_product_id']=null;
		$this->update_bridge(array('shop_product_id'=>$id),$data);
	}
	
	private function updateImagePair($id, $url,$type='M', $description='') 
	{
		$detailed = array();
		$im = fn_get_url_data($url);
		$detailed[] = $im;
		$a = fn_get_image_pairs(array($id), 'product', $type, true, true);
		$pair = array_shift($a);
		if( !isset($pair['pair_id']) )
			$pair['pair_id']=0;
			$pair_data[] = array(
			"pair_id" => $pair['pair_id'],
			"type" => $type,
			"object_id" => $id,
			"image_alt" => $description,
			"detailed_alt" => $description,
		);
		
		fn_update_image_pairs(array(), $detailed, $pair_data, $id, 'product');
	}
	
	/*
	* Child Functions
	*/
	protected function update_bridge_prices($cd)
	{
		try {
			Db::use_bridge();
			
			$price_arr=explode('@',$cd['Timh']);
			$price_data=array();
			foreach ($price_arr as $no=>$price) {
				$price_data[]=array(
					'Code'=>$cd['Code'],
					'Timh'=>$this->calculate_net_price($price,$cd['SFPA']),
					'Timok'=>$no+1
				);
			}
			
			db_query("DELETE FROM whouseprice WHERE Code=?s",$cd['Code']);
			db_query("INSERT INTO whouseprice ?m",$price_data);
			
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
	
  
  // csv format: cat1-pos1@cat2-pos2@cat3-pos3
	protected function get_shop_product_categories($cd)
	{
		$categ_arr=explode('@',$cd['eShopWHCateg']);
		$shop_categories = $category_positions = array();
		foreach ($categ_arr as $cat) {
      
      $tmp = explode('-', $cat);
      if (count($tmp)!=2) {
        echo 'category - position NOT PROVIDED or WRONG FROMAT'; print_r($cd); echo '<br>';
        continue;
      }
      
      list ($category, $position) = $tmp; 
			if (!empty($this->category_map[$category])) {
        $shop_categories []= $this->category_map[$category];
        $category_positions []= $position;
      } else {
				echo 'category NOT PROVIDED'; print_r($cd); echo '<br>';
			} 
		}
			
		return (empty($shop_categories))? array('','','') :array(implode(',',$shop_categories),$shop_categories[0], implode(',',$category_positions));
	}
	
	/*
	* Cache functions
	*/
	
	private function fill_taxes_map()
	{
		try {
			Db::use_bridge();
			$this->taxes=db_get_hash_array("SELECT * FROM fpa",'Syntelesths');
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
	
	private function fill_category_map()
	{
		try {
			Db::use_bridge();
			$this->category_map=db_get_hash_single_array("SELECT Code,shop_category_id FROM whcateg WHERE shop_category_id IS NOT NULL",array('Code','shop_category_id'));
			Db::use_shop();			
		}catch (\Exception $e){
			Db::use_shop();
			throw $e;
		}
	}
	
	/*
	* Helper Functions
	*/ 
	
	private function calculate_net_price($price,$SFPA)
	{
		$fpa=$this->taxes[$SFPA]['pos1'];
		$divisor=1+$fpa/100;
		$price=str_replace(',','.',$price);
		$net_price = $price/$divisor;
		
		return $net_price;
	}
	
	
}
