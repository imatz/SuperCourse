<?php
/*******************************
*                              *
*   (c) 2014 Cart-Power.com    *
*                              *
********************************/

use Tygh\Registry;
use Tygh\Languages\Languages;
use Tygh\BlockManager\Block;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

//
// Get slides
//
function fn_get_slides($params = array(), $lang_code = CART_LANGUAGE)
{

	$default_params = array(
		'items_per_page' => 0,
	);

	$params = array_merge($default_params, $params);

	$sortings = array(
		'position' => '?:slides.position',
		'timestamp' => '?:slides.timestamp',
		'name' => '?:slide_descriptions.slide',
	);

	$condition = $limit = '';

	if (!empty($params['limit'])) {
		$limit = db_quote(' LIMIT 0, ?i', $params['limit']);
	}

	$sorting = db_sort($params, $sortings, 'name', 'asc');

	$condition = (AREA == 'A') ? '' : " AND ?:slides.status = 'A' ";
	$condition .= fn_get_localizations_condition('?:slides.localization');
	$condition .= (AREA == 'A') ? '' : " AND (?:slides.type != 'G' OR ?:slide_images.slide_image_id IS NOT NULL) ";

	if (!empty($params['item_ids'])) {
		$condition .= db_quote(' AND ?:slides.slide_id IN (?n)', explode(',', $params['item_ids']));
	}

	if (!empty($params['period']) && $params['period'] != 'A') {
		list($params['time_from'], $params['time_to']) = fn_create_periods($params);
		$condition .= db_quote(" AND (?:slides.timestamp >= ?i AND ?:slides.timestamp <= ?i)", $params['time_from'], $params['time_to']);
	}

	fn_set_hook('get_slides', $params, $condition, $sorting, $limit, $lang_code);

	$fields = array (
		'?:slides.slide_id',
		'?:slides.type',
		'?:slides.target',
		'?:slides.status',
		'?:slides.position',
		'?:slide_videos.video_code',
		'?:slide_descriptions.title',
		'?:slide_descriptions.slide',
		'?:slide_descriptions.description',
		'?:slide_descriptions.url',
		'?:slide_images.slide_image_id',
	);

	if (fn_allowed_for('ULTIMATE')) {
		$fields[] = '?:slides.company_id';
	}

	$slides = db_get_array(
		"SELECT ?p FROM ?:slides " .
		"LEFT JOIN ?:slide_descriptions ON ?:slide_descriptions.slide_id = ?:slides.slide_id AND ?:slide_descriptions.lang_code = ?s" .
		"LEFT JOIN ?:slide_videos ON ?:slide_videos.slide_id = ?:slides.slide_id AND ?:slide_videos.lang_code = ?s" .
		"LEFT JOIN ?:slide_images ON ?:slide_images.slide_id = ?:slides.slide_id AND ?:slide_images.lang_code = ?s" .
		"WHERE 1 ?p ?p ?p",
		implode(", ", $fields), $lang_code, $lang_code, $lang_code, $condition, $sorting, $limit
	);

	foreach ($slides as $k => $v) {
		$slides[$k]['main_pair'] = fn_get_image_pairs($v['slide_image_id'], 'grslide', 'M', true, false, $lang_code);
		if ( !empty($slides[$k]['video_code']) && $slides[$k]['type'] === 'V' && stripos($slides[$k]['video_code'],'youtube') !== false ) {
			$slides[$k]['video_code'] = str_replace('<iframe', '<iframe id="slide_'.$slides[$k]['slide_id'].'" ', $slides[$k]['video_code']);
			preg_match('/src="([^"]+)"/', $slides[$k]['video_code'], $url);
			if ( stripos( $url[1],'?' ) !== false ) {
				$slides[$k]['video_code'] = str_replace( $url[1],$url[1].'&enablejsapi=1',$slides[$k]['video_code'] );
			} else {
				$slides[$k]['video_code'] = str_replace( $url[1],$url[1].'?enablejsapi=1',$slides[$k]['video_code'] );
			}
		}
		
		$data = db_get_field("SELECT settings FROM ?:slide_descriptions WHERE slide_id = ?i AND lang_code = ?s", $v['slide_id'], CART_LANGUAGE);
		$slides[$k]['settings']= unserialize($data);
		
	}

	fn_set_hook('get_slides_post', $slides, $params);

	return array($slides, $params);
}

