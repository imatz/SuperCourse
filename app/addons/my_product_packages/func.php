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

function fn_my_product_packages_get_setting_field($field_name,$default_value='')
{
	$settings=Registry::get('addons.my_product_packages');
	return (!empty($settings[$field_name]))?$settings[$field_name]:$default_value;
}

function fn_my_product_packages_generate_code()
{
	$user_bridge = new Sync\User();
	$user_code = $user_bridge->get_customer_by_shop_id($_SESSION['auth']['user_id']);
	// ta 5 prvta einai o kvdikos pelath
	$user_code = str_pad($user_code, 5, "0", STR_PAD_LEFT);
	do {
		$code=$user_code;
		// ta epomena 4 einai tyxaia
		for ($i=0; $i<4; $i++) {
			$code.= mt_rand(0, 9);
		}
		// to teleytaio einai chfio elegxoy
		$code.= sum_till_one_digit($code);
		
		$code_exists=db_get_field("SELECT COUNT(product_id) FROM ?:products WHERE product_code=?s", $code);
		
	} while ($code_exists);
	
	return $code;
}

function sum_till_one_digit($number)
{
	$number = (string) $number;
	while (strlen($number) > 1) {
	
		$tmp = 0;
		for ($i = 0, $j = strlen($number); $i < $j; $i++) { 
			$tmp += $number[$i];
		}
		
		$number = (string) $tmp;
	}
	
	return $number;
}

function fn_my_product_packages_delete_product_post($product_id, $product_deleted) 
{
  if (!empty($product_deleted)) {
    db_query('DELETE FROM ?:package_products WHERE package_id = ?i', $product_id);
    db_query('DELETE FROM ?:package_products WHERE product_id = ?i', $product_id);
    db_query('DELETE FROM ?:package_data WHERE package_id = ?i', $product_id);
  }
}

function fn_my_product_packages_update_product_post($product_data, $product_id, $lang_code, $create)
{
	if (isset($product_data['package_data'])) {
		$package_data = $product_data['package_data'];
		$package_data['package_id'] = $product_id;
		
		if($create) {
			db_query("INSERT INTO ?:package_data ?e", $package_data);
			foreach ($product_data['package_products'] as $product) {
				db_query("INSERT INTO ?:package_products ?e", array('package_id'=>$product_id, 'product_id'=>$product['product_id']));
			}
		} else {
			unset($package_data['user_id']);
			unset($package_data['creation']);
			db_query("UPDATE ?:package_data SET ?u WHERE package_id=?i", $package_data, $product_id);
		//	db_query("DELETE FROM ?:package_products WHERE package_id=?i", $product_id);
		}
		
		// foreach ($product_data['package_products'] as $product) {
			// db_query("INSERT INTO ?:package_products ?e", array('package_id'=>$product_id, 'product_id'=>$product['product_id']));
		// }
	}
}


