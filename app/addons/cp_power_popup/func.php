<?php
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }


function fn_cp_get_popups($params, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);

    $fields = array (
        '?:cp_popups.*',
        '?:cp_popup_descriptions.*'
    );

    // Define sort fields
    $sortings = array (
        'priority' => '?:cp_popups.priority',
        'name' => '?:cp_popup_descriptions.name',
        'status' => '?:cp_popups.status',
        'stop_other' => '?:cp_popups.stop_other',
        'not_closable' => '?:cp_popups.not_closable'
    );

    $limit = $condition = '';

    $join = db_quote(" LEFT JOIN ?:cp_popup_descriptions ON ?:cp_popup_descriptions.popup_id = ?:cp_popups.popup_id AND ?:cp_popup_descriptions.lang_code = ?s", $lang_code);

    $condition .= (AREA == 'A') ? '1 ' : " ?:cp_popups.status = 'A'";

    // Get additional information about companies
    if (fn_allowed_for('ULTIMATE')) {
        $fields[] = ' ?:companies.company as company';
        $sortings['company'] = 'company';
        $join .= db_quote(" LEFT JOIN ?:companies ON ?:companies.company_id = ?:cp_popups.company_id");

        $company_id = Registry::get('runtime.company_id');
        $condition .= fn_get_company_condition('?:companies.company_id', true, $company_id);
    }

   
    if (!empty($params['item_ids'])) {
        $condition .= db_quote(' AND ?:cp_popups.popup_id IN (?n)', explode(',', $params['item_ids']));
    }

    $limit = '';
    if (!empty($params['limit'])) {
        $limit = db_quote(" LIMIT 0, ?i", $params['limit']);

    } elseif (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(?:cp_popups.popup_id) FROM ?:cp_popups ?p WHERE ?p", $join, $condition);
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $sorting = db_sort($params, $sortings, 'priority', 'desc');

    $fields = join(', ', $fields);
    $popups = db_get_array("SELECT ?p FROM ?:cp_popups ?p WHERE ?p ?p ?p", $fields, $join, $condition, $sorting, $limit);

    return array($popups, $params);
}

//
// Get specific popup data
//
function fn_cp_get_popup_data($popup_id, $lang_code = CART_LANGUAGE)
{
	if (empty($popup_id)) {
		return false;
	}
	
    $fields = array (
        '?:cp_popups.*',
        '?:cp_popup_descriptions.*',
    );

    $join = '';
    $condition = (AREA == 'A') ? '' : " AND ?:cp_popups.status = 'A' ";

    $popup = db_get_row(
        "SELECT " . implode(', ', $fields) . " FROM ?:cp_popups "
        . "LEFT JOIN ?:cp_popup_descriptions ON ?:cp_popup_descriptions.popup_id = ?:cp_popups.popup_id "
        . "AND ?:cp_popup_descriptions.lang_code = ?s ?p "
        . "WHERE ?:cp_popups.popup_id = ?i ?p",
        $lang_code, $join, $popup_id, $condition
    );
    
    $linked_products = db_get_fields('SELECT link_object_id FROM ?:cp_popup_links WHERE popup_id=?i AND link_type=?s', $popup_id, 'P');
    if (!empty($linked_products)) {
		$popup['products'] = implode(',', $linked_products);
    }
    
	$linked_categories = db_get_fields('SELECT link_object_id FROM ?:cp_popup_links WHERE popup_id=?i AND link_type=?s', $popup_id, 'C');
 	if (!empty($linked_categories)) {
		$popup['categories'] = implode(',', $linked_categories);
    }
	
	if ($popup['content_type'] == 'P' && !empty($popup['page_id'])) {
		 $popup['page_content'] = fn_get_page_data($popup['page_id']);
	}
	
    return $popup;
}

