<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tygh\Pdf;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (empty($auth['user_id']) || ($auth['account_type']!='B'))
	return array(CONTROLLER_STATUS_DENIED);
	
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	fn_add_breadcrumb(__('upload_product_list'), 'products_list.view');	
	
    if ($mode == 'upload') { 
		fn_add_breadcrumb(__('upload_product_list'));
		
		$files = fn_filter_uploaded_data('products_list_file', array('csv', 'xls', 'xlsx', 'ods'));
		
		if (empty($files)) {
			fn_set_notification('W', __('warning'), __('no_file_selected'));
			return array(CONTROLLER_STATUS_REDIRECT, 'products_list.view');
		} else {
			$ext_original = fn_get_file_ext($files[0]['name']);
			$file_tmp = $files[0]['path'];
			$file = substr($file_tmp, 0, -4) . $ext_original;
			fn_rename($file_tmp,$file);
			try {
				$spreadsheet = IOFactory::load($file);
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				if ($sheetData[1]['B'] == __('Products_list_column_amount'))
					unset($sheetData[1]);
				
				$error = false;
				
				list($product_list, $product_data) = fn_check_product_list($sheetData);
				
				Tygh::$app['view']->assign('product_list', $product_list);

				foreach ($product_list as $pl) {
					if (!empty($pl['error'])) {
						$error = true;
						break;
					}
				}
				
				if ($error) {
					fn_set_notification('E', __('error'), __('products_list_contains_errors'));
				} else if (empty($product_data)) {
					fn_set_notification('W', __('warning'), __('products_list_empty'));
					return array(CONTROLLER_STATUS_REDIRECT, 'products_list.view');
				} else {
					fn_add_to_cart_product_list($product_data, $auth);
					return array(CONTROLLER_STATUS_OK, 'products_list.view');
				}
				
			} catch (InvalidArgumentException $e) {
				fn_set_notification('E', __('error'), __('products_list_file_error'));
				return array(CONTROLLER_STATUS_REDIRECT, 'products_list.view');
			}
			
			fn_rm($file);
		}
		
		return array(CONTROLLER_STATUS_OK);
	} else if ($mode == 'manual') {
			fn_add_breadcrumb(__('manual_product_list'));
			
			$error = false;
			
			list($product_list, $product_data) = fn_check_product_list($_REQUEST['product_list']);
			
			Tygh::$app['view']->assign('product_list', $product_list);
			
			foreach ($product_list as $pl) {
				if (!empty($pl['error'])) {
					$error = true;
					break;
				}
			}
			
			if ($error) {
				fn_set_notification('E', __('error'), __('products_list_contains_errors'));
			} else if (empty($product_data)) {
				fn_set_notification('W', __('warning'), __('products_list_empty'));
				return array(CONTROLLER_STATUS_REDIRECT, 'products_list.view');
			} else {
				fn_add_to_cart_product_list($product_data, $auth);
				return array(CONTROLLER_STATUS_OK, 'products_list.view');
			}
		
			return array(CONTROLLER_STATUS_OK);
	
	} else if ($mode == 'confirm') {
		fn_add_breadcrumb(__('confirm_product_list'));
	
		$error = false;
		
		list($product_list, $product_data) = fn_check_product_list($_REQUEST['product_list']);
		
		Tygh::$app['view']->assign('product_list', $product_list);
		
		foreach ($product_list as $pl) {
			if (!empty($pl['error'])) {
				$error = true;
				break;
			}
		}
		
		if ($error) {
			fn_set_notification('E', __('error'), __('products_list_contains_errors'));
		} else if (empty($product_data)) {
			fn_set_notification('W', __('warning'), __('products_list_empty'));
			return array(CONTROLLER_STATUS_REDIRECT, 'products_list.view');
		} else {
			fn_add_to_cart_product_list($product_data, $auth);
			return array(CONTROLLER_STATUS_OK, 'products_list.view');
		}
		
		return array(CONTROLLER_STATUS_OK);
		
	} else if ($mode == 'print_err') {
		list($product_list, $product_data) = fn_check_product_list($_REQUEST['error_list']);
	
		$html = '<style>#product_list_errors td{text-align:center;border-bottom:1px solid}</style><table id="product_list_errors" style="width:100%; font-size:20">';
		$html .= '<thead>
					<tr>
						<th>'.__("code").'</th>
						<th>'.__("product").'</th>
						<th>'.__("quantity").'</th>
						<th>'.__("error").'</th>
					</tr>
				</thead>
				<tbody>';
		foreach ($product_list as $pl) {
			if (!empty($pl['error'])) {
				$html .= '<tr>
					<td>'.$pl['A'].'</td>
					<td>'.$pl['product'].'</td>
					<td>'.$pl['B'].'</td>
					<td>'.$pl['error'].'</td>
				</tr>';
			}
		}
		$html.= '</tbody></table>';
        Pdf::render(array($html), 'list_errors');
	}
	
} else if ($mode == 'csv' || $mode == 'xls' || $mode == 'csv_demo' || $mode == 'xls_demo') {
	
	$spreadsheet = new Spreadsheet();
	
	$spreadsheet->getProperties()->setCreator('SuperCourse ELT')
		->setLastModifiedBy('SuperCourse ELT')
		->setTitle('Product List');
	
	$spreadsheet->setActiveSheetIndex(0);
	$spreadsheet->getActiveSheet()->setCellValue('A1', __('Products_list_column_code'));
	
	if ($mode == 'csv' || $mode == 'xls') {
		$spreadsheet->getActiveSheet()->setCellValue('B1', __('Products_list_column_product'));
	} else {
		$spreadsheet->getActiveSheet()->setCellValue('B1', __('Products_list_column_amount'));
	}
	//$spreadsheet->getActiveSheet()->setCellValue('C1', __('Products_list_column_price'));
	//$spreadsheet->getActiveSheet()->setCellValue('D1', __('Products_list_column_quantity'));
	
	$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	//$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	
	$i=1;
	
	$file = fn_create_temp_file();	
	$filename = 'Supercourse_list';
	if ($mode == 'csv_demo' || $mode == 'xls_demo') {
		$filename.='_demo';
	}
	
	
	if ($mode == 'csv' || $mode == 'xls') {
		$products = fn_get_products(array());
		$products = $products[0];
		//fn_my_product_packages_get_retail_data($products);
		
		foreach($products as $product) {
			$i++;
			$spreadsheet->getActiveSheet()->setCellValue('A' . $i, $product['product_code'])
				->setCellValue('B' . $i, $product['product']);
			//	->setCellValue('C' . $i, fn_format_price($product['retail_data']['price']));
		}	
		
	} else { // demo
		// dummy data
		$spreadsheet->getActiveSheet()->setCellValue('A2', 'SIB1M07')
				->setCellValue('B2', 1);
		$spreadsheet->getActiveSheet()->setCellValue('A3', 'SB1M01')
				->setCellValue('B3', 1);
		$spreadsheet->getActiveSheet()->setCellValue('A4', 'CEDMB101')
				->setCellValue('B4', 2);
	}
	
	if ($mode == 'csv' || $mode == 'csv_demo') {
		$writer = IOFactory::createWriter($spreadsheet, 'Csv')->setDelimiter(',')
			->setEnclosure('"')
			->setSheetIndex(0);
		
		$filename.='.csv';
		
	} else { // xls
		$writer = IOFactory::createWriter($spreadsheet, 'Xls');
	
		$filename.='.xls';
	
	}
	
	$writer->save($file);
	
	fn_get_file($file, $filename, true);
	
	exit;
	
} else if ($mode == 'view') {
	fn_add_breadcrumb(__('upload_product_list'));	
} else if ($mode == 'download') {
	fn_add_breadcrumb(__('download_product_list'));	
}