// apenergopoihse ta paketa apo thn anazhthsh
function fn_my_product_packages_get_products_before_select(&$params, &$join, &$condition, $u_condition, $inventory_join_cond, $sortings, $total, $items_per_page, $lang_code, $having)
{
	//if ('C'==AREA) {
	$packages='N';
	if (!empty($params['packages']) && 'Y' == $params['packages']) {
		$packages='Y';
		// bgalta
		$params['extend']=array_diff($params['extend'], array('prices','prices2','categories','sharing'));
    
    // sort by timestamp
    if ('C'==AREA || empty($params['sort_by']) || ! in_array($params['sort_by'], array('timestamp','code','product','status'))) {
      $params['sort_order']='desc';
      $params['sort_by']='timestamp';
      
		}
		$join.= db_quote(" INNER JOIN ?:package_data packdt ON products.product_id = packdt.package_id");
		
		if ('C' == AREA ) {
      if (!empty($_SESSION['auth']['account_type']) && 'S' == $_SESSION['auth']['account_type']) {
        $user_id=$_SESSION['auth']['user_id'];
        if (!empty($_SESSION['auth']['real_user_id'])) $user_id=$_SESSION['auth']['real_user_id'];
			
        $join.= db_quote(" AND packdt.user_id=?i", $user_id);
      }
		} else {
      $join.= db_quote(" INNER JOIN ?:users u ON packdt.user_id = u.user_id");
      
      // search creator
      if (!empty($params['user_login'])) 
        $join.= db_quote(" AND packdt.user_id IN (SELECT user_id FROM ?:users WHERE user_login=?s)", $params['user_login']);
      
      // search in package products
      if (isset($params['pp']) && fn_string_not_empty($params['pp'])) {

        $params['pp'] = trim($params['pp']);
        
        if ($params['match'] == 'any') {
            $query_pieces = fn_explode(' ', $params['pp']);
            $search_type = ' OR ';
        } elseif ($params['match'] == 'all') {
            $query_pieces = fn_explode(' ', $params['pp']);
            $search_type = ' AND ';
        } else {
            $query_pieces = array($params['pp']);
            $search_type = '';
        }
        
        $_cond=array();
        
        foreach ($query_pieces as $piece) {
          
          if (strlen($piece) == 0) {
            continue;
          }
          
          $search_conditions = array();
          
          $search_conditions [] = db_quote("IFNULL(des.product_alt,des.product) LIKE ?l", '%' . $piece . '%');
          $search_conditions [] = db_quote("des.full_description LIKE ?l", '%' . $piece . '%');
          $search_conditions [] = db_quote("des.full_description LIKE ?l", '%' . htmlentities($piece, ENT_QUOTES, 'UTF-8') . '%');
          $search_conditions [] = db_quote("prods.product_code LIKE ?l", "%{$piece}%");
          
          $_cond [] = '(' . implode(' OR ', $search_conditions) . ')';
        }

        if (!empty($_cond)) {
          $condition .= ' AND products.product_id IN ( 
            SELECT package_id FROM ?:package_products papr'. 
            db_quote(" LEFT JOIN ?:product_descriptions as des ON des.product_id = papr.product_id AND des.lang_code = ?s ", $lang_code).
            db_quote(" LEFT JOIN ?:products as prods ON prods.product_id = papr.product_id").
            ' WHERE (' . implode($search_type, $_cond) . ')
          ) ';
        }

        unset($search_conditions);
      }
    }
		
		if (!empty($params['creation'])) {
			$condition.= db_quote(" AND packdt.creation in (?a)",$params['creation']);
		}
	}
	$condition.= " AND products.package='$packages'";
//	}
}

function fn_my_product_packages_get_products($params, &$fields, $sortings, &$condition, $join, $sorting, $group_by, $lang_code, $having)
{	
  if (in_array('product_name', $params['extend'])) {
    $fields['product_alt'] = 'descr1.product_alt as product_alt';
  }
  
  // mhn deixneis proionta apo kryfes kathgories sthn anazhthsh
  if ('C'==AREA && (empty($params['package_picker'])) && empty($params['packages']) && !empty($params['search_performed']) && 'Y'==$params['search_performed']) {
    $join = str_replace(db_quote(" AND ?:categories.status IN (?a) ", array('A','H')), db_quote(" AND ?:categories.status IN (?a) ", array('A')), $join);  
  }  
    
  
//	if ('C'==AREA) {
	if (!empty($params['packages']) && 'Y' == $params['packages']) {
		$fields['package_data']='packdt.*';
		//$fields['creation']='products.timestamp';
		
		// override showing only enabled products for packages
		if ('C'==AREA) 
		{
			$condition = str_replace(db_quote(' AND (' . fn_find_array_in_set($_SESSION['auth']['usergroup_ids'], 'products.usergroup_ids', true) . ')' . db_quote(' AND products.status IN (?a)', array('A'))), '',$condition);	  
	  
	  		//
	  		// nikosgkil 
			//
	 	 	/*$_p_statuses = array('D', 'A');
		 	$condition .= ($params['area'] == 'C') ? ' AND (' . fn_find_array_in_set($_SESSION['auth']['usergroup_ids'], 'products.usergroup_ids', true) . ')' . db_quote(' AND products.status IN (?a)', $_p_statuses) : '';*/
	
	} 
	else 
	{
      $fields['user']='u.user_login,u.lastname,u.firstname,u.email';
    }
	}
//	}
	$fields['package']='products.package';	
}

