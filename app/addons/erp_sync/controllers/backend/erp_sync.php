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
if (!defined('BOOTSTRAP')) { die('Access denied'); }if ($_SERVER['REQUEST_METHOD'] == 'POST') {    if ($mode == 'clear') {    if (!empty($_REQUEST['modules'])) {      $queue = fn_erp_sync_clear_modules($_REQUEST['modules']);      fn_set_notification('I', __('file_update_sequence'), '<ol><li>'.implode('</li><li>',$queue)).'</li></ol>';    } else {      fn_set_notification('E', __('error'), __('no_modules_selected'));    }        return array(CONTROLLER_STATUS_OK, 'erp_sync.clear');  }
}
if ($mode == 'activity') {
	$data=fn_erp_sync_get_activity_status();
	Registry::get('view')->assign('activity',$data);
} else if ($mode == 'reset') {
	$data=fn_erp_sync_reset_activity_status();
	return array(CONTROLLER_STATUS_REDIRECT,'erp_sync.activity');
} else if ($mode == 'clear') {    $data=fn_erp_sync_get_reset_modules();	Registry::get('view')->assign('items',$data);}

