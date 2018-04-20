<?php
/*******************************
*                              *
*   (c) 2014 Cart-Power.com    *
*                              *
********************************/

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

	fn_trusted_vars('slides', 'slide_data');
	$suffix = '';

	//
	// Delete slides
	//
	if ($mode == 'm_delete') {
		foreach ($_REQUEST['slide_ids'] as $v) {
			fn_delete_slide_by_id($v);
		}

		$suffix = '.manage';
	}

	//
	// Add/edit slides
	//
	if ($mode == 'update') {
// 		$slide_id = fn_graceful_slider_update_slide($_REQUEST['slide_data'], $_REQUEST['slide_id'], DESCR_SL);
// 
// 		$suffix = ".update?slide_id=$slide_id";


//         foreach ($_REQUEST['slide_data']['text'] as $k => $v) {
//         
// 			$_REQUEST['slide_data']['text'][$k] = array_diff($v, array(''));
// 
// 			if ( empty($_REQUEST['slide_data']['text'][$k]) ) {
// 				unset($_REQUEST['slide_data']['text'][$k]);
// 			}
// 			
// 		}
	
		$_REQUEST['slide_data']['settings'] = serialize($_REQUEST['slide_data']['settings']);
	
        $slide_id = fn_graceful_slider_update_slide($_REQUEST['slide_data'], $_REQUEST['slide_id'], DESCR_SL);

        $suffix = ".update?slide_id=$slide_id";



		
		
	}

	return array(CONTROLLER_STATUS_OK, "slides$suffix");
}

if ($mode == 'update') {
	$slide = fn_get_slide_data($_REQUEST['slide_id'], DESCR_SL);

	if (empty($slide)) {
		return array(CONTROLLER_STATUS_NO_PAGE);
	}

	Registry::set('navigation.tabs', array (
		'general' => array (
			'title' => __('general'),
			'js' => true
		),
	));

	Registry::get('view')->assign('slide', $slide);

} elseif ($mode == 'manage' || $mode == 'picker') {

	list($slides, ) = fn_get_slides(array(), DESCR_SL);

	Registry::get('view')->assign('slides', $slides);

} elseif ($mode == 'delete') {
	if (!empty($_REQUEST['slide_id'])) {
		fn_delete_slide_by_id($_REQUEST['slide_id']);
	}

	return array(CONTROLLER_STATUS_REDIRECT, "slides.manage");
}

//
// Categories picker
//
if ($mode == 'picker') {
	Registry::get('view')->display('addons/graceful_slider/pickers/slides/picker_contents.tpl');
	exit;
}
