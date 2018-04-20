<?php
/*
© 2014 HungryWeb.net | Support at yum@hungryweb.net

This source file is subject to the EULA that is bundled with this package in the file HW-LICENSE.txt.
It is also available through the world-wide-web at this URL: http://www.hungryweb.net/HW-LICENSE.txt

@copyright  	Copyright (c) 2014 hungryweb.net
@license    	http://www.hungryweb.net/HW-LICENSE.txt
 */

if ( !defined('BOOTSTRAP') ) { die('Access denied'); }

use Tygh\Registry;
use Tygh\Http;

//hw action
function fn_hw_top_install(){ fn_hw_action('top','install'); }
function fn_hw_top_uninstall(){ fn_hw_action('top','uninstall'); }
if (!function_exists('fn_hw_action')){ 	
  function fn_hw_action($addon,$a){
		  $request = array(	
			  'addon' => $addon,
			  'host' => Registry::get('config.http_host'),
			  'path' => Registry::get('config.http_path'),
			  'version' => PRODUCT_VERSION,
			  'edition' => PRODUCT_EDITION,
			  'lang' => strtoupper(CART_LANGUAGE),
			  'a' => $a
		  );
		  Http::post('http://api.hungryweb.net/', $request);
  }
}