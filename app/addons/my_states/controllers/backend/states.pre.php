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
	//
    // Updating existing states
    //
    if ($mode == 'm_update') {
        foreach ($_REQUEST['states'] as $key => $_data) {
            if (!empty($_data)) {
                fn_my_states_update_state($_data, $key, DESCR_SL);
            }
        }
    }

    return array(CONTROLLER_STATUS_OK, 'states.manage?country_code=' . $_REQUEST['country_code']);
}
