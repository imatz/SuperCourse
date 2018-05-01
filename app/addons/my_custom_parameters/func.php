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

use Tygh\Registry;
use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_my_custom_parameters_update_parameter($param,$value)
{
	if (!$section = Settings::instance()->getSectionByName('my_custom_parameters', Settings::ADDON_SECTION)) {
		$section = Settings::instance()->updateSection(array(
			'parent_id' =>      0,
			'edition_type' =>   'ROOT,ULT:VENDOR',
			'name' =>           'my_custom_parameters',
			'type' =>           'ADDON'
		));
	}
	
	
	if (!$setting_id = Settings::instance()->getId($param, 'my_custom_parameters')) {
		$setting_id = Settings::instance()->update(array(
			'name' =>           $param,
			'section_id' =>     $section['section_id'],
			'edition_type' =>	'ROOT,ULT:VENDOR',
			'section_tab_id' => 0,
			'type' =>           'A',
			'position' =>       0,
			'is_global' =>      'N',
			'handler' =>        ''
		));
	}

	Settings::instance()->updateValueById($setting_id, $value, Registry::get('runtime.company_id'));
	
	$description_data =array(
		'object_id'=>$setting_id,
		'lang_code'=>'en',
		'object_type'=>'O',
		'value'=>$param
	);
	
	Settings::instance()->updateDescription($description_data);
	
	$description_data ['lang_code']='el';
	
	Settings::instance()->updateDescription($description_data);	
	
	return true;
}

function fn_my_custom_parameters_get_setting_field($field_name,$default_value='')
{
	$settings=Registry::get('addons.my_custom_parameters');
	return (!empty($settings[$field_name]))?$settings[$field_name]:$default_value;
}

function fn_my_custom_parameters_apply_parameters()
{	
	//$settings=Registry::get('addons.my_custom_parameters');
	$settings=array();
	$section = Settings::instance()->getSectionByName('my_custom_parameters', Settings::ADDON_SECTION);
    $options = Settings::instance()->getList($section['section_id']);
	foreach($options['main'] as $o) {
		$settings[$o['name']]=$o['value'];
	}
	
	$a_discounts = array('a_discount','a_b_discount','a_s_discount','a_p_discount','a_p_b_discount','a_p_s_discount');
	
	// a_discount => promotion
	foreach ($a_discounts as $a) {
		$promotion_id=$settings[$a.'_promotion_id'];
		if (!empty($promotion_id)) {
			$data=array();
			$type = $a.'_type';
			$value = $a.'_value';
			
			if (!empty($settings[$type]) && !empty($settings[$value])) {
				if ('P'==$settings[$type]) {
					$data['bonuses'][1]['discount_bonus']='by_percentage';
				} elseif('A'==$settings[$type]){
					$data['bonuses'][1]['discount_bonus']='by_fixed';
				}
				
				$data['bonuses'][1]['bonus']='product_discount';
				$data['bonuses'][1]['discount_value']=$settings[$value];
				$data['bonuses'] = serialize($data['bonuses']);
				$data['status']='A';
				
			} else {
				$data['status']='D';
			}
			
			db_query("UPDATE ?:promotions SET ?u WHERE promotion_id = ?i", $data, $promotion_id);
		}
	}
}

/*
* Hooks
*/

function fn_my_custom_parameters_get_categories ($params, $join, $condition, &$fields, $group_by, $sortings, $lang_code)
{
	if ($params['simple'] == true) {
        $fields[] = '?:categories.product_count';
    }
}

function fn_my_custom_parameters_get_categories_before_cut_levels (&$categories_list, $params)
{
	if ('C'==AREA) $categories_list=fn_my_custom_parameters_remove_empty_categories($categories_list);
}

function fn_my_custom_parameters_remove_empty_categories($list)
{	/* 
	*	kvdikas poy afairei anadromika oles tis kenes apo proionta kai ypokathgories kathgories
	*/
	foreach ($list as $no=>&$item) {
		if (isset($item['subcategories'])) {
			if (!empty($item['subcategories']))	$item['subcategories'] = fn_my_custom_parameters_remove_empty_categories($item['subcategories']);
	
			if (empty($item['subcategories']) && 0==$item['product_count']) unset($list[$no]);
			
		} else {
			$tree_product_count = db_get_field("SELECT SUM(product_count) FROM ?:categories WHERE category_id=?i OR id_path LIKE ?l",$item['category_id'],$item['id_path'].'%');
			
			if (empty($tree_product_count)) unset($list[$no]);
		}
	}
	 /*
	foreach ($list as $no=>&$item) {
		if (isset($item['subcategories'])) {
			
			if (empty($item['subcategories']) && 0==$item['product_count']) unset($list[$no]);
			
		} else {
			$children_no = db_get_field("SELECT COUNT(category_id) FROM ?:categories WHERE parent_id=?i",$item['category_id']);
			
			if (empty($children_no) && 0==$item['product_count']) unset($list[$no]);
		}
	}
	*/
	return $list;
}