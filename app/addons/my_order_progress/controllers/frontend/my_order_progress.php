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
use Tygh\Storage;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (!empty($_REQUEST['order_id']) && $mode != 'search') {
    // If user is not logged in and trying to see the order, redirect him to login form
    if (empty($auth['user_id'])) {
        return array(CONTROLLER_STATUS_REDIRECT, 'my_changes.my_info?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $orders_company_condition = '';
    if (fn_allowed_for('ULTIMATE')) {
        $orders_company_condition = fn_get_company_condition('?:orders.company_id');
    }

    
    $allowed_id = db_get_field("SELECT user_id FROM ?:orders WHERE user_id = ?i AND order_id = ?i $orders_company_condition", $auth['user_id'], $_REQUEST['order_id']);

    // Check order status (incompleted order)
    if (!empty($allowed_id)) {
        $status = db_get_field("SELECT status FROM ?:orders WHERE order_id = ?i $orders_company_condition", $_REQUEST['order_id']);
        if ($status == STATUS_INCOMPLETED_ORDER) {
            $allowed_id = 0;
        }
    }

    fn_set_hook('is_order_allowed', $_REQUEST['order_id'], $allowed_id);

    if (empty($allowed_id)) { // Access denied

        return array(CONTROLLER_STATUS_DENIED);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

}

fn_add_breadcrumb(__('order_progress'));

//
// Show voucher
//
if ($mode == 'get_voucher') {
	if (empty($_REQUEST['voucher']) || empty($_REQUEST['order_id'])) {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }
	
	$order_id = (int) $_REQUEST['order_id'];
	
	$customer = fn_get_user_login($auth['user_id']);
	$vouchers_utf8 = fn_get_order_vouchers($customer, $order_id);
	$vouchers = fn_get_order_vouchers($customer, $order_id, false);
	
	$voucher = (int) $_REQUEST['voucher'] - 1;
	
	if(empty($vouchers[$voucher])) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}
	
	$path = fn_get_customer_voucher_folder($customer) . '/' . $order_id . '/' . $vouchers[$voucher];
	
	Storage::instance('vouchers')->get($path, $vouchers_utf8[$voucher]);

//
// epejergasia paraggelias
//
} elseif ($mode == 'review') {
	$order = fn_get_order_info($_REQUEST['order_id']);
	$progress_status = fn_get_order_progress_status($order);
	
	if (1 == $progress_status) {
		fn_change_order_status($order['order_id'], 'I');
		fn_set_notification('N', __('important'), __('text_order_cancelled', ["[order_id]" => $order['order_id']]));
		
		fn_reorder($_REQUEST['order_id'], $_SESSION['cart'], $auth);

		return array(CONTROLLER_STATUS_REDIRECT, 'checkout.cart');
	} else {
		return array(CONTROLLER_STATUS_DENIED);
	}


//
// akyrvsh paraggelias
//
} elseif ($mode == 'cancel') {

	$order = fn_get_order_info($_REQUEST['order_id']);
	$progress_status = fn_get_order_progress_status($order);
	
	if (1 == $progress_status) {
		fn_change_order_status($order['order_id'], 'I');
		fn_set_notification('N', __('important'), __('text_order_cancelled', ["[order_id]" => $order['order_id']]));
		
		return array(CONTROLLER_STATUS_REDIRECT, 'my_order_progress.search');
		
	} else {
		return array(CONTROLLER_STATUS_DENIED);
	}
	


//
// Search orders
//
} elseif ($mode == 'search') {
    $params = $_REQUEST;
    if (!empty($auth['user_id'])) {
        $params['user_id'] = $auth['user_id'];
		$params['get_vouchers'] = true;
		$params['status'] = ['O', 'P', 'E'];
    } else {
        return array(CONTROLLER_STATUS_REDIRECT, 'my_changes.my_info?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    list($orders, $search) = fn_get_orders($params, Registry::get('settings.Appearance.orders_per_page'));
	
    Tygh::$app['view']->assign('orders', $orders);
    Tygh::$app['view']->assign('search', $search);
}

// copy of app/controllers/frontend/orders.php
function fn_reorder($order_id, &$cart, &$auth)
{
    $order_info = fn_get_order_info($order_id, false, false, false, true);
    unset($_SESSION['shipping_hash']);
    unset($_SESSION['edit_step']);

    fn_set_hook('reorder', $order_info, $cart, $auth);

    foreach ($order_info['products'] as $k => $item) {
        // refresh company id
        $company_id = db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i", $item['product_id']);
        $order_info['products'][$k]['company_id'] = $company_id;

        unset($order_info['products'][$k]['extra']['ekey_info']);
        $order_info['products'][$k]['product_options'] = empty($order_info['products'][$k]['extra']['product_options']) ? array() : $order_info['products'][$k]['extra']['product_options'];
        $order_info['products'][$k]['main_pair'] = fn_get_cart_product_icon($item['product_id'], $order_info['products'][$k]);
    }

    if (!empty($cart) && !empty($cart['products'])) {
        $cart['products'] = fn_array_merge($cart['products'], $order_info['products']);
    } else {
        $cart['products'] = $order_info['products'];
    }

    foreach ($cart['products'] as $k => $v) {
        $_is_edp = db_get_field("SELECT is_edp FROM ?:products WHERE product_id = ?i", $v['product_id']);
        if ($amount = fn_check_amount_in_stock($v['product_id'], $v['amount'], $v['product_options'], $k, $_is_edp, 0, $cart)) {
            $cart['products'][$k]['amount'] = $amount;

            // Change the path of custom files
            if (!empty($v['extra']['custom_files'])) {
                foreach ($v['extra']['custom_files'] as $option_id => $_data) {
                    if (!empty($_data)) {
                        foreach ($_data as $file_id => $file) {
                            $cart['products'][$k]['extra']['custom_files'][$option_id][$file_id]['path'] = 'sess_data/' . fn_basename($file['path']);
                        }
                    }
                }
            }
        } else {
            unset($cart['products'][$k]);
        }
    }

    // Restore custom files for editing
    $dir_path = 'order_data/' . $order_id;

    if (Storage::instance('custom_files')->isExist($dir_path)) {
        Storage::instance('custom_files')->copy($dir_path, 'sess_data');
    }

    // Redirect customer to step three after reordering
    $cart['payment_updated'] = true;

    fn_save_cart_content($cart, $auth['user_id']);
    unset($cart['product_groups']);
}