function fn_cp_update_popup($popup_id, $popup_data, $lang_code = CART_LANGUAGE)
{    
    if (!empty($popup_id) && !fn_check_company_id('cp_popups', 'popup_id', $popup_id)) {
        fn_company_access_denied_notification();

        return false;
    }

    $_data = $popup_data;
	
	if (isset($popup_data['usergroup_ids'])) {
        $_data['usergroup_ids'] = empty($popup_data['usergroup_ids']) ? '0' : implode(',', $_data['usergroup_ids']);
    }
	
    $_data['from_date'] = !empty($_data['from_date']) ? fn_parse_date($_data['from_date']) : 0;
    $_data['to_date'] = !empty($_data['to_date']) ? fn_parse_date($_data['to_date'], true) : 0;

    if (!empty($_data['to_date']) && $_data['to_date'] < $_data['from_date']) { // protection from incorrect date range (special for isergi :))
        $_data['from_date'] = fn_parse_date($_data['to_date']);
        $_data['to_date'] = fn_parse_date($_data['from_date'], true);
    }

    if (empty($popup_id)) {
        $create = true;

        $popup_id = $_data['popup_id'] = db_query("REPLACE INTO ?:cp_popups ?e", $_data);

        if (empty($popup_id)) {
            return false;
        }

        // Adding descriptions
        foreach (fn_get_translation_languages() as $_data['lang_code'] => $v) {
            db_query("INSERT INTO ?:cp_popup_descriptions ?e", $_data);
        }

    } else {
        $create = false;

        db_query("UPDATE ?:cp_popups SET ?u WHERE popup_id = ?i", $_data, $popup_id);
        // update popups descriptions
        $_data = $popup_data;
        db_query("UPDATE ?:cp_popup_descriptions SET ?u WHERE popup_id = ?i AND lang_code = ?s", $_data, $popup_id, $lang_code);
    }
    
	db_query('DELETE FROM ?:cp_popup_links WHERE popup_id=?i', $popup_id);
	
	$__data = array();
    if (!empty($popup_data['categories'])) {
		$link_objects = explode(',', $popup_data['categories']);
		$__data['link_type'] = 'C';
		$__data['popup_id'] = $popup_id;
		
		foreach ($link_objects as $link_object_id) {
			$__data['link_object_id'] = $link_object_id;
			db_query('REPLACE INTO ?:cp_popup_links ?e', $__data);
		}
    }
    if (!empty($popup_data['products'])) {
		$link_objects = explode(',', $popup_data['products']);
		$__data['link_type'] = 'P';
		$__data['popup_id'] = $popup_id;
		
		foreach ($link_objects as $link_object_id) {
			$__data['link_object_id'] = $link_object_id;
			db_query('REPLACE INTO ?:cp_popup_links ?e', $__data);
		}
    }
    
    // Log popups update/add
    fn_log_event('cp_popups', !empty($create) ? 'create' : 'update', array(
        'popup_id' => $popup_id,
    ));

    return $popup_id;
}

function fn_cp_delete_power_popup($popup_id)
{
    $popup_deleted = false;

    if (!empty($popup_id)) {
        if (fn_check_company_id('cp_popups', 'popup_id', $popup_id)) {

            // Log popup deletion
            fn_log_event('cp_popups', 'delete', array(
                'popup_id' => $popup_id,
            ));

            $affected_rows = db_query("DELETE FROM ?:cp_popups WHERE popup_id = ?i", $popup_id);

            db_query("DELETE FROM ?:cp_popup_descriptions WHERE popup_id = ?i", $popup_id);

            if ($affected_rows != 0) {
                $popup_deleted = true;
            } else {
                fn_set_notification('E', __('error'), __('object_not_found', array('[object]' => __('popups'))),'','404');
            }

        } else {
            fn_company_access_denied_notification();
        }
    }

    return $popup_deleted;
}

function fn_cp_get_popup_section($controller, $mode, $params) {
	
	if ($controller == 'index' && $mode == 'index') {
		return 'H';
	} elseif ($controller == 'products' && $mode == 'view') {
		return 'P';
	} elseif ($controller == 'products' && $mode == 'search') {
		return 'S';
	} elseif ($controller == 'pages' && $mode == 'view') {
		return 'W';
	} elseif ($controller == 'categories' && $mode == 'view') {
		return 'C';
	} elseif ($controller == 'checkout' && $mode == 'cart') {
		return 'B';
	} elseif ($controller == 'checkout' && $mode == 'checkout') {
		return 'Z';
	} elseif ($controller == 'checkout' && $mode == 'complete') {
		return 'O';
	} 
	
	return 'U';
}

