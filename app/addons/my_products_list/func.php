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

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_check_product_list($product_list)
{
	$product_data = array();
	
	$products = fn_get_products(array());
	$products = $products[0];
	
	if (empty($product_list)) {
		fn_set_notification('W', __('warning'), __('products_list_empty'));
	} else {
		foreach($product_list as $key=>$pl) {
			$product_list[$key]['error'] = '';
			
			if (empty($pl['B'])) {
				unset($product_list[$key]);
				continue;
			} else if (empty($pl['A'])) {
				$product_list[$key]['error'] = __('products_list_no_code');
				continue;
			}
		
			$amount = (int) $pl['B'];

			if ($amount <= 0){
				$product_list[$key]['error'] = __('products_list_wrong_quantity');
			}
			
			$product_row = db_get_row("SELECT product_id, package FROM ?:products WHERE status='A' AND product_code = ?s", $pl['A']);
			if (empty($product_row['product_id']) || ('Y' != $product_row['package'] && empty($products[$product_row['product_id']]))) {
				$product_list[$key]['error'] .= ' ' . __('products_list_wrong_code');
			} else {
				if ('Y' != $product_row['package']) {
					$product_list[$key]['product'] = $products[$product_row['product_id']]['product']; 
				} else {
					list($tmp, $junk)=fn_get_products(array('pid'=>array($product_row['product_id']),'packages'=>'Y'));
					$product_list[$key]['product'] = $tmp[$product_row['product_id']]['product'];
				}
			}
			
			if (empty($product_list[$key]['error']))
				$product_data[$product_row['product_id']] = array('product_id'=>$product_row['product_id'], 'amount'=>$amount);
		}
	
	}
	
	return array($product_list, $product_data);
}

function fn_add_to_cart_product_list($product_data, &$auth) {
	if (empty($_SESSION['cart'])) {
		fn_clear_cart($_SESSION['cart']);
	}

	$cart = & $_SESSION['cart'];

	$prev_cart_products = empty($cart['products']) ? array() : $cart['products'];

	fn_add_product_to_cart($product_data, $cart, $auth);
	fn_save_cart_content($cart, $auth['user_id']);

	$previous_state = md5(serialize($prev_cart_products));
	$cart['change_cart_products'] = true;
	fn_calculate_cart_content($cart, $auth, 'S', true, 'F', true);

	if (md5(serialize($cart['products'])) != $previous_state && empty($cart['skip_notification'])) {
		$product_cnt = 0;
		$added_products = array();
		foreach ($cart['products'] as $key => $data) {
			if (empty($prev_cart_products[$key]) || !empty($prev_cart_products[$key]) && $prev_cart_products[$key]['amount'] != $data['amount']) {
				$added_products[$key] = $data;
				$added_products[$key]['product_option_data'] = fn_get_selected_product_options_info($data['product_options']);
				if (!empty($prev_cart_products[$key])) {
					$added_products[$key]['amount'] = $data['amount'] - $prev_cart_products[$key]['amount'];
				}
				$product_cnt += $added_products[$key]['amount'];
			}
		}

		if (!empty($added_products)) {
			Tygh::$app['view']->assign('added_products', $added_products);
			if (Registry::get('config.tweaks.disable_dhtml') && Registry::get('config.tweaks.redirect_to_cart')) {
				Tygh::$app['view']->assign('continue_url', (!empty($_REQUEST['redirect_url']) && empty($_REQUEST['appearance']['details_page'])) ? $_REQUEST['redirect_url'] : $_SESSION['continue_url']);
			}

			$msg = Tygh::$app['view']->fetch('views/checkout/components/product_notification.tpl');
			fn_set_notification('I', __($product_cnt > 1 ? 'products_added_to_cart' : 'product_added_to_cart'), $msg, 'I');
			$cart['recalculate'] = true;
		} else {
			fn_set_notification('N', __('notice'), __('product_in_cart'));
		}
	}

	unset($cart['skip_notification']);

	if (Registry::get('config.tweaks.disable_dhtml') && Registry::get('config.tweaks.redirect_to_cart') && !defined('AJAX_REQUEST')) {
		if (!empty($_REQUEST['redirect_url']) && empty($_REQUEST['appearance']['details_page'])) {
			$_SESSION['continue_url'] = fn_url_remove_service_params($_REQUEST['redirect_url']);
		}
		unset($_REQUEST['redirect_url']);
	}
}