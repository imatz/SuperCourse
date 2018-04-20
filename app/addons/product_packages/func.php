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


if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;
use Tygh\Session;
use Tygh\Storage;
function fn_get_package_info($product_id) {
    $_package_info = db_get_array('SELECT * FROM ?:products_packages WHERE p_id = ?i ORDER BY position',  $product_id);
    foreach ($_package_info as $k => $v) {
	    $v['product_options'] = unserialize($v['options']);
	    unset($v['options']);
	    $package_info[$v['item_id']] = $v;
    }
    return $package_info;
}
function fn_get_full_package_info($product_id) {
    $_package_info = db_get_array('SELECT * FROM ?:products_packages WHERE p_id = ?i ORDER BY position',  $product_id);
    foreach ($_package_info as $k => $v) {
	 $v['product_options'] = unserialize($v['options']);
	 unset($v['options']);
	 $package_info[$v['item_id']] = $v;
	 $pr_data = fn_get_product_data($v['product_id'], $_SESSION['auth'], DESCR_SL, '?:product_descriptions.product', false, true, false, false, false, false, false);
	 if (!empty($v['product_options'])) {
	    $pr_data['selected_options'] = $v['product_options'];
	 }
	 fn_gather_additional_product_data($pr_data, false, false, true, false, false);
	 $package_info[$v['item_id']]['product_data'] = $pr_data;
	 
    }
    return $package_info;
}

function fn_product_packages_add_product_to_cart_check_price(&$data, &$price, &$allow_add) {
    if (isset($data['package_info']) && !empty($data['package_info'])) {
        if (db_get_field('SELECT price_rules_options FROM ?:products WHERE product_id = ?i', $data['product_id']) == 'N') {
            foreach ($data['package_info'] as $k => $package_items) {
                foreach ($package_items as $item) {
                    if (empty($item['product_options']) && !empty($item['product_data']['product_options'])) {
                        $price += $item['product_data']['modifiers_price'];
                    }
                }
            }
        }
    } elseif (isset($data['extra']['package_info']) && !empty($data['extra']['package_info'])) {
        $zero_p_action = db_get_field('SELECT zero_price_action FROM ?:products WHERE product_id=?i', $data['product_id']);
        if ($price <= 0 && $allow_add == false && $zero_p_action == 'R') {
            $allow_add = true;
        }
    }
}

function fn_product_packages_apply_options_rules_post(&$product) {
    if (fn_check_package($product['product_id']) == 'Y') {
	$product['has_options'] = true;
	
    }
}

function fn_product_packages_get_product_data_post(&$product_data, $auth, $preview, $lang_code) {
// fn_print_r($product_data);
    if (fn_check_package($product_data['product_id']) == 'Y' && (AREA == 'C' || defined('ORDER_MANAGEMENT'))) {
        $product_data['has_options'] = true;
        $product_data['package_info'] = fn_product_packages_get_package_info($product_data['product_id']);
        if ($product_data['price_rules_options'] == 'N') {
            foreach ($product_data['package_info'] as $k => &$package_items) {
                foreach ($package_items as $item) {
                    if (empty($item['product_options']) && !empty($item['product_data']['product_options'])) {
                        $product_data['price'] += $item['product_data']['modifiers_price']*$item['amount'];
                        $product_data['list_price'] += $item['product_data']['modifiers_price']*$item['amount'];
                        if (!isset($package_items[0]['modifiers_price']))  $package_items[0]['modifiers_price'] = 0;
                        $package_items[0]['modifiers_price'] += $item['product_data']['modifiers_price']*$item['amount'];
                    }
                }
            }
        }
        unset($package_items);
    }
  
}

