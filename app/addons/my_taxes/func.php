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

function fn_my_taxes_create_destination($data)
{
	$data['localization'] = '' ;
    $destination_id = $data['destination_id'] = db_query("REPLACE INTO ?:destinations ?e", $data);

	foreach (fn_get_translation_languages() as $data['lang_code'] => $_v) {
		db_query("REPLACE INTO ?:destination_descriptions ?e", $data);
	}

    $_data = array(
        'destination_id' => $destination_id
    );

    if (!empty($data['countries'])) {
        $_data['element_type'] = 'C';
        foreach ($data['countries'] as $key => $_data['element']) {
            db_query("INSERT INTO ?:destination_elements ?e", $_data);
        }
    }
	
	if (!empty($data['cities'])) {
        $cities = explode("\n", $data['cities']);
        $_data['element_type'] = 'T';
        foreach ($cities as $key => $value) {
            $value = trim($value);
            if (!empty($value)) {
                $_data['element'] = $value;
                db_query("INSERT INTO ?:destination_elements ?e", $_data);
            }
        }
    }

    return $destination_id;
}


function fn_my_taxes_install()
{
	// ftiaje ta pedia topouesias
	$pos_ids=array();
	
	$pos1_data=array(
		'destination'=>'pos1',
		'status'=>'A',
		'countries' => array('GR'),
		'cities'=>'vat_class_pos1'
	);
	
	$pos_ids['pos1']=fn_my_taxes_create_destination($pos1_data);
	
	$pos2_data=array(
		'destination'=>'pos2',
		'status'=>'A',
		'countries' => array('GR'),
		'cities'=>'vat_class_pos2'
	);
	
	$pos_ids['pos2']=fn_my_taxes_create_destination($pos2_data);
	
	$pos3_data=array(
		'destination'=>'pos3',
		'status'=>'A',
		'countries' => db_get_fields("SELECT code FROM ?:countries WHERE region = 'EU' AND code <>'GR'"),
		'cities'=>'vat_class_pos3'
	);
	
	$pos_ids['pos3']=fn_my_taxes_create_destination($pos3_data);
	
	$pos4_data=array(
		'destination'=>'pos4',
		'status'=>'A',
		'countries' => db_get_fields("SELECT code FROM ?:countries WHERE region <> 'EU'"),
		'cities'=>'vat_class_pos4'
	);
	
	$pos_ids['pos4']=fn_my_taxes_create_destination($pos4_data);

	$pos5_data=array(
		'destination'=>'pos5',
		'status'=>'A',
		'countries' => array(),
		'cities'=>'vat_class_pos5'
	);
	
	$pos_ids['pos5']=fn_my_taxes_create_destination($pos5_data);

	// balta sta settings toy addon
	if (!$section = Settings::instance()->getSectionByName('my_taxes', Settings::ADDON_SECTION)) {
		$section = Settings::instance()->updateSection(array(
			'parent_id' =>      0,
			'edition_type' =>   'ROOT,ULT:VENDOR',
			'name' =>           'my_taxes',
			'type' =>           'ADDON'
		));
	}
	
	foreach ($pos_ids as $option_name => $option_value) {
		if (!$setting_id = Settings::instance()->getId($option_name, 'my_taxes')) {
			$setting_id = Settings::instance()->update(array(
				'name' =>           $option_name,
				'section_id' =>     $section['section_id'],
				'edition_type' =>	'ROOT,ULT:VENDOR',
				'section_tab_id' => 0,
				'type' =>           'A',
				'position' =>       0,
				'is_global' =>      'N',
				'handler' =>        ''
			));
		}

		Settings::instance()->updateValueById($setting_id, $option_value, Registry::get('runtime.company_id'));
	}				
}


function fn_my_taxes_get_setting_field($field_name,$default_value='')
{
	$settings=Registry::get('addons.my_taxes');
	return (!empty($settings[$field_name]))?$settings[$field_name]:$default_value;
}

