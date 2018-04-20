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

class Category extends Master
{
  protected $table='whcateg';
  protected $file='eShopWHCateg';
  protected $shop_id='shop_category_id';
  protected $dependents=array('Product');    
  
  public function clear()  
  {    
    if (!parent::clear()) return;    
    $category_ids = $this->get_shop_clear_data();
    
    foreach ($category_ids as $cid)
      fn_delete_category($cid,false);
      
    $this->clear_bridge();  
  }  
  
	public function sync_bridge()
	{	
		//ignore all but the last one
		$this->keep_last_csv();
		
		$this->load_csv();
		
		if (!empty($this->csv_data)) $this->reset_erp_updated();
		
		echo '.. Categories csv loaded ..<br>';
		foreach($this->csv_data as $no=>$cd) {
			if($row=$this->get_bridge_row(array('Code'=>$cd['Code']))){
				$this->update_csv(array('Code'=>$cd['Code']), $cd);
			}else{ 
				$this->insert_csv($cd);
			}
		
		}
		$this->mark_csv();
		echo '.. Categories bridge synced ..<br>';
	}
	
	public function sync_shop()
	{
		if (!empty($this->csv_data)) { // an eixame csv
			//pame gia delete osvn den enhmervuhkan
			
			$del_data=$this->get_erp_unsynced_data();
			
			foreach($del_data as $del) {
				if (!empty($del['shop_category_id']) ) {
					fn_delete_category($del['shop_category_id']);
				}
			}
			
			// vraia tvra update
		
			$bridge_fld=fn_erp_sync_get_setting_field('root');
			$images_fld=fn_erp_sync_get_setting_field('category_images');
			$base_image_url=Registry::get('config.http_location')."/$bridge_fld/$images_fld/";
			$unsynced=$this->get_shop_unsynced_data();
			echo '.. got Category data for sync ..<br>';
			foreach ($unsynced as $no => $un) {
				
				$parent_id=0;
				if(!empty($un['Parent'])){
					$parent_row=$this->get_bridge_row(array('Code'=>$un['Parent']));
					if (!empty($parent_row)) $parent_id=$parent_row['shop_category_id'];
				} 
				$shop_data=array(
					"category"=>$un['Name'],
					'status'=>$un['Active'], 
					"parent_id"=>$parent_id,
					'position'=>$un['Sort'],
					'company_id'=>1
				);
				
				if(empty($un['shop_category_id'])) { //create
					
					if (!$category_id=fn_update_category($shop_data, 0))
						throw new \Exception('Error creating category '.var_export($shop_data,true));
						$un['shop_category_id']=$category_id;
				} else { //update
					
					$category_id=$un['shop_category_id'];
					if (!fn_update_category($shop_data, $category_id))
						throw new \Exception('Error updating category '.var_export($shop_data,true));
				}
				
				$this->update_shop(array('Code'=>$un['Code']), $un);
				
				
				if (!empty($un['Ico']) && !empty($category_id)) {
					$url=$base_image_url.$un['Ico'];
					$this->updateImagePair($category_id, $url, 'M', $un['Name']);
				}
				
				
			}
			echo '.. shop synced Categories ..<br>';
		} else echo '.. No data for Categories ..<br>';
	}
	
	private function updateImagePair($id, $url,$type='M', $description='') 
	{
		$detailed = array();
		$im = fn_get_url_data($url);
		$detailed[] = $im;
		$a = fn_get_image_pairs(array($id), 'category', $type, true, true);
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
		
		fn_update_image_pairs(array(), $detailed, $pair_data, $id, 'category');
	}
	
	public function clear_shop_id($id)
	{
		$data['shop_updated']=-1;
		$data['shop_timestamp']=time();
		$data['shop_category_id']=null;
		$this->update_bridge(array('shop_category_id'=>$id),$data);
	}
	
}