function fn_product_packages_get_main_product_instock($product_id, $product_options, $cart_id = false, $cart = false) {
    $tracking = db_get_field('SELECT tracking FROM ?:products WHERE product_id = ?i', $product_id);
    $in_cart = 0;
    if (empty($cart)) {
        $cart_products = $_SESSION['cart']['products'];
    } else {
        $cart_products = $cart['products'];
    }
    foreach ($cart_products as $k => $v) {
        if ($k == $cart_id) continue;
        if ($v['product_id'] == $product_id) {
            if ($tracking == 'O') {
                $op_dif = @array_diff_assoc($v['product_options'], $product_options);
                if (empty($op_dif)) {
                    $in_cart += $v['amount'];
                }
            } elseif ($tracking == 'B') {
                $in_cart += $v['amount'];
            }
        }
    }
    
    if ($tracking == 'O') {
        $key = fn_generate_cart_id($product_id, array('product_options' => $product_options));
        $amount = db_get_field('SELECT amount FROM ?:product_options_inventory WHERE product_id = ?i AND combination_hash = ?s', $product_id, $key) - $in_cart;
    } elseif ($tracking == 'B') {
        $amount = db_get_field('SELECT amount FROM ?:products WHERE product_id = ?i', $product_id) - $in_cart;
    } else {
        $amount = 99999999;
    }
    
    return $amount;
}