function fn_my_taxes_get_destination_ids()
{
	return array(
		'pos1'=>fn_my_taxes_get_setting_field('pos1'),
		'pos2'=>fn_my_taxes_get_setting_field('pos2'),
		'pos3'=>fn_my_taxes_get_setting_field('pos3'),
		'pos4'=>fn_my_taxes_get_setting_field('pos4'),
		'pos5'=>fn_my_taxes_get_setting_field('pos5')
	);
}

function fn_my_taxes_get_current_vat_class()
{
	if(!empty($_SESSION['auth']['vat_class']) && 1!=$_SESSION['auth']['vat_class'] && 
		// na exei gia epilogh mono Timologio h na exei epilejei timologio sto checkout
		('T'==$_SESSION['auth']['tim_lian'] || (isset($_SESSION['cart']['tim_lian']) && 'T'== $_SESSION['cart']['tim_lian']) ) ) { 
		
		return $_SESSION['auth']['vat_class'];
	}
	
	return 1;
}

/*
* HOOKS
* spoofs user_data using vat_class as state_id for tax calculation at pre controller and restores at post controller 
*/

function fn_my_taxes_calculate_taxes_pre(&$cart, $group_products, $shipping_rates, &$auth)
{ 
	if (!empty($cart['user_data']['s_city'])) $auth['s_city']=$cart['user_data']['s_city'];
	if (!empty($cart['user_data']['s_country'])) $auth['s_country']=$cart['user_data']['s_country'];
	
	if(!empty($auth['vat_class']) && 1!=$auth['vat_class'] && 
		// na exei gia epilogh mono Timologio h na exei epilejei timologio sto checkout
		((isset($auth['tim_lian']) && 'T'==$auth['tim_lian']) || (isset($cart['tim_lian']) && 'T'== $cart['tim_lian']) ) ) { 
	
		$cart['user_data']['s_city']='vat_class_pos'.$auth['vat_class'];
		
		// fere thn prvth xvra poy yparxei sto idio destination me thn polh vat_class gia na kanei match argotera
		$cart['user_data']['s_country']=db_get_field("SELECT a.element
			FROM ?:destination_elements a 
			INNER JOIN ?:destination_elements b ON a.destination_id=b.destination_id
			AND a.element_type='C' AND b.element_type='T' AND b.element=?s
			LIMIT 1",'vat_class_pos'.$auth['vat_class']);
			
	} else {
		$cart['user_data']['s_country']='GR';
		$cart['user_data']['s_city']='vat_class_pos1';
	}
}

function fn_my_taxes_calculate_taxes_post(&$cart, $group_products, $shipping_rates, &$auth, $calculated_data)
{ 
	if (!empty($auth['s_city'])) {
		$cart['user_data']['s_city']=$auth['s_city'];
		unset($auth['s_city']);
	} else $cart['user_data']['s_city']='';
	if (!empty($auth['s_country'])) {
		$cart['user_data']['s_country']=$auth['s_country'];
		unset($auth['s_country']);
	} else $cart['user_data']['s_country']='';
}

// den ginetai klhsh ths fn_calculate_taxes() opote ta kalv apo monos moy

function fn_my_taxes_gather_additional_product_data_before_discounts($product, &$auth, $params)
{
	fn_my_taxes_calculate_taxes_pre($_SESSION['cart'], array(), array(), $auth);
}

function fn_my_taxes_gather_additional_product_data_post(&$product, &$auth, $params)
{
	if (!empty($product['discount'])) {
		if ($product['package']!='Y') {
			$tx_base = (!empty($product['original_price'])) ? $product['original_price'] : $product['base_price'];
			
			// part of fn_get_taxed_and_clean_prices
			
			$product_taxes = fn_get_set_taxes($product['tax_ids']);

			$calculated_data = fn_calculate_tax_rates($product_taxes, $tx_base, 1, $auth, $_SESSION['cart']);
			
			// Apply taxes to product subtotal
			if (!empty($calculated_data)) {
				foreach ($calculated_data as $_k => $v) {
					if ($v['price_includes_tax'] != 'Y') {
						$tx_base += $v['tax_subtotal'];
					}
				}
			}
			
			$product['base_taxed_price']= $tx_base;
		} else {
			$product['taxed_price']=$product['price'];
		}
		
	}/* elseif (!empty($product['list_discount'])) {
	
	}
	*/
	
	fn_my_taxes_calculate_taxes_post($_SESSION['cart'], array(), array(), $auth, array());
}

/*
* EPIBOLH EMFANISHS TIMVN MONO APO TIMOK : ejairesh USERGROUP_ALL apo times
* EMFANISH KAI EFARMOGH FILTROY TIMHS SE TIMES ME FPA :
*	1. h fn_get_products einai ypeyuynh gia thn efarmogh toy filtroy => 
*		join me taxes kai tax_rates gia eyresh posostoy foroy kai replace th synuhkh me nea sthn opoia h timh einai me foro (prosoxh to addon ult kanei ki ayto override to sygk. hook)
*	2. allagh tvn ranges toy filtroy vste na emfanizei times me fpa =>
*		allagh ths closure function tvn price ranges kat antistoixia ths anvterv
*/

function fn_my_taxes_get_product_data($product_id, $field_list, $join, $auth, $lang_code, &$condition)
{
	$auth=$_SESSION['auth'];
	if (AREA != 'A' && !empty($auth['user_id'])) $condition.= db_quote(" AND ?:product_prices.usergroup_id <>?i", USERGROUP_ALL);
}

function fn_my_taxes_get_products (&$params, &$fields, $sortings, &$condition, &$join, $sorting, $group_by, $lang_code, $having)
{	
	//$params['use_caching']=false;
	$dest_id=fn_my_taxes_get_setting_field('pos'.fn_my_taxes_get_current_vat_class());
	$auth=$_SESSION['auth'];
	//fn_set_notification('W', __('notice'), var_export($params,true));
	
	if (isset($params['price_from']) && fn_is_numeric($params['price_from'])) {
		$price_from=fn_convert_price(trim($params['price_from']));
		
        $old_condition_from= db_quote(' AND prices.price >= ?d', $price_from);
        $new_condition_from= db_quote(' AND ROUND(prices.price*(1 + IFNULL(tax_rates.rate_value,0)/100), 2) >= ?d', $price_from);
		$condition = str_replace($old_condition_from,$new_condition_from,$condition);
       
		$old_condition_from= db_quote('AND (prices.price >= ?d OR shared_prices.price >= ?d)', $price_from, $price_from);
       // $new_condition_from= db_quote('AND (ROUND(prices.price*(1 + IFNULL(tax_rates.rate_value,0)/100),2) >= ?d 
		//								OR ROUND(shared_prices.price*(1 + IFNULL(tax_rates.rate_value,0)/100), 2) >= ?d)', $price_from, $price_from);
		$condition = str_replace($old_condition_from,$new_condition_from,$condition);
    }

    if (isset($params['price_to']) && fn_is_numeric($params['price_to'])) {
		$price_to=fn_convert_price(trim($params['price_to']));
		
        $old_condition_to= db_quote(' AND prices.price <= ?d', $price_to);
        $new_condition_to= db_quote(' AND ROUND(prices.price*(1 + IFNULL(tax_rates.rate_value,0)/100),2) <= ?d', $price_to);
		$condition = str_replace($old_condition_to,$new_condition_to,$condition);
		
		$old_condition_to= db_quote('AND (prices.price <= ?d OR shared_prices.price <= ?d)', $price_to, $price_to);
		//$new_condition_to= db_quote('AND (ROUND(prices.price*(1 + IFNULL(tax_rates.rate_value,0)/100), 2) <= ?d 
		//								OR ROUND(shared_prices.price*(1 + IFNULL(tax_rates.rate_value,0)/100),2) <= ?d)', $price_to, $price_to);
		$condition = str_replace($old_condition_to,$new_condition_to,$condition);
    }
	
	$old_price_condition = '';
    if (in_array('prices', $params['extend'])) {
        // AFAIRESE to usergroup_all
		if ($params['area'] != 'A' && $_SESSION['auth']['user_id']>0) $condition.= db_quote(' AND prices.usergroup_id <> ?i', USERGROUP_ALL);
    }

    // get prices for search by price
    if (in_array('prices2', $params['extend'])) {
		// AFAIRESE to usergroup_all
		if ($params['area'] != 'A' && $_SESSION['auth']['user_id']>0) $condition.= db_quote(' AND prices_2.usergroup_id <> ?i', USERGROUP_ALL);
    }
	
	if (in_array('prices', $params['extend']) && in_array('prices2', $params['extend']) ) {
		//join to foro
		$join .= " LEFT JOIN ?:taxes as taxes ON products.tax_ids = taxes.tax_id AND taxes.price_includes_tax='N'
				   LEFT JOIN ?:tax_rates as tax_rates ON taxes.tax_id=tax_rates.tax_id AND tax_rates.destination_id = $dest_id AND rate_type='P'";
		//$fields['taxed_price'] = '(MIN(IF(prices.percentage_discount = 0, prices.price, prices.price - (prices.price * prices.percentage_discount)/100)) * (1 + IFNULL(tax_rates.rate_value,0)/100)) as taxed_price';
	}
}

function fn_my_taxes_get_product_filter_fields(&$filters) 
{	
	$filters ['P']['conditions'] = function($db_field, $join, $condition) 
	{
		$dest_id=fn_my_taxes_get_setting_field('pos'.fn_my_taxes_get_current_vat_class());
		
		$db_field='price * (1 + IFNULL(tax_rates.rate_value,0)/100)';
		$join .= db_quote("
			LEFT JOIN ?:taxes as taxes ON products.tax_ids = taxes.tax_id AND taxes.price_includes_tax='N'
			LEFT JOIN ?:tax_rates as tax_rates ON taxes.tax_id=tax_rates.tax_id AND tax_rates.destination_id = $dest_id AND rate_type='P'
			LEFT JOIN ?:product_prices as prices_2 ON ?:product_prices.product_id = prices_2.product_id AND ?:product_prices.price > prices_2.price AND prices_2.lower_limit = 1 AND prices_2.usergroup_id IN (?n)",
			$_SESSION['auth']['usergroup_ids']
		);

		$condition .= db_quote("
			AND ?:product_prices.lower_limit = 1 AND ?:product_prices.usergroup_id IN (?n) AND prices_2.price IS NULL",
			$_SESSION['auth']['usergroup_ids']
		);

		if (fn_allowed_for('ULTIMATE') && Registry::get('runtime.company_id')) {
			$db_field = "IF(shared_prices.product_id IS NOT NULL, shared_prices.price, ?:product_prices.price) * (1 + IFNULL(tax_rates.rate_value,0)/100)";
			$join .= db_quote(" LEFT JOIN ?:ult_product_prices AS shared_prices ON shared_prices.product_id = products.product_id"
				. " AND shared_prices.lower_limit = 1"
				. " AND shared_prices.usergroup_id IN (?n)"
				. " AND shared_prices.company_id = ?i",
				$_SESSION['auth']['usergroup_ids'],
				Registry::get('runtime.company_id')
			);
		}

		return array($db_field, $join, $condition);
	} ; 
}

function fn_my_taxes_load_products_extra_data(&$extra_fields, $products, $product_ids, $params, $lang_code)
{
	if ($_SESSION['auth']['user_id']>0
		&& in_array('prices', $params['extend'])
        && $params['sort_by'] != 'price'
        && !in_array('prices2', $params['extend'])
		&& $params['area'] != 'A'
		) {
		
		$extra_fields['?:product_prices']['condition'].= db_quote(' AND ?:product_prices.usergroup_id <> ?i', USERGROUP_ALL);
	}
}

