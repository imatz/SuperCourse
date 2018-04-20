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
	
	public function sync_bridge()
	{
		foreach ($this->csv_data as $p) {
			$detail_data = $this->form_detail_data($p);
			
			foreach ($detail_data as $dd) $this->insert_shop($dd);
		}
		
		unset($this->csv_data);
	}
	
	private function form_detail_data($p)
	{	
		$detail_data = array();
		
		if ('Y'==$p['extra']['package']) {			
			$promotion = $promo_type = '';
			if (!empty($p['promotions'])) { 
				$promotion = key($p['promotions']);
				$promo_type = $p['promotions'][$promotion]['bonuses'][0]['discount_bonus'];
				$promo_value = $p['promotions'][$promotion]['bonuses'][0]['discount_value'];
				
				$highest_price = $highest_price_index = 0;
			}
			
			foreach ($p['extra']['package_products'] as $i=>$pp) {
				$price = $pp['taxed_price'];
				$discount = 0;
				
				if (!empty($promo_type)) {
					if ('by_percentage' == $promo_type) {
						$discount = $price * $promo_value / 100;
						$price -= $discount; 
					} elseif ('by_fixed' == $promo_type) {
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
					'TLian'=>$pp['retail_data']['taxed_price'],
					'Timk'=>$p['extra']['pricegroup'] ,
					'Tmon'=>$price,
					'Sum'=>$p['subtotal'],
					'Notes'=>$p['extra']['notes'],
					'Package'=>$p['product_code'],
					'PackageName'=>$p['product'],
					'Discount'=>$discount,
					'Promotion'=>$promotion
				);
			}
			
			if (!empty($promo_type) && 'by_fixed' == $promo_type) {
				$detail_data[$highest_price_index]['Discount'] = $promo_value;
				$detail_data[$highest_price_index]['Tmon'] -= $promo_value;
			}
			
		} else {
			$detail_data[] = array(
				'AA'=>$p['order_id'],
				'No'=>$p['item_id'],
				'wh'=>$p['product_code'],
				'WhName'=>$p['product'],
				'Quan'=>$p['amount'],
				'TLian'=>$p['extra']['retail_data']['taxed_price'],
				'Timk'=>$p['extra']['pricegroup'] ,
				'Tmon'=>$p['base_price']+($p['tax_value']/$p['amount'])-($p['discount']/$p['amount']),
				'Sum'=>$p['subtotal'],
				'Notes'=>$p['extra']['notes']				
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
	
	protected function update_csv($where_data,$data)
	{
		$where_data=array(
			'AA'=>$where_data['AA'],
			'NO'=>$where_data['No'],
			'wh'=>$where_data['wh']
		);
		parent::update_csv($where_data,$data);
	}
}