function fn_product_packages_get_package_instock($package, $use_cart = false, $cart_id = null, $cart = false) {
    $min = null;
    $possible_count = $needed_count = $amounts_in_cart = array();
    $needed_count = array();
    
    if (!empty($package)) {
        foreach ($package as $k => $package_items) {
            foreach ($package_items as $k1=>$item) {
                $key = fn_generate_cart_id($item['product_id'], array('product_options' => $item['product_data']['selected_options']));
                $amounts_in_cart[$key] = 0;
                if ($item['product_data']['tracking'] == 'O' && isset($item['product_data']['inventory_amount'])) {
                    if (!isset($needed_count[$key])) $needed_count[$key] = 0;
                    $needed_count[$key] += $item['amount'];
                    $possible_count[$key] = $item['product_data']['inventory_amount'];
                } elseif ($item['product_data']['tracking'] == 'B') {
                    if (!isset($needed_count[$key])) $needed_count[$key] = 0;
                    $needed_count[$key] += $item['amount'];
                    $possible_count[$key] = $item['product_data']['amount'];
                }
                if ($use_cart) {
                    $amounts_in_cart[$key] = fn_product_packages_get_amount_in_cart($item['product_data']['product_id'], $item['product_data']['tracking'], $item['product_data']['selected_options'], $cart_id, $cart);
                }
            }
        }
    }
    foreach ($needed_count as $k => $v) {
        $val = floor(($possible_count[$k]-$amounts_in_cart[$k])/$v);
        if (is_null($min) || $min > $val) {
            $min = $val;
        }
    }
    return $min;
}
function fn_product_packages_get_amount_in_cart($product_id, $tracking, $product_options, $cart_id = null, $cart = false) {
    if (empty($cart)) {
        $cart = $_SESSION['cart'];
    }
    $amount = 0;
    foreach ($cart['products'] as $k => $v) {
        if (isset($v['extra']['package_info']['p_id']) && $v['extra']['package_info']['p_id'] == $cart_id) continue;
        if ($tracking == 'B') {
            if ($v['product_id'] == $product_id) {
                $amount += $v['amount'];
            }
        } elseif ($tracking == 'O') {
            if ($v['product_id'] == $product_id) {
                $op_dif = @array_diff_assoc($v['product_options'], $product_options);
                if (empty($op_dif)) {
                    $amount += $v['amount'];
                }
            }
        }
    }
    return $amount;
}
function fn_product_packages_get_package_info_in_cart($product_id, $cart_id) {

    $_package_info = db_get_array('SELECT * FROM ?:products_packages WHERE p_id = ?i ORDER BY position',  $product_id);
    foreach ($_package_info as $k => $v) {
        $count = 0;
        if ($v['amount'] > 1 && $v['multiple']=="Y") {
            $count = $v['amount']-1;
            $amount = 1;
            $v['amount'] = 1;
        }
        
        $v['product_options'] = unserialize($v['options']);
        unset($v['options']);
        
        for($i=0;$i<=$count;$i++){
            $v['product_data'] = fn_get_product_data($v['product_id'], $_SESSION['auth'], DESCR_SL, '', false, true, false, false, false, false, false);
            if (empty($v['product_options'])) {
                $cart_products = $_SESSION['cart']['products'];
                foreach ($cart_products as $k2 => $v2) {
                    if (isset($v2['extra']['package_info']) && $cart_id == $v2['extra']['package_info']['p_id'] && $v2['extra']['package_info']['inc'] == $i && $v2['extra']['package_info']['table_key'] == $v['item_id']) {
                        $v['product_data']['selected_options'] = $v2['product_options'];
                    }
                }
            } else {
                    $v['product_data']['selected_options'] = $v['product_options'];
            }
            fn_gather_additional_product_data($v['product_data'], true, true, true, false, false);
            $package_info[$v['item_id']][$i] = $v;
        }
    }
    return $package_info;
}
function fn_product_packages_get_package_info($product_id, $cart_id = false) {

    $_package_info = db_get_array('SELECT * FROM ?:products_packages WHERE p_id = ?i ORDER BY position',  $product_id);
    $package_info = array();
    foreach ($_package_info as $k => $v) {
        
        $v['product_options'] = unserialize($v['options']);
        unset($v['options']);
        
        $count = 0;
        if ($v['amount'] > 1 && $v['multiple']=="Y" && empty($v['product_options'])) {
            $count = $v['amount']-1;
            $amount = 1;
            $v['amount'] = 1;
        }
        
        for($i=0;$i<=$count;$i++){
            $v['product_data'] = fn_get_product_data($v['product_id'], $_SESSION['auth'], DESCR_SL, '', false, true, false, false, false, false, false);
            if (empty($v['product_options'])) {
                if ((Registry::get('runtime.controller') == 'products' && Registry::get('runtime.mode') == 'options' && isset($_REQUEST['product_data'][$product_id]['package'])) || fn_check_location_product_data()) {
                    if (isset($_REQUEST['product_data'][$product_id]['package'][$v['item_id']][$i]['product_options']))
                        $v['product_data']['selected_options'] = $_REQUEST['product_data'][$product_id]['package'][$v['item_id']][$i]['product_options'];
                } elseif ((Registry::get('runtime.controller') == 'products' && Registry::get('runtime.mode') == 'options' && isset($_REQUEST['cart_products'])) || (Registry::get('runtime.controller') == 'checkout' && Registry::get('runtime.mode') == 'update')) {
                    $cart_products = $_SESSION['cart']['products'];
                    foreach ($_REQUEST['cart_products'] as $k1=>$v1) {
                        foreach ($cart_products as $k2 => $v2) {
                            if (isset($v2['extra']['package_info']) && $k1 == $cart_id && $v2['extra']['package_info']['p_id'] == $k1 && $v2['extra']['package_info']['inc'] == $i && $v2['extra']['package_info']['table_key'] == $v['item_id']) {
                                $v['product_data']['selected_options'] = $v2['product_options'];
                            }
                        }
                    }
                }
            } else {
                    $v['product_data']['selected_options'] = $v['product_options'];
            }
            fn_gather_additional_product_data($v['product_data'], true, true, true, false, false);
            $package_info[$v['item_id']][$i] = $v;
        }
    }
    
    return $package_info;
}

function fn_check_location_product_data() {
    if ((Registry::get('runtime.controller') == 'checkout' && Registry::get('runtime.mode') == 'add') || 
        (Registry::get('runtime.controller') == 'orders' && Registry::get('runtime.mode') == 'reorder') || 
        (Registry::get('runtime.controller') == 'order_management')
    ) {
        return true;
    }
}

function fn_product_packages_gather_additional_product_data_post(&$product, $auth, $params) {
    if (isset($product['package_info']) && fn_check_package($product['product_id']) == 'Y') {
        $product['has_options'] = true;
        if (!empty($product[$product['package_info']])) {
            $package_amount = fn_product_packages_get_package_instock($product['package_info']);
            if (isset($product['inventory_amount']) && $product['inventory_amount'] > $package_amount) {
                $product['inventory_amount'] = $package_amount;
            } 
            if ($product['amount'] > $package_amount) {
                $product['amount'] = $package_amount;
            }
        }
    }
}

