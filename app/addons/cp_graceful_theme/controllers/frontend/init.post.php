<?php
use Tygh\Registry;

	$settings = Registry::get('addons.cp_graceful_theme.rtl_settings');

	$r = (!empty($settings[CART_LANGUAGE]) && $settings[CART_LANGUAGE] == 'Y') ? true : false;
	Registry::get('view')->assign('rtl', $r);

?>