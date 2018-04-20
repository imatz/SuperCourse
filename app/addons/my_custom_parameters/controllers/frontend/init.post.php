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

use Tygh\Registry;

$login_prompt=false;
//allow login && registration
if ('erp_sync'!=$controller && !('auth'==$controller && ('login_form'==$mode || 'login'==$mode || 'recover_password'==$mode)) && !('profiles'==$controller && ('retail'==$mode || 'add'==$mode || 'register'==$mode|| 'success_register'==$mode))) {
	$retail_enabled=fn_my_custom_parameters_get_setting_field('retail_enabled');
	Tygh::$app['view']->assign('retail_enabled',$retail_enabled);
	
	//// an den einai login h den epitrepetai lianikh eite den exei pathsei "eimai pelaths lianikhs" steilton arxikh
//	if(empty($_SESSION['auth']['user_id']) && (empty($_SESSION['retail_customer']) || $retail_enabled !='Y')) {
//		if($controller=='index') $login_prompt=true;
//		else return array(CONTROLLER_STATUS_REDIRECT,'index.php');
//	}
}

// an einai logged in h kai den exei epilejei ypokatasthma steilton na eplejei
if(!empty($_SESSION['auth']['user_id']) && empty($_SESSION['auth']['profile_id'])) {
  if(!($controller=='profiles' && $mode=='choose')) return array(CONTROLLER_STATUS_REDIRECT,'profiles.choose');
}

Tygh::$app['view']->assign('login_prompt',$login_prompt);
Tygh::$app['view']->assign('a_discount_display', fn_my_custom_parameters_get_setting_field('a_discount_display'));