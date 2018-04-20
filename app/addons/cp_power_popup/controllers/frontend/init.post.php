<?php
use Tygh\Registry;
if (!defined('BOOTSTRAP')) { die('Access denied'); }

$popup = fn_cp_get_available_popup($controller, $mode, $_REQUEST); 

if (!empty($popup)) {

	if ($popup['content_type'] == 'P' &&  !empty($popup['page_id'])) {

		$page_is_https = db_get_field("SELECT value FROM ?:form_options WHERE element_type = ?s AND page_id = ?i", FORM_IS_SECURE, $popup['page_id']);
		// if form is secure, redirect to https connection
		if (!defined('HTTPS') && $page_is_https == 'Y') {
			return array(CONTROLLER_STATUS_REDIRECT, Registry::get('config.https_location') . '/' . Registry::get('config.current_url'));

		} elseif (defined('HTTPS') && Registry::get('settings.Security.keep_https') != 'Y' && $page_is_https != 'Y') {
			return array(CONTROLLER_STATUS_REDIRECT, Registry::get('config.http_location') . '/' . Registry::get('config.current_url'));
		}

		$restored_form_values = fn_restore_post_data('form_values');
		if (!empty($restored_form_values)) {
			Registry::get('view')->assign('form_values', $restored_form_values);
		}
	} elseif ($popup['content_type'] == 'R' && !empty($popup['product_id'])) {
	
        $product = fn_get_product_data($popup['product_id'], $_SESSION['auth'], CART_LANGUAGE, '', true, true, true, true, fn_is_preview_action($_SESSION['auth'], $_REQUEST));
        if (!empty($product)) {
            fn_gather_additional_product_data($product, true, true);
        }
        $popup['product'] = $product;       
    }
	
	if (!isset($_COOKIE["power_popup_" . $popup['popup_id']])) {
		Registry::get('view')->assign('popup', $popup);
	}
	if ($popup['ttl'] > 0 && $popup['type'] == 'R') {
		setcookie("power_popup_" . $popup['popup_id'], "power_popup_" . $popup['popup_id'], time() + (60 * 60 * $popup['ttl']), '/');
	}
}