function fn_my_picker_plain_categories_tree($category_id = '0')
{
  $params = array (
    'category_id' => $category_id,
    'simple' => false,
    'visible' => false,
    'plain' => true,
    
    'package_picker'=>true
  );    
  list($categories, ) = fn_get_categories($params);
  return $categories;
}

function fn_my_product_packages_get_categories($params, $join, &$condition, $fields, $group_by, $sortings, $lang_code)
{
  // mhn deixneis proionta apo kryfes kathgories sthn anazhthsh
  if ('C'==AREA && !empty($params['package_picker'])) {
    $condition = str_replace(db_quote(" AND ?:categories.status IN (?a) ", array('A')), db_quote(" AND ?:categories.status IN (?a) AND ?:categories.category_id <>?i ", array('A','H'), fn_my_product_packages_get_setting_field('packages_category')), $condition);  
  }  
 
}

function fn_my_product_packages_get_products_post(&$products, $params, $lang_code)
{
	foreach ($products as &$pr) {
		if ('Y' == $pr['package']) {
			$pr['price']=$pr['taxed_price']=$pr['base_taxed_price']=$pr['clean_price']=$pr['base_price']=0;
			
			fn_my_product_packages_get_package_products($pr);
			
			$pr['taxes']=array();
			foreach($pr['package_products'] as $ppr) { //fn_print_r($ppr);
				$pr['price'] += $ppr['price'];
				if ('C'==AREA) {
					$pr['taxed_price'] += $ppr['taxed_price'];
					$pr['clean_price'] += $ppr['clean_price'];
					$pr['base_price'] += $ppr['base_price'];
					/*
					* den exei nohma me tis ekptvseis gt den ua bgainoyn oi prajeis kauvs oi ekptvseis efarmozontai sthn timh meta fpa
					foreach ($ppr['taxes'] as $tax_id => $tax) {
						if(empty($pr['taxes'][$tax_id])) {
							$pr['taxes'][$tax_id] = $tax;
						} else {
							$pr['taxes'][$tax_id]['tax_subtotal'] += $tax['tax_subtotal'];
						}
						
					}
					*/
				}
			}
			
			/*
			* !!! TIMES !!! h anauesh ginetai me thn ejhs seira apo ayth th function h to cscart genikotera
			* real_{base_}price: h arxikh {base_}price 
			* base_taxed_price: arxikh timh me foro
			* price: ua mpei h arxikh timh me foro gia na ypologistei panv ths h ekptvsh 
			* taxed_price: ua katalhjei h price meta thn ektpvsh
			*/
			
			$pr['real_base_price'] = $pr['base_price'];
			$pr['real_price'] = $pr['price'];
			$pr['price'] = $pr['base_price'] = $pr['base_taxed_price'] = $pr['taxed_price'];
			
		  //fn_print_r($pr);
		} 
	}
}

/*
* default to cscart ypologizei tis ekptvseis kai meta to foro
* emeis antistrefoyme th seira me hack {kratv thn parametro get_discounts se custom metablhth kai klvnopoiv ton kvdika tvn ektpvsevn}
* etsi lynoyme thn anantistoixia timvn memonomenoy proiontos - synueshs logv daforas 1 cent apo stroggylopoihseis
*/