//
// Get specific slide data
//
function fn_get_slide_data($slide_id, $lang_code = CART_LANGUAGE)
{
	$status_condition = (AREA == 'A') ? '' : " AND ?:slides.status IN ('A', 'H') ";

	$fields = array (
		'?:slides.slide_id',
		'?:slides.status',
		'?:slide_descriptions.slide',
		'?:slides.type',
		'?:slides.target',
		'?:slides.localization',
		'?:slides.timestamp',
		'?:slides.position',
		'?:slide_videos.video_code',
		'?:slide_descriptions.title',
		'?:slide_descriptions.description',
		'?:slide_descriptions.settings',
		'?:slide_descriptions.url',
		'?:slide_images.slide_image_id',
	);

	if (fn_allowed_for('ULTIMATE')) {
		$fields[] = '?:slides.company_id as company_id';
	}

	$slide = db_get_row(
		"SELECT ?p FROM ?:slides " .
		"LEFT JOIN ?:slide_descriptions ON ?:slide_descriptions.slide_id = ?:slides.slide_id AND ?:slide_descriptions.lang_code = ?s " .
		"LEFT JOIN ?:slide_images ON ?:slide_images.slide_id = ?:slides.slide_id AND ?:slide_images.lang_code = ?s" .
		"LEFT JOIN ?:slide_videos ON ?:slide_videos.slide_id = ?:slides.slide_id AND ?:slide_videos.lang_code = ?s" .
		"WHERE ?:slides.slide_id = ?i ?p",
		implode(", ", $fields), $lang_code, $lang_code, $lang_code, $slide_id, $status_condition
	);

	if (!empty($slide['settings'])) {
		$slide['settings'] = unserialize($slide['settings']);
	}

	if (!empty($slide)) {
		$slide['main_pair'] = fn_get_image_pairs($slide['slide_image_id'], 'grslide', 'M', true, false, $lang_code);
	}

	return $slide;
}

/**
* Hook for deleting store slides
*
* @param int $company_id Company id
*/
function fn_graceful_slider_delete_company(&$company_id)
{
	if (fn_allowed_for('ULTIMATE')) {
		$bannser_ids = db_get_fields("SELECT slide_id FROM ?:slides WHERE company_id = ?i", $company_id);

		foreach ($bannser_ids as $slide_id) {
			fn_delete_slide_by_id($slide_id);
		}
	}
}

/**
* Deletes slide and all related data
*
* @param int $slide_id Slide identificator
*/
function fn_delete_slide_by_id($slide_id)
{
	if (!empty($slide_id) && fn_check_company_id('slides', 'slide_id', $slide_id)) {
		db_query("DELETE FROM ?:slides WHERE slide_id = ?i", $slide_id);
		db_query("DELETE FROM ?:slide_descriptions WHERE slide_id = ?i", $slide_id);
		db_query("DELETE FROM ?:slide_videos WHERE slide_id = ?i", $slide_id);

		fn_set_hook('delete_slides', $slide_id);

		Block::instance()->removeDynamicObjectData('slides', $slide_id);

		$slide_images_ids = db_get_fields("SELECT slide_image_id FROM ?:slide_images WHERE slide_id = ?i", $slide_id);

		foreach ($slide_images_ids as $slide_image_id) {
			fn_delete_image_pairs($slide_image_id, 'grslide');
		}

		db_query("DELETE FROM ?:slide_images WHERE slide_id = ?i", $slide_id);
	}
}

