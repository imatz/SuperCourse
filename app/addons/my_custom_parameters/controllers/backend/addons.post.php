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
use Tygh\Settings;
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    fn_trusted_vars (
        'addon_data'
    );

    if ($mode == 'update') {
        if (isset($_REQUEST['addon_data']) && 'my_custom_parameters'==$_REQUEST['addon']) {
			
			fn_my_custom_parameters_apply_parameters();
			return array(CONTROLLER_STATUS_OK, 'addons.update?addon=' . $_REQUEST['addon']);
        }		
    } 
}