function fn_my_product_packages_gather_additional_products_data_params ( $product_ids, &$params, $products, $auth, $products_images, $additional_images, $product_options, $has_product_options, $has_product_options_links)
{
	$params['get_my_discounts'] = $params['get_discounts']; 
	$params['get_discounts'] = false; 
}

function fn_my_product_packages_gather_additional_product_data_before_discounts (&$product, $auth, $params)
{	
	
}

function fn_my_product_packages_gather_additional_product_data_post (&$product, $auth, $params)
{	
if ('C'==AREA) {
	// fn.catalog.php lines 675 - 708
	if ($params['get_my_discounts'] && !isset($product['exclude_from_calculate'])) {
		$product['real_base_price'] = $product['base_price'];
		$product['real_price'] = $product['price'];
		$product['price'] = $product['base_price'] = $product['base_taxed_price'] = $product['taxed_price'];
	
		fn_promotion_apply('catalog', $product, $auth);
		if (!empty($product['prices']) && is_array($product['prices'])) {
			$product_copy = $product;
			foreach ($product['prices'] as $pr_k => $pr_v) {
				$product_copy['base_price'] = $product_copy['price'] = $pr_v['price'];
				fn_promotion_apply('catalog', $product_copy, $auth);
				$product['prices'][$pr_k]['price'] = $product_copy['price'];
			}
		}

		if (empty($product['discount']) && !empty($product['list_price']) && !empty($product['price']) && floatval($product['price']) && $product['list_price'] > $product['price']) {
			$product['list_discount'] = fn_format_price($product['list_price'] - $product['price']);
			$product['list_discount_prc'] = sprintf('%d', round($product['list_discount'] * 100 / $product['list_price']));
		}
    
    $product['taxed_price'] = $product['price'];
    $product['base_price'] = $product['real_base_price'];
	
	
	// nikosgkil - zitao apo ti vasi na mou dinei tis times lianikis twn proiontwn
	$lianikis_price = db_get_field("SELECT price FROM ?:product_prices WHERE usergroup_id='0' AND product_id = '?i'", $product['product_id']);
	$product['lianikis_price'] = $lianikis_price;
    //$product['price'] = $product['real_price'];
	}

	// FIXME: old product options scheme
	$product['discounts'] = array('A' => 0, 'P' => 0);
	if (!empty($product['promotions'])) {
		foreach ($product['promotions'] as $v) {
			foreach ($v['bonuses'] as $a) {
				if ($a['discount_bonus'] == 'to_fixed') {
					$product['discounts']['A'] += $a['discount'];
				} elseif ($a['discount_bonus'] == 'by_fixed') {
					$product['discounts']['A'] += $a['discount_value'];
				} elseif ($a['discount_bonus'] == 'to_percentage') {
					$product['discounts']['P'] += 100 - $a['discount_value'];
				} elseif ($a['discount_bonus'] == 'by_percentage') {
					$product['discounts']['P'] += $a['discount_value'];
				}
			}
		}
	}
}
	
}

function fn_my_product_packages_get_package_products(&$pr)
{
	$product_ids = db_get_fields("SELECT product_id FROM ?:package_products WHERE package_id=?i", $pr['product_id']);
	
	$pr['package_products']=array();
	
	if (!empty($product_ids)) {
		
		list($pr['package_products'], $junk)=fn_get_products(array('pid'=>$product_ids));
		
		foreach($pr['package_products'] as &$ppr) {
			$ppr['package_id']=$pr['product_id']; // baltoys to id toy paketoy
			$ppr['package']='I';
		}
		unset($ppr);
		
		$additional_params=array(
			'get_discounts' => true,
			'get_taxed_prices' => true,
		);
		
		fn_gather_additional_products_data($pr['package_products'],$additional_params);
		//fn_my_product_packages_get_retail_data($pr['package_products']);
	}
}

/*
* timh lianikhs
* allazei prosvrina to session vste na nomizei oti einai pelaths lianikhs 
* pairnei thn timh kai meta epistrefei tis kanonikes times
*/

