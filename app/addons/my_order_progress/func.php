<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Sync\DB;
function fn_create_ordertracking_table()
{
	DB::use_bridge();
	db_query("CREATE TABLE `ordertracking` (
			  `AA` int(11) NOT NULL,
			  `tracking_code` varchar(512) NOT NULL,
			  `shop_updated` int(1) DEFAULT '0',
			  `erp_updated` int(1) DEFAULT '0',
			  `shop_timestamp` int(11) unsigned DEFAULT '0',
			  `erp_timestamp` int(11) unsigned DEFAULT '0',
			  PRIMARY KEY (`AA`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
	DB::use_shop();
}

function fn_add_ordertracking_module()
{
	DB::use_bridge();
	db_query('INSERT INTO modules (module, status, begin_timestamp, end_timestamp, last_activity_timestamp) VALUES (?s, ?s, 0, 0, 0)','OrderTracking', 'I');
	DB::use_shop();
}

function fn_my_order_progress_get_orders($params, &$fields, $sortings, $condition, &$join, $group)
{
	$join .= " LEFT JOIN ?:order_sync_queue osq ON ?:orders.order_id=osq.order_id";
	$fields []= "osq.order_id AS unsynced";
	if ('C' == AREA) {
		// den ua xrhsimopoihuei
	//	$join .= " LEFT JOIN ?:shippings sh ON ?:orders.shipping_ids=sh.shipping_id";
	//	$fields []= "sh.tracking_url";
		
		$fields []= "?:orders.tracking_code";
	}
}

function fn_my_order_progress_get_order_info(&$order, $additional_data)
{
	$order['unsynced'] = fn_my_order_progress_is_unsynced($order['order_id']);
}

function fn_my_order_progress_is_unsynced($order_id)
{
	$oid = db_get_field("SELECT order_id FROM ?:order_sync_queue WHERE order_id=?i", $order_id);
	
	return !empty($oid);
}

/**
 * PROGRESS STATUS
 * 1 = kataxvrhmenh, prin pesei sto erp kai gia 10 lepta (epitrepei diagrafh kai epejerghasia)
 * 2 = se epejergasia, afoy pesei kai mexri na labei tracking code
 * 3 = exei apostalei, otan labei tracking code
 */

function fn_get_order_progress_status($order)
{
	$status = 2;
	
	if (!empty($order['tracking_code']))
		$status = 3;
	else if (!empty($order['unsynced']) && (TIME - $order['timestamp']) < 600)
		$status = 1;
	
	return $status;
}

function fn_get_order_progress_status_desc($status)
{
	switch ($status) {
		case 1: return __('progress_status_registered');
		case 2: return __('progress_status_processed');
		case 3: return __('progress_status_sent');
		default: return '';
	}
}

function fn_get_order_tracking_link($order)
{
	if (empty($order['tracking_code']))
		return false;
	//if (empty($order['tracking_url']))
	//	return false;
	
	//return str_replace('[tracking_code]', $order['tracking_code'], $order['tracking_url']); 	
	
	return $order['tracking_code'];
}