function fn_product_packages_delete_product_post($product_id, $product_deleted) {
    db_query('DELETE FROM ?:products_packages WHERE p_id = ?i', $product_id);
}

function fn_product_packages_clone_product_post($product_id, $pid, $orig_name, $new_name) {
    $package = db_get_array('SELECT * FROM ?:products_packages WHERE p_id = ?i', $product_id);
    if (!empty($package)) {
	foreach ($package as $v) {
	    $v['p_id'] = $pid;
	    db_query('INSERT INTO ?:products_packages ?e', $v);
	}
    }
}
function fn_product_packages_pre_place_order(&$cart, $allow, $packages) {
    foreach ($cart['products'] as $k => $v) {
        if (fn_check_package($v['product_id']) == 'Y') {
            $cart['products'][$k]['extra']['no_items_products'] = fn_get_no_products($v['product_id']);
        }
    }
}
function fn_product_packages_reorder(&$order_info, &$cart, $auth) {
    if (!empty($order_info['products'])) {
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
                fn_save_cart_content($cart, $auth['user_id']);
            }
        }
    }
}
function fn_product_packages_update_cart_products_pre(&$cart, &$product_data, $auth) {
     if (is_array($cart['products']) && !empty($product_data)) {
        $packages = array();
        foreach ($product_data as $k => $v) {
            if (isset($v['package'])) {
                fn_delete_cart_product($cart, $k);
                unset($product_data[$k]);
                $packages[$k][$v['product_id']] = $v;
            }
        }
        if (!empty($packages)) {
            foreach ($packages as $v) {
                $_REQUEST['product_data'] = $v;
                fn_add_product_to_cart($v, $cart, $auth);
            }
        }
    }
}
function fn_product_packages_calculate_cart_items(&$cart, &$cart_products, $auth) {
    foreach ($cart['products'] as $k => &$v) {
        if (fn_check_package($v['product_id']) == 'Y') {
            $package_info = fn_product_packages_get_package_info_in_cart($v['product_id'], $k);
            
            $package_amount = fn_product_packages_get_package_instock($package_info, true, $k, $cart);
            $main_product_instock = fn_product_packages_get_main_product_instock($v['product_id'], $v['product_options'], $k, $cart);
            $package_amount = min($package_amount, $main_product_instock);
            $packages_in_cart = fn_get_package_in_cart($package_info, $k, $cart);
            $inventory_tracking = Registry::get('settings.General.inventory_tracking');
            $allow_negative = Registry::get('settings.General.allow_negative_amount');
            if ($inventory_tracking == "N" || $allow_negative == "Y") {
                $package_amount = $v['amount'];
                $packages_in_cart = $v['amount'];
            }
            if ($package_amount < $v['amount']) {
                $v['amount'] = $package_amount;
                fn_update_product_package_in_cart($cart, $cart_products, $k, $package_info, $package_amount);
            }
            if ($packages_in_cart > $v['amount']) {
                fn_update_product_package_in_cart($cart, $cart_products, $k, $package_info, $v['amount']);
            }
            if ($package_amount) {
                if (db_get_field('SELECT price_rules_options FROM ?:products WHERE product_id = ?i', $v['product_id']) == 'N') {
                    foreach ($cart['products'] as $k1 => $v1) {
                        if (isset($v1['extra']['package_info']) && $v1['extra']['package_info']['p_id'] == $k && isset($cart_products[$k1])) {
                            if (!isset($cart['products'][$k]['stored_price']) || $cart['products'][$k]['stored_price'] == "N") {
                                $cart['products'][$k]['price'] += $v1['extra']['package_info']['modifiers_price'];
                                
                                if (isset($cart['products'][$k]['subtotal'])) $cart['products'][$k]['subtotal'] += $v1['extra']['package_info']['modifiers_price'];
                                $cart_products[$k]['price'] += $v1['extra']['package_info']['modifiers_price'];
                                $cart_products[$k]['subtotal'] += $v1['extra']['package_info']['modifiers_price'];
                                $cart_products[$k]['original_price'] += $v1['extra']['package_info']['modifiers_price'];
                            }
                            
                            if (!isset($cart_products[$k]['package_modifier'])) $cart_products[$k]['package_modifier'] = 0;
                            if (!isset($cart['products'][$k]['package_modifier'])) $cart['products'][$k]['package_modifier'] = 0;
                            $cart['products'][$k]['package_modifier'] += $v1['extra']['package_info']['modifiers_price'];
                            $cart_products[$k]['package_modifier'] += $v1['extra']['package_info']['modifiers_price'];
                        }
                    }
                }
            }
        } 
    }
    
    unset($v);
    $amount = 0;
    foreach ($cart['products'] as $k => $v) {
        if (isset($v['extra']['package_info']) && !empty($v['extra']['package_info']) && isset($cart_products[$k])) {
            $cart['products'][$k]['price'] = 0;
            $cart['products'][$k]['subtotal'] = 0;
            $cart_products[$k]['price'] = 0;
            $cart_products[$k]['subtotal'] = 0;
            $cart['products'][$k]['extra']['exlude_from_calculate'] = true;
        } else {
            $amount += $v['amount'];
        }
    }
    $cart['amount'] = $amount;
    foreach ($cart_products as $k => $v) {
        if (!isset($cart['products'][$k])) {
            unset($cart_products[$k]);
        }
    }
}
function fn_product_packages_get_cart_product_data($product_id, &$_pdata, &$product, $auth, $cart) {
    if (isset($product['extra']['package_info']) && !empty($product['extra']['package_info'])) {
        $_pdata['price'] = 0;
        $_pdata['base_price'] = 0;
        $product['price'] = 0;
        $product['base_price'] = 0;
    }
}
function fn_get_package_in_cart($package_info, $k, $cart) {
    foreach ($package_info as $v) {
        $key = $v[0]['item_id'];
        $amount = $v[0]['amount'];
        break;
    }
    foreach ($cart['products'] as $k1 => $v1) {
        if (isset($v1['extra']['package_info']) && $v1['extra']['package_info']['p_id'] == $k && $v1['extra']['package_info']['inc'] == 0 && $v1['extra']['package_info']['table_key'] == $key) {
            $amount_in_cart = $v1['amount']/$amount;
        }
    }
    return $amount_in_cart;

}

