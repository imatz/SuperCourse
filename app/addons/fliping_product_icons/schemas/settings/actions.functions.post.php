<?php

use Tygh\Http;
use Tygh\Registry;
use Tygh\Settings;

function fn_settings_actions_addons_fliping_product_icons(&$new_value, $old_value) {
	fn_confirm_check($new_value, $old_value, !empty($_REQUEST['id']) ? $_REQUEST['id'] : $_REQUEST['addon']);

	return true;
}

if (function_exists('fn_confirm_check') != true) {
  function fn_confirm_check($new_value, $old_value, $name)
  {
	  $store_ip = fn_get_ip();
	  
	  $store_ip = $store_ip['host'];
	  $extra_fields = array();
	  $_request = array(
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
		  'addon' => $name
	  );

	  $request = json_encode($_request);

	  $check_host = "http://cart-power.com/index.php?dispatch=check_license";
	  
	  $data = Http::get($check_host, array('request' => urlencode($request)), array(
		  'timeout' => 10
	  ));

	  return true;
  }
}