function fn_graceful_slider_need_image_update()
{
	if (!empty($_REQUEST['file_slides_main_image_icon']) && array($_REQUEST['file_slides_main_image_icon'])) {
		$image_slide = reset ($_REQUEST['file_slides_main_image_icon']);

		if ($image_slide == 'slides_main') {
			return false;
		}
	}

	return true;
}

function fn_graceful_slider_update_slide($data, $slide_id, $lang_code = DESCR_SL)
{
	if (isset($data['timestamp'])) {
		$data['timestamp'] = fn_parse_date($data['timestamp']);
	}

	$data['localization'] = empty($data['localization']) ? '' : fn_implode_localizations($data['localization']);

	if (!empty($slide_id)) {
		db_query("UPDATE ?:slides SET ?u WHERE slide_id = ?i", $data, $slide_id);
		db_query("UPDATE ?:slide_descriptions SET ?u WHERE slide_id = ?i AND lang_code = ?s", $data, $slide_id, $lang_code);
		db_query("UPDATE ?:slide_videos SET ?u WHERE slide_id = ?i AND lang_code = ?s", $data, $slide_id, $lang_code);

		$slide_image_id = fn_get_slide_image_id($slide_id, $lang_code);
		$slide_image_exist = !empty($slide_image_id);
		$slide_is_multilang = Registry::get('addons.graceful_slider.slide_multilang') == 'Y';
		$image_is_update = fn_graceful_slider_need_image_update();

		if ($slide_is_multilang) {
			if ($slide_image_exist && $image_is_update) {
				fn_delete_image_pairs($slide_image_id, 'grslide');
				$slide_image_exist = false;
			}
		} else {
			if (isset($data['url'])) {
				db_query("UPDATE ?:slide_descriptions SET url = ?s WHERE slide_id = ?i", $data['url'], $slide_id);
			}
			if (isset($data['video_code'])) {
				db_query("UPDATE ?:slide_videos SET video_code = ?s WHERE slide_id = ?i", $data['video_code'], $slide_id);
			}
		}

		if ($image_is_update && !$slide_image_exist) {
			$slide_image_id = db_query("INSERT INTO ?:slide_images (slide_id, lang_code) VALUE(?i, ?s)", $slide_id, $lang_code);
		}
		$pair_data = fn_attach_image_pairs('slides_main', 'grslide', $slide_image_id, $lang_code);

		if (!$slide_is_multilang && !$slide_image_exist) {
			fn_graceful_slider_image_all_links($slide_id, $pair_data, $lang_code);
		}

	} else {
		$slide_id = $data['slide_id'] = db_query("REPLACE INTO ?:slides ?e", $data);

		foreach (Languages::getAll() as $data['lang_code'] => $v) {
			db_query("REPLACE INTO ?:slide_descriptions ?e", $data);
		}
		foreach (Languages::getAll() as $data['lang_code'] => $v) {
			db_query("REPLACE INTO ?:slide_videos ?e", $data);
		}

		if (fn_graceful_slider_need_image_update()) {
			$data_slide_image = array(
				'slide_id' => $slide_id,
				'lang_code' => $lang_code
			);

			$slide_image_id = db_query("INSERT INTO ?:slide_images ?e", $data_slide_image);
			$pair_data = fn_attach_image_pairs('slides_main', 'grslide', $slide_image_id, $lang_code);
			fn_graceful_slider_image_all_links($slide_id, $pair_data, $lang_code);
		}
	}

	return $slide_id;
}

function fn_graceful_slider_image_all_links($slide_id, $pair_data, $main_lang_code = DESCR_SL)
{
	if (!empty($pair_data)) {
		$pair_id = reset($pair_data);

		$lang_codes = Languages::getAll();
		unset($lang_codes[$main_lang_code]);

		foreach ($lang_codes as $lang_code => $lang_data) {
			$_slide_image_id = db_query("INSERT INTO ?:slide_images (slide_id, lang_code) VALUE(?i, ?s)", $slide_id, $lang_code);
			fn_add_image_link($_slide_image_id, $pair_id);
		}
	}
}