function fn_my_product_packages_get_retail_data(&$products)
{
	$auth = &$_SESSION['auth'];
	
	$collect= array('base_price' ,
					'clean_price' ,
					'base_taxed_price' ,
					'taxed_price',
					'price',
					'taxes',
					'discounts',
					'discount',
					'promotions');
	
	if (!empty($auth['user_id'])) {
		$user_id = $auth['real_user_id'] = $auth['user_id'];
		$auth['user_id']=0;
		$usergroup_ids = $auth['usergroup_ids'];
		$auth['usergroup_ids'] = array(USERGROUP_ALL, USERGROUP_GUEST);
		$vat_class=$auth['vat_class'];
		$auth['vat_class']=1;
		$account_type = $auth['account_type'];
		unset($auth['account_type']);
    }  
	
	foreach ($products as &$product) {
		list($tmp, $junk)=fn_get_products(array('pid'=>array($product['product_id']),'packages'=>$product['package']));
		if (!empty($tmp)) {
			$tmp2=$tmp;
			
			$retail_data = $retail_data_no_discounts = array();
			
			fn_gather_additional_products_data($tmp, array('get_discounts' => true, 'get_taxed_prices' => true));
			foreach ($collect as $c) if(isset($tmp[$product['product_id']][$c])) $retail_data[$c]=$tmp[$product['product_id']][$c];
			
			fn_gather_additional_products_data($tmp2, array('get_discounts' => false, 'get_taxed_prices' => true));
			foreach ($collect as $c) if(isset($tmp2[$product['product_id']][$c])) $retail_data_no_discounts[$c]=$tmp2[$product['product_id']][$c];
			
			$product['retail_data_no_discounts']=$retail_data_no_discounts;
			$product['retail_data']=$retail_data;
		} else $product['retail_data'] = $product['retail_data_no_discounts'] = array();		
	}
		
	if (!empty($auth['real_user_id'])) {	
		$auth['user_id'] = $user_id;
		unset($auth['real_user_id']);
		$auth['usergroup_ids'] = $usergroup_ids;
		$auth['vat_class'] = $vat_class;
		$auth['account_type'] = $account_type;
	}	
	
}

/*
* timh synueshs xvris ekptvseis b,c
* dinei to flag no_package_discount sto product
*/

function fn_my_product_packages_get_package_data_no_discount(&$products)
{
	$auth = &$_SESSION['auth'];
	$auth['no_package_discount']=true;
	
	$collect= array('base_price' ,
					'clean_price' ,
					'base_taxed_price' ,
					'taxed_price',
					'price',
					'taxes',
					'discounts',
					'discount',
					'promotions');
	
	foreach ($products as &$product) {
		if ($product['package']=='Y'){
		list($tmp, $junk)=fn_get_products(array('pid'=>array($product['product_id']),'packages'=>$product['package']));
			if (!empty($tmp)) {
				$retail_data = $no_package_discount_data = array();
				
				fn_gather_additional_products_data($tmp, array('get_discounts' => true, 'get_taxed_prices' => true));
				foreach ($collect as $c) if(isset($tmp[$product['product_id']][$c])) $no_package_discount_data[$c]=$tmp[$product['product_id']][$c];
				
				// allh mia gia retail
				if (!empty($auth['user_id'])) {
					$user_id = $auth['real_user_id'] = $auth['user_id'];
					$auth['user_id']=0;
					$usergroup_ids = $auth['usergroup_ids'];
					$auth['usergroup_ids'] = array(USERGROUP_ALL, USERGROUP_GUEST);
					$vat_class=$auth['vat_class'];
					$auth['vat_class']=1;
					$account_type = $auth['account_type'];
					unset($auth['account_type']);

				}  
				// ante pali 
				list($tmp2, $junk)=fn_get_products(array('pid'=>array($product['product_id']),'packages'=>$product['package']));
				fn_gather_additional_products_data($tmp2, array('get_discounts' => true, 'get_taxed_prices' => true));
				foreach ($collect as $c) if(isset($tmp2[$product['product_id']][$c])) $retail_data[$c]=$tmp2[$product['product_id']][$c];
				
				if (!empty($auth['real_user_id'])) {	
					$auth['user_id'] = $user_id;
					unset($auth['real_user_id']);
					$auth['usergroup_ids'] = $usergroup_ids;
					$auth['vat_class'] = $vat_class;
					$auth['account_type'] = $account_type;
				}	
				
				$product['no_package_discount_data']=$no_package_discount_data;
				$product['no_package_discount_data']['retail_data']=$retail_data;
			} else $product['no_package_discount_data'] = array();		
		}
	}
	
	unset($auth['no_package_discount']);
}