function fn_cp_get_available_popup($controller, $mode, $params) {
	$section = fn_cp_get_popup_section($controller, $mode, $params);
	$popups = array();
    
	$section_array = array('A', $section);
    $condition = $join = '';
    $auth = $_SESSION['auth'];
    
    if (AREA == 'C') {
        $condition .= " AND (" . fn_find_array_in_set($auth['usergroup_ids'], '?:cp_popups.usergroup_ids', true) . ")";
    }
    
    // Get additional information about companies
    if (fn_allowed_for('ULTIMATE')) {
        $join .= db_quote(" LEFT JOIN ?:companies ON ?:companies.company_id = ?:cp_popups.company_id");

        $company_id = Registry::get('runtime.company_id');
        $condition .= fn_get_company_condition('?:companies.company_id', true, $company_id);
    }
    
	$popup = db_get_row('SELECT ?:cp_popups.* FROM ?:cp_popups ?p WHERE ?:cp_popups.section IN (?a) AND ?:cp_popups.status=?s ?p ORDER BY ?:cp_popups.priority DESC limit 0,1', $join, $section_array, 'A', $condition);

	if (!empty($popup)) {
       $popup_section = $popup['section'];
		if ($section == 'U') {
            if (empty($popup['dispatch']) && $popup_section == 'A') {
                if ($popup['from_date'] > 0 && $popup['to_date'] > 0 && time() >= $popup['from_date'] && time() < $popup['to_date']) {
                    $popups[$popup['popup_id']] = $popup;
                } elseif ($popup['from_date'] == 0 && $popup['to_date'] == 0) {
                    $popups[$popup['popup_id']] = $popup;
                }
            } else {
                if (strpos($popup['dispatch'], '?') !== false) {
                    list($dispatch, $params_list) = explode('?', $popup['dispatch']);
                } else {
                    $dispatch = $popup['dispatch'];
                }
        
                if (!empty($dispatch) && strpos($dispatch, '.') !== false) {
                    list($_controller, $_mode) = explode('.', $dispatch);
                    if ($controller == $_controller && $mode == $_mode) {
                        if ($popup['from_date'] > 0 && $popup['to_date'] > 0 && time() >= $popup['from_date'] && time() < $popup['to_date']) {
                            $popups[$popup['popup_id']] = $popup;
                        } elseif ($popup['from_date'] == 0 && $popup['to_date'] == 0) {
                            $popups[$popup['popup_id']] = $popup;
                        }
                    }
                }
            }
 		} else {
			if ($popup['from_date'] > 0 && $popup['to_date'] > 0 && time() >= $popup['from_date'] && time() < $popup['to_date']) {
				$popups[$popup['popup_id']] = $popup;
			}  elseif ($popup['from_date'] == 0 && $popup['to_date'] == 0) {
				$popups[$popup['popup_id']] = $popup;
			}
 		}
	} 
	
	//check if there is special popup for product or category
	$object_id = 0;
	if ($section == 'P') {
		$object_id = $params['product_id'];
	} elseif ($section == 'C') {
		$object_id = $params['category_id'];
	}
	if (!empty($object_id)) {
		$popup = db_get_row('SELECT ?:cp_popups.* FROM ?:cp_popups LEFT JOIN ?:cp_popup_links ON ?:cp_popups.popup_id=?:cp_popup_links.popup_id ?p WHERE ?:cp_popup_links.link_object_id=?i AND ?:cp_popup_links.link_type=?s AND ?:cp_popups.status=?s ?p ORDER BY ?:cp_popups.priority DESC limit 0,1', $join, $object_id, $section, 'A', $condition);
		if (!empty($popup)) {
			if ($popup['from_date'] > 0 && $popup['to_date'] > 0 && time() >= $popup['from_date'] && time() < $popup['to_date']) {
				$popups[$popup['popup_id']] = $popup;
			} elseif ($popup['from_date'] == 0 && $popup['to_date'] == 0) {
				$popups[$popup['popup_id']] = $popup;
			}
		}
	}

	if (!empty($popups)) {
		$max_priority = 0;
		//$stop_other = false;
		foreach ($popups as $k => $_popup) {
			if ($_popup['stop_other'] == 'Y') {
				$stop_other_array[$k] = $_popup;
			} else {
				if ($_popup['priority'] >= $max_priority) {
					$display_popup_id = $_popup['popup_id'];
					$max_priority = $_popup['priority'];
				}
			}
		}
		
		if (!empty($stop_other_array)) {
			foreach ($popups as $k => $_popup) {
				if ($_popup['priority'] >= $max_priority) {
					$display_popup_id = $_popup['popup_id'];
					$max_priority = $_popup['priority'];
				}
			}
		}
		
		$display_popup = fn_cp_get_popup_data($display_popup_id);
		return $display_popup;
	} else {
		return array();
	}
}