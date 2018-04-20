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

fn_define('ORDER_MANAGEMENT', true); // Defines that the cart is in order management mode now

$_SESSION['cart'] = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$cart = & $_SESSION['cart'];

$_SESSION['customer_auth'] = isset($_SESSION['customer_auth']) ? $_SESSION['customer_auth'] : array();
$customer_auth = & $_SESSION['customer_auth'];

$_SESSION['shipping_rates'] = isset($_SESSION['shipping_rates']) ? $_SESSION['shipping_rates'] : array();
$shipping_rates = & $_SESSION['shipping_rates'];

if (empty($customer_auth)) {
    $customer_auth = fn_fill_auth(array(), array(), false, 'C');
}

$_suffix = !empty($cart['order_id']) ? 'update' : 'add';

if (fn_allowed_for('ULTIMATE') && $mode != 'edit' && $mode != 'new') {
    if (
        (Registry::get('runtime.company_id') && !empty($cart['order_company_id']) && Registry::get('runtime.company_id') != $cart['order_company_id'])
        || (!Registry::get('runtime.company_id') && !empty($cart['order_company_id']))
    ) {
        if (Registry::get('runtime.company_id')) {
            fn_set_notification('W', __('warning'), __('orders_not_allow_to_change_company'));
        }

        if (fn_get_available_company_ids($cart['order_company_id'])) {
            return array(CONTROLLER_STATUS_REDIRECT, fn_link_attach(Registry::get('config.current_url'), 'switch_company_id=' . $cart['order_company_id']));
        } else {
            return array(CONTROLLER_STATUS_DENIED);
        }

    } elseif (empty($cart['order_company_id']) && Registry::get('runtime.company_id')) {
        $cart['order_company_id'] = Registry::get('runtime.company_id');
    }
}


if ($mode == 'edit' && !empty($_REQUEST['order_id'])) {
    fn_clear_cart($cart, true);
    $customer_auth = fn_fill_auth(array(), array(), false, 'C');

    $cart_status = md5(serialize($cart));
    
    fn_form_cart_with_packages($_REQUEST['order_id'], $cart, $customer_auth);
    
// fn_print_die($cart);
    if (!empty($cart['product_groups'])) {
        foreach ($cart['product_groups'] as $group_key => $group) {
            if (!empty($group['chosen_shippings'])) {
                foreach ($group['chosen_shippings'] as $shipping_key => $shipping) {
                    if (!empty($shipping['stored_shipping']) && empty($cart['stored_shipping'][$group_key][$shipping_key])) {
                        $cart['stored_shipping'][$group_key][$shipping_key] = $shipping['rate'];
                    }
                }
            }
        }
    }

    if ($cart_status == md5(serialize($cart))) {
        // Order info was not found or customer does not have enought permissions
        return array(CONTROLLER_STATUS_DENIED, '');
    }
    $cart['order_id'] = $_REQUEST['order_id'];

    return array(CONTROLLER_STATUS_REDIRECT, "order_management.update");

}