function fn_update_product_package_in_cart(&$cart, &$cart_products, $cart_id, $package_info, $package_amount) {
    if (empty($package_amount)) {
        foreach ($cart['products'] as $k=>$product) {
            if (isset($product['extra']['package_info']) && $product['extra']['package_info']['p_id'] == $cart_id) {
              unset($cart_products[$k]);
            }
        }
        fn_delete_cart_product($cart, $cart_id);
        unset($cart_products[$cart_id]);
    } else {
        $before_amount = $cart['products'][$cart_id]['amount'];
        $cart['products'][$cart_id]['amount'] = $package_amount;
        $cart_products[$cart_id]['amount'] = $package_amount;
        foreach ($cart['products'] as $k=>&$product) {
            if (isset($product['extra']['package_info']) && $product['extra']['package_info']['p_id'] == $cart_id) {
                $product['amount'] = $package_info[$product['extra']['package_info']['table_key']][$product['extra']['package_info']['inc']]['amount'] * $package_amount;
            }
        }
    }
}

function fn_check_package($p_id) {
    $package = db_get_field('SELECT package FROM ?:products WHERE product_id = ?i', $p_id);
    $package_count = db_get_field('SELECT COUNT(*) FROM ?:products_packages WHERE p_id = ?i', $p_id);

    if ($package == 'Y' && $package_count > 0) {
        return $package;
    }
    
    return 'N';
}
function fn_product_packages_pre_add_to_cart(&$product_data, &$cart, $auth, $update) {
//     fn_print_die($product_data);
    foreach ($product_data as $p_id=>$data) {
        if (!isset($data['product_id'])) {
            $data['product_id'] = $p_id;
            $product_data[$p_id]['product_id'] = $p_id;
        }
        if (fn_check_package($data['product_id']) == 'Y') {
            if ($update) {
                $product_data[$p_id]['package_info'] = fn_product_packages_get_package_info($data['product_id'], $p_id);
            } else {
                $product_data[$p_id]['package_info'] = fn_product_packages_get_package_info($data['product_id']);
            }
        }
    }

    if ($update) {
        foreach ($cart['products'] as $k=>$v) {
            if (isset($v['extra']['package_info']['p_id']) && !empty($v['extra']['package_info']['p_id'])) {
                unset($cart['products'][$k]);
            }
        }
    }
    unset($data);
    $package_products = array();
    foreach ($product_data as $p_id=>&$data) {
        if (!empty($data['package_info'])) {
            if (!isset($data['product_options'])) $data['product_options'] = array();
            $data['extra']['product_options'] = $data['product_options'];
            $package_instock = fn_product_packages_get_package_instock($data['package_info'], true);
            if ($update) {
                $main_product_instock = fn_product_packages_get_main_product_instock($data['product_id'], $data['product_options'], $p_id);
            } else {
                $main_product_instock = fn_product_packages_get_main_product_instock($data['product_id'], $data['product_options']);
            }
            $inventory_tracking = Registry::get('settings.General.inventory_tracking');
            $allow_negative = Registry::get('settings.General.allow_negative_amount');
            if ($inventory_tracking == 'N' || $allow_negative == 'Y') {
                $package_instock = $data['amount'];
            } else {
                $package_instock = min($package_instock, $main_product_instock);
            }
            if ($data['amount'] > $package_instock) {
                $data['amount'] = $package_instock;
                if ($package_instock) {
                    fn_set_notification('W', __('important'), __('text_cart_amount_corrected', array(
                        '[product]' => fn_get_product_name($data['product_id'])
                    )));
                } else {
                    fn_set_notification('W', __('warning'), __('text_cart_not_enough_inventory'));
                }
            }
            $data['added_amount'] = $data['amount'];
            $package_hash = array();
            foreach ($data['package_info'] as $k => $package_items) {
                foreach ($package_items as $k1=>$item) {
                    $extra = array(
                        'product_options' => $item['product_data']['selected_options'],
                        'table_key' => $k,
                        'inc' => $k1
                    );
                    $package_hash[] = fn_generate_cart_id($item['product_data']['product_id'], $extra);
                }
            }
            $data['extra']['package_hash'] = $package_hash;
            $data['extra']['feature_hash'] = fn_generate_cart_id($data['product_id'], $product_data[$p_id]['extra'], false);
        }
    }
}
function fn_product_packages_generate_cart_id(&$_cid, $extra, $only_selectable = false)
{
        // Buy together product
        if (isset($extra['package_info']) && !empty($extra['package_info'])) {
                $_cid[] = 'package_' . $extra['package_info']['p_id'];
                $_cid[] = 'package_' . $extra['package_info']['table_key'];
                $_cid[] = 'package_' . $extra['package_info']['inc'];
        } 
        if (isset($extra['package_hash']) && !empty($extra['package_hash'])) {
                $_cid[] = base64_encode(serialize($extra['package_hash']));
        } 
}
function fn_product_packages_post_add_to_cart(&$product_data, &$cart, $auth, $update) {
    foreach ($product_data as $p_id => &$data) {
        if (isset($data['package_info']) && !empty($data['package_info']) && $data['amount'] > 0) {
            foreach ($data['package_info'] as $k => $package_items) {
                foreach ($package_items as $k1=>$item) {
                    $p_data = array();
                    $p_data[$item['product_data']['product_id']] = array(
                        'product_id' => $item['product_data']['product_id'],
                        'product_options' => $item['product_data']['selected_options'],
                        'amount' => $item['amount']*$data['amount'],
                        'extra' => array(
                            'product_options' => $item['product_data']['selected_options'],
                            'package_info' => array(
                                'table_key' => $k,
                                'inc' => $k1,
                                'modifiers_price' => (empty($item['product_options']) && !empty($item['product_data']['product_options'])) ? $item['product_data']['modifiers_price']*$item['amount'] : 0,
                                'p_id' => $data['extra']['feature_hash']
                            ),
                        )
                    );
                    fn_add_product_to_cart($p_data, $cart, $auth);
                    fn_save_cart_content($cart, $auth['user_id']);
                }
            }
        }
    }
}

