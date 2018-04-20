<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  Products  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

namespace Sync;

use Tygh\Registry;

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
		$group_bridge= new Pricegroup();
		$group_data=$this->get_unsynced_Productgroups();
		$group_bridge->set_csv_data($group_data);
		$group_bridge->sync_bridge();
		
		echo '.. Products bridge synced ..<br>';
	}
	
	public function sync_shop()
	{
		
		// sygxronise pvta ta Productgroups
		$group_bridge= new Pricegroup();
		$group_bridge->sync_shop();	
		
		$bridge_fld=fn_erp_sync_get_setting_field('root');
		$images_fld=fn_erp_sync_get_setting_field('product_images');
		$base_image_url=Registry::get('config.http_location')."/$bridge_fld/$images_fld/";
		
		$unsynced=$this->get_shop_unsynced_data();
		echo '.. got Product data for sync ..<br>';
		
		foreach ($unsynced as $un) {
      $categories = explode(',',$un['shop_category_ids']);
      $positions = explode(',',$un['category_positions']);
      
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
			
			$product_id=fn_update_product($shop_data,$un['shop_product_id']);
			
			if (empty($product_id)) throw new \Exception('Error syncing Product {'.var_export($un,true).'}');
			
      //uesh sthn kathgoria
      foreach ($categories as $i=>$cid)
        db_query("UPDATE ?:products_categories SET position = ?i WHERE category_id = ?i AND product_id = ?i", 
          $positions[$i], $cid, $product_id);
      
      //foto
			if (!empty($un['ico1']) && !empty($product_id)) {
				$url=$base_image_url.$un['ico1'];
				$this->updateImagePair($product_id, $url, 'M', $un['name']);
			}
			
			$un['shop_prices']=json_encode($un['prices']);
			$un['shop_product_id']=$product_id;
			$this->update_shop(array('Code'=>$un['Code']), $un);
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
