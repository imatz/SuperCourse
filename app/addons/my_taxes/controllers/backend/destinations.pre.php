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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$_REQUEST['destination_id'] = empty($_REQUEST['destination_id']) ? 0 : $_REQUEST['destination_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //cannot update/delete reserved destinations (pos1,pos2....5)

    if (($mode == 'update' || $mode == 'delete') && !empty($_REQUEST['destination_id']) && in_array($_REQUEST['destination_id'],fn_my_taxes_get_destination_ids())) { 
		fn_set_notification('W', __('warning'), __('cannot_alter_reserved_destination'));
		return array(CONTROLLER_STATUS_REDIRECT, 'destinations.manage');
    }
	
    if ($mode == 'm_delete') {
		
		$reserved_ids=fn_my_taxes_get_destination_ids();
		
        if (!empty($_REQUEST['destination_ids'])) {
            foreach($_REQUEST['destination_ids'] as $no => $id) {
				if (in_array($id,$reserved_ids)){
					unset($_REQUEST['destination_ids'][$no]);
					fn_set_notification('W', __('warning'), __('cannot_alter_reserved_destination'));
				}
			}
        }
    }

}
