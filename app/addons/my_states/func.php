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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

// einai klvnos ths fn_update_state (./app/controllers/backend/states.php line 85) alla kanei update kai ston pinaka states 
function fn_my_states_update_state($state_data, $state_id = 0, $lang_code = DESCR_SL)
{
    if (empty($state_id)) {
        if (!empty($state_data['code']) && !empty($state_data['state'])) {
            $state_data['state_id'] = $state_id = db_query("REPLACE INTO ?:states ?e", $state_data);

            foreach (fn_get_translation_languages() as $state_data['lang_code'] => $_v) {
                db_query('REPLACE INTO ?:state_descriptions ?e', $state_data);
            }
        }
    } else {
		unset($state_data['state_id']);
		db_query("UPDATE ?:states SET ?u WHERE state_id = ?i", $state_data, $state_id);
        db_query("UPDATE ?:state_descriptions SET ?u WHERE state_id = ?i AND lang_code = ?s", $state_data, $state_id, $lang_code);
    }

    return $state_id;

}

// einai klvnos ths fn_get_states (.app/functions/fn.locations.php) alla fernei kai to cmp
function fn_my_states_get_states($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    // Set default values to input params
    $default_params = array (
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);

    $fields = array(
        'a.state_id',
        'a.country_code',
        'a.code',
        'a.status',
        'a.cmp',
        'b.state',
        'c.country'
    );

    $condition = '1';
    if (!empty($params['only_avail'])) {
        $condition .= db_quote(" AND a.status = ?s", 'A');
    }

    if (!empty($params['q'])) {
        $condition .= db_quote(" AND b.state LIKE ?l", '%' . $params['q'] . '%');
    }

    if (!empty($params['country_code'])) {
        $condition .= db_quote(" AND a.country_code = ?s", $params['country_code']);
    }

    $limit = '';
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT count(*) FROM ?:states as a WHERE ?p", $condition);
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $states = db_get_array(
        "SELECT " . implode(', ', $fields) . " FROM ?:states as a " .
        "LEFT JOIN ?:state_descriptions as b ON b.state_id = a.state_id AND b.lang_code = ?s " .
        "LEFT JOIN ?:country_descriptions as c ON c.code = a.country_code AND c.lang_code = ?s " .
        "WHERE ?p ORDER BY c.country, b.state $limit",
    $lang_code, $lang_code, $condition);

    return array($states, $params);
}