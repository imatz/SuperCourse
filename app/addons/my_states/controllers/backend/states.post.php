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

if ($mode == 'manage') {

    $params = $_REQUEST;
    if (empty($params['country_code'])) {
        $params['country_code'] = Registry::get('settings.General.default_country');
    }

    list($states, $search) = fn_my_states_get_states($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

    Tygh::$app['view']->assign('states', $states);

}