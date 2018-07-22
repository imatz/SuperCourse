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

fn_add_breadcrumb(__('order_vouchers'));

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