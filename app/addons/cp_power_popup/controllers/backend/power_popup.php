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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    fn_trusted_vars('popup_data');

    //
    // Delete popups
    //
    if ($mode == 'm_delete') {
        foreach ($_REQUEST['popup_ids'] as $v) {
            fn_cp_delete_power_popup($v);
        }

        $suffix = ".manage";
    }
    
    if ($mode == 'm_update') {
		if (!empty($_REQUEST['popup_data'])) {
			foreach ($_REQUEST['popup_data'] as $popup_id => $popup_data) {
				$popup_data['popup_id'] = $popup_id;
				fn_cp_update_popup($popup_id, $popup_data, DESCR_SL);
			}
		}

        $suffix = ".manage";
    }

    //
    // Add/update popup
    //
    if ($mode == 'update') {
        if (!empty($_REQUEST['popup_data'])) {
            $popup_id = fn_cp_update_popup($_REQUEST['popup_id'], $_REQUEST['popup_data'], DESCR_SL);
        }

        if (empty($popup_id)) {
            $suffix = ".manage";
        } else {
            $suffix = ".update?popup_id=$popup_id";
        }
    }

    return array(CONTROLLER_STATUS_OK, "power_popup$suffix");
}

if ($mode == 'update') {

    $popup_data = fn_cp_get_popup_data($_REQUEST['popup_id'], DESCR_SL);

    if (empty($popup_data)) {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }
    Registry::get('view')->assign('popup_data', $popup_data);

} elseif ($mode == 'manage' || $mode == 'picker') {

    list($popups, $search) = fn_cp_get_popups($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

    Registry::get('view')->assign('popups', $popups);
    Registry::get('view')->assign('search', $search);

} elseif ($mode == 'delete') {
    if (!empty($_REQUEST['popup_id'])) {
        fn_cp_delete_power_popup($_REQUEST['popup_id']);
    }

    return array(CONTROLLER_STATUS_REDIRECT, "power_popup.manage");
}