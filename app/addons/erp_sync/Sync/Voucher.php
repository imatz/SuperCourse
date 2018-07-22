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

use Tygh\Storage;

class Voucher extends Master
{
	protected $table='voucher';
	/***
	 * Oi fakeloi einai ths morfhs: Kvd.Pelath/Kvd.Paraggelias/Parastatika
	 * Antigrafontai apo fakelo gefyras->var/files/
	 * Emfanizontai sto my_order_vouchers me keimeno syndesmoy to onoma arxeioy
	 */
	
	public function sync_bridge()
	{
		
	}
	
	public function sync_shop()
	{
		$bridge_dir = BRIDGE_ROOT.'vouchers';
		
		if (file_exists($bridge_dir)) {
			echo '.. Voucher directory Found ..<br>';
		
			$customers = fn_get_dir_contents($bridge_dir);
			// fn_print_r($customers);
			if (!empty($customers)) {
				if (defined('IS_WINDOWS')) { 
					$tmp = setlocale(LC_CTYPE, 0); 
					$SYS_LOCALE_CHARSET = 'Windows-'.substr($tmp, -4); 
				}
				
				$params = array(
					'overwrite' => true,
				);
				
				foreach ($customers as $customer) {
					$customer_dir = $bridge_dir . '/' . $customer;
					$orders = fn_get_dir_contents($customer_dir);
					//fn_print_r($orders);
					
					$wrong_orders = db_get_fields('SELECT order_id FROM ?:orders WHERE order_id IN (?n) AND user_login != ?i', $orders, $customer);
					if (!empty($wrong_orders)) {
						throw new \Exception('Wrong orders for customer:'.$customer.'['.implode(', ', $wrong_orders).']');
					}
			
					foreach ($orders as $order) {
						$order_dir = $customer_dir . '/' . $order;
						
						$files = fn_get_dir_contents($order_dir, false, true);
						
						foreach ($files as $file) {
							
							if (defined('IS_WINDOWS')) {
								$utf_file = iconv($SYS_LOCALE_CHARSET, 'UTF-8', $file);
							} else {
								$utf_file = $file;
							}
							
							$file_path = $order_dir . '/' . $file;
							$storage_path = fn_get_customer_voucher_folder($customer) . '/' . $order . '/' . $file;
							
							$params['file'] = $file_path;
							$cp = Storage::instance('vouchers')->put($storage_path, $params);
							if (empty($cp)) {
								throw new \Exception('Could not copy '.$file_path);
							}
							
							$data = array('Customer'=>$customer, 'order_id'=>$order, 'file'=>$utf_file);
							$this->insert_shop($data);
						}
						
						$file_leftovers = fn_get_dir_contents($order_dir, false, true, '', '', true);
						if (empty($file_leftovers)) {
							fn_rm($order_dir);
						}	
					}
					
					$order_leftovers = fn_get_dir_contents($customer_dir);
					if (empty($order_leftovers)) {
						fn_rm($customer_dir);
					}					
				}
				
				echo '.. shop synced Vouchers ..<br>';
			} else {
				echo '.. No files for Vouchers ..<br>';
			}
		} else {
			echo '.. Voucher directory NOT Found ..<br>';
		}
	}
	
}