// permission check 
function fn_my_product_packages_get_owner($product_id)
{
	return db_get_field("SELECT user_id FROM ?:package_data WHERE package_id=?i",$product_id);
}
// print code sheet
function fn_my_product_packages_get_code($product_id)
{
	return db_get_field("SELECT product_code FROM ?:products WHERE product_id=?i",$product_id);
}
// add package to cart
function fn_my_product_packages_is_package($product_id)
{
	return db_get_field("SELECT package FROM ?:products WHERE product_id=?i",$product_id);
}
// add package to cart
function fn_my_product_packages_get_package_id($product_code)
{
	$sql = db_quote("SELECT product_id FROM ?:products p INNER JOIN ?:package_data pd ON p.product_id = pd.package_id WHERE p.package='Y' AND p.product_code=?s AND p.status='A'",$product_code);
	if (isset($_SESSION['auth']['account_type'])  && 'S' == $_SESSION['auth']['account_type']) {
		$sql.= db_quote(" AND pd.user_id=?i", $_SESSION['auth']['user_id']);
	}
	return db_get_field($sql);
}

function fn_my_product_packages_get_package_data($product_id)
{
	return db_get_row("SELECT * FROM ?:package_data WHERE package_id=?i",$product_id);
}

function fn_my_product_packages_get_product_price_post($product_id, $amount, $auth, &$price)
{
	$params = array(
		'packages'=>'Y',
		'pid'=>array($product_id)
	);
	list($product,$junk)=fn_get_products($params);
	
	if (!empty($product)) {
		//fn_gather_additional_products_data($product, array('get_discounts' => true));
		//fn_set_notification('E','var_export',var_export($product,true));
		$price = $product[$product_id]['price'];
	}

}

function fn_my_product_packages_add_to_cart(&$cart, $product_id, $_id)
{
	$cart['products'][$_id]['package'] = $cart['products'][$_id]['extra']['package'] = $package = fn_my_product_packages_is_package($product_id);
	if ('Y'== $package) fn_my_product_packages_get_package_products($cart['products'][$_id]);
}

function fn_my_product_packages_pre_get_cart_product_data($hash, $product, $skip_promotion, $cart, $auth, $promotion_amount, &$fields, $join)
{
	$fields[]='?:products.package';
}

function fn_my_product_packages_get_cart_product_data_post($hash, $product, $skip_promotion, $cart, $auth, $promotion_amount, &$_pdata)
{
	if ('Y'==$_pdata['package']) {
		fn_my_product_packages_get_package_products($_pdata);
	}
}

