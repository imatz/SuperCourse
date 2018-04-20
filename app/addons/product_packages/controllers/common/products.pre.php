<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'options') {

    if (isset($_REQUEST['cart_products'])) {
        foreach ($_REQUEST['cart_products'] as $p_id => $data) {
            if (fn_check_package($data['product_id']) == 'Y') {
                $package_info = fn_product_packages_get_package_info($data['product_id'], $p_id);
                $package_amount = fn_product_packages_get_package_instock($package_info, true, $p_id);
                if ($data['amount'] > $package_amount) {
                    $_REQUEST['cart_products'][$p_id]['amount'] = $package_amount;
                    if ($package_amount) {
                        fn_set_notification('W', __('important'), __('text_cart_amount_corrected', array(
                            '[product]' => fn_get_product_name($data['product_id'])
                        )));
                    } else {
                        fn_set_notification('W', __('warning'), __('text_cart_not_enough_inventory'));
                    }
                }
            }
        }
    }
}
