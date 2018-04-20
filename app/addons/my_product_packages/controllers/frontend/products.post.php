<?php
require('tcpdf_barcodes_1d.php');


if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;
use Tygh\Pdf;

if (in_array($mode, array('add', 'package_picker', 'update', 'delete', 'manage', 'print'))) {

if (empty($auth['user_id']) || ($auth['account_type']!='S'))
	return array(CONTROLLER_STATUS_DENIED);

if (!empty($_REQUEST['product_id'])) {
	if ($auth['user_id']!=fn_my_product_packages_get_owner($_REQUEST['product_id']))
		return array(CONTROLLER_STATUS_DENIED);
} 
 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$id = (empty($_REQUEST['product_id']))? 0: $_REQUEST['product_id']; 
    if ($mode == 'update') { 
		$product_data=array();
		$discount_error = false;
		
		if (!empty($id)){ // update status || discount if admin
			if ($auth['act_as_user']) {
				
				$product_data['package_data'] = $_REQUEST['product_data']['package_data'];
				
				if (empty($product_data['package_data']['c_discount_type'])) {
					$product_data['package_data']['c_discount_value']=0;
				} else if (empty($product_data['package_data']['c_discount_value'])) {
					$discount_error = true;
					fn_set_notification('E', __('error'), __('text_discount_c_error'));
				}
				
				
				if (empty($product_data['package_data']['b_discount_type'])) {
					$product_data['package_data']['b_discount_value']=0;
				} else if (empty($product_data['package_data']['b_discount_value'])) {
					$discount_error = true;
					fn_set_notification('E', __('error'), __('text_discount_b_error'));
				}
				
			}
		} else { //create
			if (!empty($_REQUEST['product_data']['product'])) {
				if (empty($_REQUEST['product_data']['package_products'])) {
					fn_set_notification('E', __('error'), __('text_no_items_defined', array('[items]'=>__('package_products'))));

					return array(CONTROLLER_STATUS_REDIRECT, 'products.add');
				} else {
					$product_data['package'] = 'Y';
					$product_data['price'] = 0;
					$product_data['product'] = $_REQUEST['product_data']['product'];
					$product_data['category_ids'] = array(fn_my_product_packages_get_setting_field('packages_category'));
					$product_data['product_code'] = fn_my_product_packages_generate_code();
					$product_data['package_data']['user_id'] = $auth['user_id'];
					$product_data['package_data']['creation'] = $_REQUEST['product_data']['package_data']['creation'];
					$product_data['package_products']=$_REQUEST['product_data']['package_products'];
				}
			}
		}
		$product_data['status'] = empty($_REQUEST['product_data']['status'])?'A':$_REQUEST['product_data']['status'];
		
		if ($discount_error) $product_id = false;
		else $product_id = fn_update_product($product_data, $id, DESCR_SL);

		if ($product_id === false) {
			// Some error occured
			fn_save_post_data('product_data');

			return array(CONTROLLER_STATUS_REDIRECT, !empty($id) ? 'products.manage' : 'products.add');
		}
        
        $suffix = '.manage';
    }
	
	return array(CONTROLLER_STATUS_OK, 'products' . $suffix);
	
} elseif ($mode == 'manage' ) {
/*
	unset($_SESSION['product_ids']);
    unset($_SESSION['selected_fields']);
*/
    $params = $_REQUEST;

	$params['packages']='Y';
	
    if (fn_allowed_for('ULTIMATE')) {
        $params['extend'][] = 'sharing';
    }


    list($products, $search) = fn_get_products($params, Registry::get('settings.Appearance.admin_products_per_page'), DESCR_SL);
    fn_gather_additional_products_data($products, array('get_discounts' => true));
	fn_my_product_packages_get_retail_data($products);
	fn_my_product_packages_get_package_data_no_discount($products);
//fn_print_r($products);
    $page = $search['page'];
    $valid_page = db_get_valid_page($page, $search['items_per_page'], $search['total_items']);

    if ($page > $valid_page) {
        $_REQUEST['page'] = $valid_page;

        return array(CONTROLLER_STATUS_REDIRECT, Registry::get('config.current_url'));
    }

    Tygh::$app['view']->assign('show_package_retail_price', fn_my_custom_parameters_get_setting_field('show_package_retail_price'));
    Tygh::$app['view']->assign('products', $products);
    Tygh::$app['view']->assign('search', $search);

} elseif ($mode == 'delete') {

	if (!empty($_REQUEST['product_id'])) {
		$result = fn_delete_product($_REQUEST['product_id']);
		if ($result) {
			fn_set_notification('N', __('notice'), __('text_product_has_been_deleted'));
		} else {
			return array(CONTROLLER_STATUS_REDIRECT, 'products.manage');
		}
	}

	return array(CONTROLLER_STATUS_REDIRECT, 'products.manage');

} elseif ($mode == 'add') {

    $product_data = fn_restore_post_data('product_data');
    Tygh::$app['view']->assign('product_data', $product_data);

} elseif ($mode == 'print') {
	
	if (!empty($_REQUEST['product_id'])) {
		
		//
		// nikosgkil
		//
		$prod_id = $_REQUEST['product_id'];
		$product_name = db_get_field("SELECT product FROM ?:product_descriptions WHERE product_id = ?i", $prod_id);
		$code = fn_my_product_packages_get_code($_REQUEST['product_id']);
		
		
		$code_in_string = (string)$code;		
		$barcodeobj = new TCPDFBarcode($code_in_string, 'C128');
		$tcpdfbarcode = $barcodeobj->getBarcodePNG(1, 30, array(0,0,0));
		file_put_contents('barcode.png', $tcpdfbarcode);
		
		//
		// nikosgkil
		//
		
		$cols = 2;
		$rows = 4; 
		
		$code_arr = array_fill(0, $cols, __('class_package_name').': '.$product_name.'<br>'.__('kwdikos_paraggelias_synthesis').'<br>'.$code.'<br><img src="http://www.supercourse-eshop.gr/barcode.png" alt="Package Code" width="200"><br>');
		
		$tr = '<tr><td style="border:1px dashed #555; padding: 8px; height:245; width:50%; text-align:center;">'.implode('</td><td style="border:1px dashed #555; padding: 8px; width:50%; text-align:center;">', $code_arr).'</td></tr>';
		fn_disable_live_editor_mode();
		$html = '<table style="height:100%; width:100%; font-size:20">';
		for($i=0; $i < $rows; $i++) $html.= $tr;
		$html.= '</table>';
        Pdf::render(array($html), $code);

		exit;
	}

} elseif ($mode == 'package_picker') {

    $params = $_REQUEST;
    $params['extend'] = array('description');
    $params['skip_view'] = 'Y';
    $params['package_picker'] = 'Y';


    list($products, $search) = fn_get_products($params, AREA == 'C' ? Registry::get('settings.Appearance.products_per_page') : Registry::get('settings.Appearance.admin_products_per_page'));

    if (!empty($_REQUEST['display']) || (AREA == 'C' && !defined('EVENT_OWNER'))) {
        fn_gather_additional_products_data($products, array('get_icon' => true, 'get_detailed' => true, 'get_options' => true, 'get_discounts' => true));
    }

    if (!empty($products)) {
        foreach ($products as $product_id => $product_data) {
            $products[$product_id]['options'] = fn_get_product_options($product_data['product_id'], DESCR_SL, true, false, true);
            if (!fn_allowed_for('ULTIMATE:FREE')) {
                $products[$product_id]['exceptions'] = fn_get_product_exceptions($product_data['product_id']);
                if (!empty($products[$product_id]['exceptions'])) {
                    foreach ($products[$product_id]['exceptions'] as $exceptions_data) {
                        $products[$product_id]['exception_combinations'][fn_get_options_combination($exceptions_data['combination'])] = '';
                    }
                }
            }
        }
    }

    Tygh::$app['view']->assign('products', $products);
    Tygh::$app['view']->assign('search', $search);

    if (isset($_REQUEST['company_id'])) {
        Tygh::$app['view']->assign('picker_selected_company', $_REQUEST['company_id']);
    }
    if (!empty($_REQUEST['company_ids'])) {
        Tygh::$app['view']->assign('picker_selected_companies', $_REQUEST['company_ids']);
    }

    Tygh::$app['view']->display('addons/my_product_packages/pickers/package_products/picker_contents.tpl');
    exit;

}
}// end of if (in_array($mode, array('add', 'update', 'delete', 'manage', 'print', 'package_picker')))  

?>