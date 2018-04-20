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
use Sync\Master;

function fn_erp_sync_get_setting_field($field_name,$default_value='')
{
	$settings=Registry::get('addons.erp_sync');
	return (!empty($settings[$field_name]))?$settings[$field_name]:$default_value;
}

function full_sync()
{
	Master::full_sync();	
}

function fn_gr_letter_to_num($letter)
{
	$map =array(
		'0'=>-1,
		'Α'=>1,
		'Β'=>2,
		'Γ'=>3,
		'Δ'=>4,
		'Ε'=>5,
		'Ζ'=>6
	);	
	
	if (!empty($map[$letter]))	return $map[$letter];
	else throw new \Exception("Letter out of number dictionary ($letter)");
	
}

function fn_erp_sync_get_activity_status()
{
	return Master::get_activity_status();
}

function fn_erp_sync_reset_activity_status()
{
	return Master::reset_activity_status();
}

function fn_erp_sync_clear_modules($modules)
{
	return Master::clear_modules($modules);
}

function fn_erp_sync_get_reset_modules()
{
  return array (
    'customers'=>array('User','Profile','Phone'),
    'products'=>array('Category','Product','ProductPackage'),
    'others'=>array('State')
  );  
}

/*
* HOOKS
*/

function fn_erp_sync_delete_tax_pre($tax_id)
{
	if(!empty($tax_id)) {
		$bridge= new Sync\Tax();
		$bridge->clear_shop_id($tax_id); 
	}
}

function fn_erp_sync_delete_usergroups($usergroup_ids)
{
	if(!empty($usergroup_ids)) {
		$bridge= new Sync\Pricegroup();
		foreach ($usergroup_ids as $id)
			$bridge->clear_shop_id($id); 
	}
}

function fn_erp_sync_delete_category_post($category_id, $recurse, $category_ids)
{
	if (!empty($category_ids)) {
		$bridge= new Sync\Category();
		foreach($category_ids as $cid)
			$bridge->clear_shop_id($cid);
	}
}

function fn_erp_sync_post_delete_user($user_id, $user_data, $result)
{
	if (!empty($result)) {
		$user_bridge= new Sync\User();
		$profile_bridge= new Sync\Profile();
		
		$customer=$user_bridge->get_customer_by_shop_id($user_id);
		
		$user_bridge->clear_shop_id($user_id);
		$profile_bridge->clear_shop_id_for_Customer($customer);
	}
}

function fn_erp_sync_delete_product_post($product_id, $product_deleted)
{
	if (!empty($product_deleted)) {
		$bridge= new Sync\Product();
		
		$bridge->clear_shop_id($product_id);
	}
}

function fn_erp_sync_delete_order($order_id)
{
	$bridge_h= new Sync\Order();
	//$bridge_t= new Sync\OrderDetail(); exei cascade
	$bridge_h->clear_shop_id($order_id);
	//$bridge_t->clear_shop_id($order_id);
}