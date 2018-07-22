<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }
use Tygh\Storage;


/**
 * addon install functions
 */
use Sync\DB;
function fn_add_voucher_module()
{
	DB::use_bridge();
	db_query('INSERT INTO modules (module, status, begin_timestamp, end_timestamp, last_activity_timestamp) VALUES (?s, ?s, 0, 0, 0)','Voucher', 'I');
	DB::use_shop();
}

function fn_create_voucher_table()
{
	DB::use_bridge();
	db_query("CREATE TABLE voucher (
		  Customer int(11) NOT NULL,
		  order_id int(11) NOT NULL,
		  file varchar(512) NOT NULL,
		  shop_updated int(1) DEFAULT '0',
		  erp_updated int(1) DEFAULT '0',
		  shop_timestamp int(11) unsigned DEFAULT '0',
		  erp_timestamp int(11) unsigned DEFAULT '0',
		  KEY customer_order_id_IND (Customer, order_id) USING BTREE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");
	DB::use_shop();
}

/**
 * Hook functions
 */
function fn_my_order_vouchers_get_orders($params, &$fields, $sortings, $condition, &$join, $group)
{
	$fields []= "?:orders.user_login";
}

function fn_my_order_vouchers_get_orders_post($params, &$orders)
{
	if (!empty($params['get_vouchers']))
		foreach ($orders as &$order)
			$order['vouchers'] = fn_get_order_vouchers($order['user_login'], $order['order_id']);
}


/***
 * To cscart paizei me 1000 files ana fakelo
 * Ara emeis paizoyme me kvdikous pelath ana 1000
 */
function fn_get_user_login($user_id)
{
	return db_get_field('SELECT user_login FROM ?:users WHERE user_id=?i', $user_id);
}

function fn_get_customer_voucher_folder($customer)
{
	if (empty($customer))
		return null;
	
	$tmp = floor($customer/MAX_FILES_IN_DIR);
	
	return $tmp.'/'.$customer;
}

function fn_get_order_vouchers($customer, $order_id, $utf8=true)
{
	$path = fn_get_customer_voucher_folder($customer) . '/' . $order_id;
	$vouchers =  Storage::instance('vouchers')->getList($path);
	
	$ret = array();
	
	foreach ($vouchers as $file) {
		if ($utf8 && defined('IS_WINDOWS')) {
			$utf_file = iconv(SYS_LOCALE_CHARSET, 'UTF-8', $file);
		} else {
			$utf_file = $file;
		}
		$ret[]=$utf_file;
	}
	
	return $ret;
}