function fn_product_packages_delete_cart_product(&$cart, $cart_id, $full_erase) {
    if (!empty($cart['products'][$cart_id]['extra']['package_info']['p_id'])) {
        fn_delete_cart_product($cart, $cart['products'][$cart_id]['extra']['package_info']['p_id'], $full_erase);
    } else {
        foreach ($cart['products'] as $k => $v) {
            if (isset($v['extra']['package_info']) && $v['extra']['package_info']['p_id'] == $cart_id) {
                fn_delete_cart_product_product_packages($cart, $k, $full_erase);
            }
        }
    }
}

function fn_delete_cart_product_product_packages(&$cart, $cart_id, $full_erase = true)
{

    if (!empty($cart_id) && !empty($cart['products'][$cart_id])) {
        // Decrease product popularity
        if (!empty($cart['products'][$cart_id]['product_id'])) {
            $product_id = $cart['products'][$cart_id]['product_id'];

            $_data = array (
                'product_id' => $product_id,
                'deleted' => 1,
                'total' => 0
            );

            db_query("INSERT INTO ?:product_popularity ?e ON DUPLICATE KEY UPDATE deleted = deleted + 1, total = total - ?i", $_data, POPULARITY_DELETE_FROM_CART);

            unset($_SESSION['products_popularity']['added'][$product_id]);
        }

        // Delete saved product files
        if (isset($cart['products'][$cart_id]['extra']['custom_files']) && $full_erase) {
            foreach ($cart['products'][$cart_id]['extra']['custom_files'] as $option_id => $images) {
                if (!empty($images)) {
                    foreach ($images as $image) {
                        Storage::instance('custom_files')->delete($image['path']);
                        Storage::instance('custom_files')->delete($image['path'] . '_thumb');
                    }
                }
            }
        }

        unset($cart['products'][$cart_id]);

        if (!empty($cart['product_groups'])) {
            foreach ($cart['product_groups'] as $group_key => $group) {
                if (isset($group['products'][$cart_id])) {
                    unset($cart['product_groups'][$group_key]['products'][$cart_id]);
                }
            }
        }

        if (!empty($cart['chosen_shipping'])) {
            $cart['calculate_shipping'] = true;
            unset($cart['product_groups']);
        }

        $cart['recalculate'] = true;
        $cart['change_cart_products'] = true;
    }

    return true;
}
function fn_product_packages_get_products_before_select($params, $join, &$condition, $u_condition, $inventory_condition, $sortings, $total, $items_per_page, $lang_code, $having){
    if(AREA =="A" && !empty($_REQUEST['display'])){//if picker
//         $condition .= db_quote(' AND products.package = ?s', 'N');
    }
}
function fn_get_no_products_price($product_id) {
    $price = db_get_field('SELECT SUM(price*amount) FROM ?:no_product_items WHERE product_id = ?i', $product_id);
    return $price;
}
function fn_get_no_products($product_id) {
    return db_get_array('SELECT * FROM ?:no_product_items WHERE product_id = ?i ORDER BY position ', $product_id);
}
function fn_show_package_product_options($product_id, $item_id) {
    $options = db_get_field('SELECT options FROM ?:products_packages WHERE p_id = ?i AND item_id = ?i', $product_id, $item_id);
    $result = unserialize($options);
    return $result;
}
function fn_prepare_package_product_options($options, $selected_options) {
    foreach ($options as $k=>$v) {
        $options[$k] = array_merge($v, $v['variants'][$selected_options[$v['option_id']]]);
    }
    return $options;
}
?>