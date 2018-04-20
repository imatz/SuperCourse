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

if ($mode == 'update') {

    // uelv mono thl kai afm
	$parathrhseis_id=fn_my_users_get_setting_field('s_delivery_notes');
	$params['field_id']=$parathrhseis_id;
	$parathrhseis_field = fn_get_profile_fields('S', array(), CART_LANGUAGE, $params);
	
    $profile_fields = array('S'=>array($parathrhseis_id=>$parathrhseis_field));

    Tygh::$app['view']->assign('profile_fields', $profile_fields);
}