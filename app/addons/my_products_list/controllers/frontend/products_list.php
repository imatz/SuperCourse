<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (empty($auth['user_id']) || ($auth['account_type']!='B'))
	return array(CONTROLLER_STATUS_DENIED);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
    if ($mode == 'upload') { 
		$files = fn_filter_uploaded_data('products_list_file');
		fn_print_r($files);
		
		return array(CONTROLLER_STATUS_OK, 'products_list.view');
	}
	
} elseif ($mode == 'csv' || $mode == 'xls' ) {
	$products = fn_get_products(array());
	
	$spreadsheet = new Spreadsheet();
	
	$spreadsheet->getProperties()->setCreator('SuperCourse ELT')
		->setLastModifiedBy('SuperCourse ELT')
		->setTitle('Product List');
	
	$spreadsheet->setActiveSheetIndex(0);
	$spreadsheet->getActiveSheet()->setCellValue('A1', 'Βιβλίο');
	$spreadsheet->getActiveSheet()->setCellValue('B1', 'Κωδικός');
	$spreadsheet->getActiveSheet()->setCellValue('C1', 'Τιμή');
	$spreadsheet->getActiveSheet()->setCellValue('D1', 'Ποσότητα');
	
	$i=1;
	
	$file = fn_create_temp_file();	
	$filename = 'Supercourse_list';
	
	foreach($products[0] as $product) {
		$i++;
		$spreadsheet->getActiveSheet()->setCellValue('A' . $i, $product['product'])
			->setCellValue('B' . $i, $product['product_code'])
			->setCellValue('C' . $i, $product['price']);
	}	
	
	if ($mode == 'csv' ) {
		$writer = IOFactory::createWriter($spreadsheet, 'Csv')->setDelimiter(',')
			->setEnclosure('"')
			->setSheetIndex(0);
		//$file.='.csv';
		$filename.='.csv';
		
	} else { // xls
		$writer = IOFactory::createWriter($spreadsheet, 'Xls');
	
		//$file.='.xls';
		$filename.='.xls';
	
	}
	
	$writer->save($file);
	
	fn_get_file($file, $filename, true);
	
	exit;
}