function fn_my_product_packages_pre_place_order(&$cart, $allow, $product_groups)
{	
	// bale ton timok (einai to kleidi toy 1x1 array poy prokyptei ap thn tomh tvn session usergroups me ola ta price usergroups ths gefyras)
	
	$pricegroups = array_intersect(Sync\Pricegroup::get_pricegroup_ids(), $_SESSION['auth']['usergroup_ids']);
	
	$pricegroup = (empty($pricegroups))? 1: key($pricegroups);
	
	fn_my_product_packages_get_retail_data ($cart['products']);
	
	foreach ($cart['products'] as &$product) {
		$product['extra']['retail_data'] = $product['retail_data']; 
		$product['extra']['retail_data_no_discounts'] = $product['retail_data_no_discounts']; 
		$product['extra']['pricegroup'] = $pricegroup; 
		if($product['package'] == 'Y') {
			fn_my_product_packages_get_package_products($product);
			fn_my_product_packages_get_retail_data($product['package_products']);
			$product['extra']['package_products']=$product['package_products'];
		}
	}
	
}

function fn_my_product_packages_get_discount_bonus($type)
{
	$map=array(
		'A'=>'by_fixed',
		'P'=>'by_percentage'
	);
	return (!empty($map[$type]))? $map[$type]: '';
}

function fn_my_product_packages_get_package_field($product)
{
	return (empty($product['package_id']))? $product['package']: 'I';
}

function fn_my_product_packages_promotion_apply_pre(&$promotions, $zone, $data, $auth, $cart_products)
{
	unset($promotions[$zone][-1]);
	unset($promotions[$zone][-2]);
	if (!empty($data['package']) && 'Y'==$data['package'] && empty($auth['no_package_discount'])) { 
		$package_data = fn_my_product_packages_get_package_data($data['product_id']);
		/*unset($promotions[$zone][-1]);
		unset($promotions[$zone][-2]);
		$promotions=array();
		*/
		//fn_set_notification('E','var_export',var_export($package_data,true));
		if(!empty($package_data['b_discount_type']) && !empty($package_data['b_discount_value'])) { 
			$promotions[$zone][-1]=array(
				'promotion_id'=>-1,
				'stop'=>'N',
				'conditions' => array (	
					'set'=>'all',
					'set_value' => 1,
					'conditions' => array (1 => array (	'operator' => 'in',	'condition' => 'users',	'value' => $package_data['user_id']	) )
				),
				'bonuses' => array (1 => array (	'bonus' => 'product_discount',	'discount_bonus' => fn_my_product_packages_get_discount_bonus($package_data['b_discount_type']),	'discount_value' => $package_data['b_discount_value']	) ),

			);
		} 
		if(!empty($package_data['c_discount_type']) && !empty($package_data['c_discount_value'])) {
			$promotions[$zone][-2]=array(
				'promotion_id'=>-2,
				'stop'=>'N',
				'conditions' => array (	
					'set'=>'all',
					'set_value' => 1,
					'conditions' => array (1 => array (	'operator' => 'eq',	'condition' => 'usergroup',	'value' => USERGROUP_GUEST	) ) 
				),
				//'conditions_hash' => 'usergroup='.USERGROUP_GUEST, 
				'bonuses' => array (1 => array (	'bonus' => 'product_discount',	'discount_bonus' => fn_my_product_packages_get_discount_bonus($package_data['c_discount_type']),	'discount_value' => $package_data['c_discount_value']	) ),

			);
		}
	}/* else { //dystyxvs anairoyme th static metablhth promotions
		$params = array(
            'active' => true,
            'expand' => true,
            'zone' => $zone,
            'sort_by' => 'priority',
            'sort_order' => 'asc'
        );

        list($promotions[$zone]) = fn_get_promotions($params);
	}
	*/
}

function fn_my_product_packages_post_delete_user($user_id, $user_data, $result)
{
	if (!empty($result)) {
		$package_ids = db_get_fields("SELECT package_id FROM ?:package_data WHERE user_id=?i", $user_id);
    foreach($package_ids as $pid)
      fn_delete_product($pid);
	}
}



function fn_my_product_packages_suggest_title($term)
{
  return db_get_fields("SELECT IFNULL(product_alt,product) FROM ?:product_descriptions 
    WHERE product_id IN (SELECT product_id FROM ?:package_products) 
    AND IFNULL(product_alt,product) LIKE ?l",'%'.$term.'%');
}