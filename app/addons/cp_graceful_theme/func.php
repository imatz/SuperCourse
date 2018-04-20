<?php


if ( !defined('BOOTSTRAP') ) { die('Access denied'); }

function fn_settings_variants_addons_cp_graceful_theme_rtl_settings()
{
	$lang_data = db_get_array("SELECT lang_code, name FROM ?:languages");
        $result = array();
        foreach ($lang_data as $k => $v)
        {
		$result[$v['lang_code']] = $v['name'].' ('.$v['lang_code'].')';
	}
        return $result;
}

function fn_get_image_for_menu($href = '') {

    $category_id = explode('category_id=', $href);
    
    if (!isset($category_id[1])) {
        return true;
    }
    
    $category_id = $category_id[1];
    
    $main_pair = fn_get_image_pairs($category_id, 'category', 'M', true, true, CART_LANGUAGE);
    
    return $main_pair;
}

?>