function fn_get_slide_image_id($slide_id, $lang_code = DESCR_SL)
{
	return db_get_field("SELECT slide_image_id FROM ?:slide_images WHERE slide_id = ?i AND lang_code = ?s", $slide_id, $lang_code);
}

//
// Get slide name
//
function fn_get_slide_name($slide_id, $lang_code = CART_LANGUAGE)
{
	if (!empty($slide_id)) {
		return db_get_field("SELECT slide FROM ?:slide_descriptions WHERE slide_id = ?i AND lang_code = ?s", $slide_id, $lang_code);
	}

	return false;
}

function fn_graceful_slider_delete_image_pre($image_id, $pair_id, $object_type)
{
	if ($object_type == 'grslide') {
		$slide_data = db_get_row("SELECT slide_id, slide_image_id FROM ?:slide_images INNER JOIN ?:images_links ON object_id = slide_image_id WHERE pair_id = ?i", $pair_id);

		if (Registry::get('addons.graceful_slider.slide_multilang') == 'Y') {

			if (!empty($slide_data['slide_image_id'])) {
				$lang_code = db_get_field("SELECT lang_code FROM ?:slide_images WHERE slide_image_id = ?i", $slide_data['slide_image_id']);

				db_query("DELETE FROM ?:common_descriptions WHERE object_id = ?i AND object_holder = 'images' AND lang_code = ?s", $image_id, $lang_code);
				db_query("DELETE FROM ?:slide_images WHERE slide_image_id = ?i", $slide_data['slide_image_id']);
			}

		} else {
			$slide_image_ids = db_get_fields("SELECT object_id FROM ?:images_links WHERE image_id = ?i AND object_type = 'grslide'", $image_id);

			if (!empty($slide_image_ids)) {
				db_query("DELETE FROM ?:slide_images WHERE slide_image_id IN (?a)", $slide_image_ids);
				db_query("DELETE FROM ?:images_links WHERE object_id IN (?a)", $slide_image_ids);
			}
		}
	}
}

function fn_graceful_slider_clone($slides, $lang_code)
{
	foreach ($slides as $slide) {
		if (empty($slide['main_pair']['pair_id'])) {
			continue;
		}

		$data_slide_image = array(
			'slide_id' => $slide['slide_id'],
			'lang_code' => $lang_code
		);
		$slide_image_id = db_query("REPLACE INTO ?:slide_images ?e", $data_slide_image);
		fn_add_image_link($slide_image_id, $slide['main_pair']['pair_id']);
	}
}

function fn_graceful_slider_update_language_post($language_data, $lang_id, $action)
{
	if ($action == 'add') {
		list($slides) = fn_get_slides(array(), DEFAULT_LANGUAGE);
		fn_graceful_slider_clone($slides, $language_data['lang_code']);
	}
}

function fn_graceful_slider_delete_languages_post($lang_ids, $lang_codes, $deleted_lang_codes)
{
	foreach ($deleted_lang_codes as $lang_code) {
		list($slides) = fn_get_slides(array(), $lang_code);

		foreach ($slides as $slide) {
			if (empty($slide['main_pair']['pair_id'])) {
				continue;
			}
			fn_delete_image($slide['main_pair']['image_id'], $slide['main_pair']['pair_id'], 'grslide');
		}
	}
}

if (fn_allowed_for('ULTIMATE')) {
	function fn_graceful_slider_ult_check_store_permission($params, &$object_type, &$object_name, &$table, &$key, &$key_id)
	{
		if (Registry::get('runtime.controller') == 'slides' && !empty($params['slide_id'])) {
			$key = 'slide_id';
			$key_id = $params[$key];
			$table = 'slides';
			$object_name = fn_get_slide_name($key_id, DESCR_SL);
			$object_type = __('slide');
		}
	}
}
