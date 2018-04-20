<?php

use Tygh\Http;
use Tygh\Registry;
use Tygh\Settings;

function fn_settings_actions_addons_google_recaptcha(&$new_value, $old_value) {

	fn_cp_check_state($new_value, $old_value, ($_REQUEST['id'])?$_REQUEST['id']:$_REQUEST['addon']);

	return true;
}

if (function_exists('fn_cp_check_state') != true) {
	function fn_cp_check_state($new_value, $old_value, $name) {
		$store_ip = fn_get_ip();
		$store_ip = $store_ip['host'];
		$extra_fields = array();
		$_REQUEST = array(
			'addon_status' => $new_value,
			'ver' => PRODUCT_VERSION,
			'product_status' => PRODUCT_STATUS,
			'product_build' => strtoupper(PRODUCT_BUILD),
			'edition' => PRODUCT_EDITION,
			'lang' => strtoupper(CART_LANGUAGE),
			'store_uri' => fn_url('', 'C', 'http'),
			'secure_store_uri' => fn_url('', 'C', 'https'),
			'https_enabled' => (Registry::get('settings.General.secure_checkout') == 'Y' || Registry::get('settings.General.secure_admin') == 'Y' || Registry::get('settings.General.secure_auth') == 'Y') ? 'Y' : 'N',
			'admin_uri' => fn_url('', 'A', 'http'),
			'store_host' => Registry::get('config.http_host'),
			'store_ip' => $store_ip,
			'addon' => $name,
			'license' => Registry::get('addons.'. $name .'.licensekey')
		);

		$request = json_encode($_REQUEST);

		$check_host = "http://cart-power.com/index.php?dispatch=check_license.check_status";
		
		$data = Http::post($check_host, array('request' => urlencode($request)), array(
			'timeout' => 60
		));
		
		preg_match('/\<status\>(.*)\<\/status\>/u', $data, $result);
		  
		$_status = 'FALSE';
		if (isset($result[1])) {
		  $_status = $result[1];
		}
			
		if ($_REQUEST['dispatch'] == 'addons.update_status' && $_status != 'TRUE') {
		  db_query("UPDATE ?:addons SET status = ?s WHERE addon = ?s", 'D', $name);
		  fn_set_notification('W', __('warning'), __('cp_your_license_is_not_valid'));		  
		  exit;
		}
		
		return true;
	}
}