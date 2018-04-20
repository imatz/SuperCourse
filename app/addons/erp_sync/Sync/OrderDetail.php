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

class OrderDetail extends Master
{
	protected $table='orderst';
	protected $file='OrdersT';
	
	public function sync_bridge($export_no=null,$export_date=null)
	{
		foreach ($this->csv_data as $p) {
			$detail_data = $this->form_detail_data($p);
			
			foreach ($detail_data as $dd) {
				$dd['export_date'] = $export_date;
				$dd['export_no'] = $export_no;
				$this->insert_shop($dd);
			}
		}
		
		unset($this->csv_data);
	}
	
	private function form_detail_data($p)
	{	
		$detail_data = array();
		
		if ('Y'==$p['extra']['package']) {	
			$promotions = array();
			$promotion = $promo_type = '';
			if (!empty($p['promotions'])) { 
				foreach ($p['promotions'] as $prom){
					$promotions[$prom['bonuses'][0]['discount_bonus']][]=$prom['bonuses'][0]['discount_value'];
				}
				
				$by_percentage = (array_key_exists('by_percentage', $promotions))? true: false;
				$by_fixed = (array_key_exists('by_fixed', $promotions))? true: false;
				
				$highest_price = $highest_price_index = 0;
			}
			
			foreach ($p['extra']['package_products'] as $i=>$pp) {
				$price = $pp['taxed_price'];
				$discount = 0;
				
				if (!empty($promotions)) {
					if ($by_percentage) {
						foreach ($promotions['by_percentage'] as $promo_value) {
							$discount = $price * $promo_value / 100;
							$price -= $discount; 
						}
					} 
					if ($by_fixed) {
						if ($price > $highest_price) {
							$highest_price = $price;
							$highest_price_index = $i;
						}
					}
				}
				
				$detail_data[$i]=array(
					'AA'=>$p['order_id'],
					'No'=>$p['item_id'],
					'wh'=>$pp['product_code'],
					'WhName'=>$pp['product'],
					'Quan'=>$p['amount'],
					'TLian'=>$pp['retail_data_no_discounts']['taxed_price'],
					'Timk'=>$p['extra']['pricegroup'] ,
					'Tmon'=>$price,
					'Sum'=>($p['amount']*$price),
					'Notes'=>$this->clean_line_feeds($p['extra']['notes']),
					'Package'=>$p['product_code'],
					'PackageName'=>$p['product'],
					'Discount'=>$discount,
					'Promotion'=>$promotion
				);
			}
			
			if ($by_fixed) {
				$promo_discount = 0;
				foreach ($promotions['by_fixed'] as $promo_value) {
					$promo_discount += $promo_value;
				}
				$detail_data[$highest_price_index]['Discount'] += $promo_discount;
				$detail_data[$highest_price_index]['Tmon'] -= $promo_discount;
				$detail_data[$highest_price_index]['Sum'] = $detail_data[$highest_price_index]['Quan'] * $detail_data[$highest_price_index]['Tmon'];
			}
			
      
		} else {
			$detail_data[] = array(
				'AA'=>$p['order_id'],
				'No'=>$p['item_id'],
				'wh'=>$p['product_code'],
				'WhName'=>$p['product'],
				'Quan'=>$p['amount'],
				'TLian'=>$p['extra']['retail_data_no_discounts']['taxed_price'],
				'Timk'=>$p['extra']['pricegroup'] ,
				'Tmon'=>$p['base_price']+($p['tax_value']/$p['amount'])-($p['discount']/*/$p['amount']*/),
				'Sum'=>$p['subtotal'],
				'Notes'=>$this->clean_line_feeds($p['extra']['notes'])				
			);
		}
		return $detail_data;
	}
	
	public function clear_shop_id($id)
	{
		$data['shop_updated']=-1;
		$data['shop_timestamp']=time();
		$data['AA']=null;
		$this->update_bridge(array('AA'=>$id),$data);
	}	
	
	protected function update_csv($where_data,$data=array())
	{
		$where_data=array(
			'AA'=>$where_data['AA'],
			'NO'=>$where_data['No'],
			'wh'=>$where_data['wh']
		);
		parent::update_csv($where_data,$data);
	}
	
	protected function save_csv()
	{
		$csv = new parseCSV();
		$csv->delimiter = $this->delimiter;
		
		if (!empty($this->csv_data)) {
			$file_data=array();
			$aa=0;
			$header=array_keys($this->csv_data[0]);
			array_pop($header);
			array_pop($header);
			array_pop($header);
			array_pop($header);
			array_pop($header);
			array_pop($header);
			foreach ($this->csv_data as $cd) {
				if($cd['AA']!=$aa) {
					$aa=$cd['AA'];
					$date = date_create($cd['export_date']);
					$name=date_format($date, 'Ymd') . '_' . $cd['export_no'] . '_'. $this->file . '.csv';
					$newpath=BRIDGE_ROOT.$name;
					
					$csv->save($newpath, array(), false, $header);
				}
				unset($cd['shop_updated']);
				unset($cd['shop_timestamp']);
				unset($cd['erp_updated']);
				unset($cd['erp_timestamp']);
				unset($cd['export_date']);
				unset($cd['export_no']);
				
				$csv->save($newpath, array($cd), true, $header);
				$this->update_csv($cd,$cd);
			}
		
		}
		
	}
}