function fn_form_cart_with_packages($order_id, &$cart, &$auth)
{
    $order_info = fn_get_order_info($order_id, false, false);

    if (empty($order_info)) {
        fn_set_notification('E', __('error'), __('object_not_found', array('[object]' => __('order'))),'','404');

        return false;
    }
    $p_ids = db_get_fields('SELECT a.product_id FROM ?:order_details as a INNER JOIN ?:products as b ON a.product_id = b.product_id WHERE b.package = ?s AND a.order_id = ?i', 'Y', $order_id);
    if (!empty($p_ids)) {
        $statuses = fn_get_statuses(STATUSES_ORDER, array(), true, false, ($order_info['lang_code'] ? $order_info['lang_code'] : CART_LANGUAGE), $order_info['company_id']);
        if ($statuses[$order_info['status']]['params']['inventory'] == 'D') {
            fn_change_order_status($order_id, 'I', $order_info['status'], array('C' => false, 'A' => false, 'V' => false));
            fn_set_notification('W', __('warning'), __('order_status_was_changed'));
        }
    
        $packages = array();
        foreach ($order_info['products'] as $k=>$v) {
            if (isset($v['extra']['package_hash'])) {
                $packages[$k][$v['product_id']] = array(
                    'product_id' => $v['product_id'],
                    'amount' => $v['amount'],
                    'package' => array()
                );
                $package = array();
                foreach ($order_info['products'] as $k1=>$v1) {
                    if (isset($v1['extra']['package_info']['p_id']) && $v1['extra']['package_info']['p_id'] == $k) {
                        $package_info = $v1['extra']['package_info'];
                        $package[$package_info['table_key']][$package_info['inc']] = array(
                            'product_id' => $v1['product_id'],
                            'amount' => round($v1['amount']/$v['amount']),
                            'product_options' => $v1['extra']['product_options']
                        );
                        unset($order_info['products'][$k1]);
                    }
                }
                $packages[$k][$v['product_id']]['package'] = $package;
                unset($order_info['products'][$k]);
            } 
        }
        if (!empty($packages)) {
            foreach ($packages as $v) {
                $_REQUEST['product_data'] = $v;
                fn_add_product_to_cart($v, $cart, $auth);
            }
        }
    }
    // Fill the cart
    foreach ($order_info['products'] as $_id => $item) {
        $_item = array (
            $item['product_id'] => array (
                'product_id' => $item['product_id'],
                'amount' => $item['amount'],
                'product_options' => (!empty($item['extra']['product_options']) ? $item['extra']['product_options'] : array()),
                'price' => $item['original_price'],
                'stored_discount' => 'Y',
                'stored_price' => 'Y',
                'discount' => (!empty($item['extra']['discount']) ? $item['extra']['discount'] : 0),
                'original_amount' => $item['amount'], // the original amount, that stored in order
                'original_product_data' => array ( // the original cart ID and amount, that stored in order
                    'cart_id' => $_id,
                    'amount' => $item['amount'],
                ),
            ),
        );
        if (isset($item['extra'])) {
            $_item[$item['product_id']]['extra'] = $item['extra'];
        }

        fn_add_product_to_cart($_item, $cart, $auth);
    }

    // Workaround for the add-ons that do not add a product to cart unless the parent product is already added.
    if (count($order_info['products']) > count($cart['products'])) {
        foreach ($order_info['products'] as $_id => $item) {
            if (empty($cart['products'][$_id])) {
                $_item = array (
                    $item['product_id'] => array (
                        'amount' => $item['amount'],
                        'product_options' => (!empty($item['extra']['product_options']) ? $item['extra']['product_options'] : array()),
                        'price' => $item['original_price'],
                        'stored_discount' => 'Y',
                        'stored_price' => 'Y',
                        'discount' => (!empty($item['extra']['discount']) ? $item['extra']['discount'] : 0),
                        'original_amount' => $item['amount'], // the original amount, that stored in order
                        'original_product_data' => array ( // the original cart ID and amount, that stored in order
                            'cart_id' => $_id,
                            'amount' => $item['amount'],
                        ),
                    ),
                );
                if (isset($item['extra'])) {
                    $_item[$item['product_id']]['extra'] = $item['extra'];
                }
                fn_add_product_to_cart($_item, $cart, $auth);
            }
        }
    }

    // Restore custom files
    $dir_path = 'order_data/' . $order_id;

    if (Storage::instance('custom_files')->isExist($dir_path)) {
        Storage::instance('custom_files')->copy($dir_path, 'sess_data');
    }

    $cart['payment_id'] = $order_info['payment_id'];
    $cart['stored_taxes'] = 'Y';
    $cart['stored_discount'] = 'Y';
    $cart['taxes'] = $order_info['taxes'];
    $cart['promotions'] = !empty($order_info['promotions']) ? $order_info['promotions'] : array();

    $cart['shipping'] = (!empty($order_info['shipping'])) ? $order_info['shipping'] : array();
    $cart['stored_shipping'] = array();
    foreach ($cart['shipping'] as $sh_id => $v) {
        if (!empty($v['rates'])) {
            $cart['stored_shipping'][$sh_id] = array_sum($v['rates']);
        }
    }

    if (!empty($order_info['product_groups'])) {
        $cart['product_groups'] = $order_info['product_groups'];
        foreach ($order_info['product_groups'] as $group) {
            if (!empty($group['chosen_shippings'])) {
                foreach ($group['chosen_shippings'] as $key => $chosen_shipping) {
                    foreach ($group['shippings'] as $shipping_id => $shipping) {
                        if ($shipping_id == $chosen_shipping['shipping_id']) {
                            $cart['chosen_shipping'][$chosen_shipping['group_key']] = $shipping_id;
                        }
                    }
                }
            }
        }
    } else {
        $cart['product_groups'] = array();
    }

    $cart['notes'] = $order_info['notes'];
    $cart['details'] = $order_info['details'];
    $cart['payment_info'] = @$order_info['payment_info'];
    $cart['profile_id'] = $order_info['profile_id'];

    // Add order discount
    if (floatval($order_info['subtotal_discount'])) {
        $cart['stored_subtotal_discount'] = 'Y';
        $cart['subtotal_discount'] = $cart['original_subtotal_discount'] = fn_format_price($order_info['subtotal_discount']);
    }

    // Fill the cart with the coupons
    if (!empty($order_info['coupons'])) {
        $cart['coupons'] = $order_info['coupons'];
    }

    // Set the customer if exists
    $_data = array();
    if (!empty($order_info['user_id'])) {
        $_data = db_get_row("SELECT user_id, user_login as login FROM ?:users WHERE user_id = ?i", $order_info['user_id']);
    }
    $auth = fn_fill_auth($_data, array(), false, 'C');
    $auth['tax_exempt'] = $order_info['tax_exempt'];

    // Fill customer info
    $cart['user_data'] = fn_check_table_fields($order_info, 'user_profiles');
    $cart['user_data'] = fn_array_merge(fn_check_table_fields($order_info, 'users'), $cart['user_data']);
    if (!empty($order_info['fields'])) {
        $cart['user_data']['fields'] = $order_info['fields'];
    }
    fn_add_user_data_descriptions($cart['user_data']);

    fn_set_hook('form_cart', $order_info, $cart);

    return true;
}