<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;
use Tygh\Session;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update') {
        if (!empty($_REQUEST['package_info'])) {
            $package_info = $_REQUEST['package_info']['product_ids'];
            unset($package_info['{product_id}']);
            $price = $list_price = 0;
            db_query('DELETE FROM ?:products_packages WHERE p_id = ?i', $_REQUEST['product_id']);
            foreach ($package_info as $k => $v) {
                $data = array();
                if (empty($v['amount'])) {
                    $v['amount'] = 1;
                }
                fn_calcalate_package_item_price($v);
                if (!empty($v['product_id'])) {
                    $price += $v['f_price']*$v['amount'];
                    $list_price += $v['price']*$v['amount'];
                    $data = array (
                        'p_id' => $_REQUEST['product_id'],
                        'product_id' => $v['product_id'],
                        'position' => $v['position'],
                        'item_id' => $k,
                        'price' => $v['price'],
                        'amount' => $v['amount'],
                        'f_price' => $v['f_price'],
                        'p_modifier_type' => $v['p_modifier_type'],
                        'p_modifier' => $v['p_modifier'],
                        'options' => ((isset($v['product_options']) && !empty($v['product_options'])) ? serialize($v['product_options']) : ''),
                        'multiple' => $v['multiple']
                    );
                    db_query('INSERT INTO ?:products_packages ?e', $data);
                }
            }
        }
        if (isset($_REQUEST['no_product_items']) && !empty($_REQUEST['no_product_items'])) {
            db_query('DELETE FROM ?:no_product_items WHERE product_id = ?i', $_REQUEST['product_id']);
            $items = $_REQUEST['no_product_items'];
            foreach ($items as $v) {
                if (!empty($v['name']) && !empty($v['amount'])) {
                    $v['product_id'] = $_REQUEST['product_id'];
                    db_query('INSERT INTO ?:no_product_items ?e', $v);
                }
            }
        }
        if ($_REQUEST['product_data']['package'] == 'Y' && $_REQUEST['product_data']['price_rule'] == 'S' && !empty($_REQUEST['product_id'])) {
            $no_product_price = fn_get_no_products_price($_REQUEST['product_id']);
            $pr_data['price'] = $price + $no_product_price;
            $pr_data['list_price'] = $list_price + $no_product_price;
            $product_id = fn_update_product($pr_data, $_REQUEST['product_id'], DESCR_SL);
        }
        if ($_REQUEST['product_data']['package'] == 'Y' && !empty($_REQUEST['product_id'])) {
            $check = db_get_field('SELECT p_id FROM ?:products_packages WHERE product_id = ?i', $_REQUEST['product_id']);
            if (!empty($check)) {
                db_query('UPDATE ?:products SET package = ?s WHERE product_id = ?i', 'N', $_REQUEST['product_id']);
                fn_set_notification('W', __('warning'), __('product_is_part_of_package'));
            }
        }
      }

} elseif ($mode == 'update') {
    if (db_get_field('SELECT package FROM ?:products WHERE product_id = ?i', $_REQUEST['product_id']) == 'Y') {
        Registry::set('navigation.tabs.package_info', array (
            'title' => __('package_info'),
            'js' => true
        ));
        $package_info = array();
        $_package_info = db_get_array('SELECT * FROM ?:products_packages WHERE p_id = ?i ORDER BY position',  $_REQUEST['product_id']);
        foreach ($_package_info as $k => $v) {
            $v['product_options'] = unserialize($v['options']);
            unset($v['options']);
            $package_info[$v['item_id']] = $v;
        }
        Registry::get('view')->assign('package_info', $package_info);
        Registry::get('view')->assign('no_product_items', fn_get_no_products($_REQUEST['product_id']));
    }
}
function fn_calcalate_package_item_price(&$data) {

    $data['price'] = fn_get_product_price($data['product_id'], $data['amount'], $_SESSION['auth']);
    if (isset($data['product_options']) && !empty($data['product_options'])) {
        $modifiers_price = 0;
        $option_modifiers = db_get_array('SELECT option_id, variant_id,modifier, modifier_type FROM ?:product_option_variants WHERE variant_id IN (?a) AND option_id in (?a)', $data['product_options'], array_keys($data['product_options']));
        if (!empty($option_modifiers)) {
            foreach ($option_modifiers as $v) {
                if ($v['modifier_type'] == 'A') {
                    $modifiers_price += $v['modifier'];
                } else {
                    $modifiers_price += $v['modifier']*$data['price']/100;
                }
            }
        }
        $data['price'] += $modifiers_price;
    }

    $data['f_price'] = $data['price'];
    if ($data['p_modifier'] > 0) {
        switch ($data['p_modifier_type']) {
            case 'by_fixed': 
                $data['f_price'] = $data['price'] - $data['p_modifier'];
                break;
            case 'to_fixed': 
                $data['f_price'] = $data['p_modifier'];
                break;
            case 'by_percentage': 
                $data['f_price'] = $data['price'] * (1 - $data['p_modifier']/100);
                break;
            case 'to_percentage': 
                $data['f_price'] = $data['price'] * $data['p_modifier']/100;
                break;                
        }
    }

}

?>
