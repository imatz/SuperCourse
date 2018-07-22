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
use Tygh\Session;
use Tygh\Mailer;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'update') {
        
        if (empty($auth['user_id'])) {
			return array(CONTROLLER_STATUS_REDIRECT, 'my_changes.my_info?return_url=' . urlencode(Registry::get('config.current_url')));
		}
		
		$user_data = fn_get_user_info($auth['user_id'], false);
		
		$is_valid_user_data = true;

		if (empty($_REQUEST['user_data']['email'])) {
			fn_set_notification('W', __('warning'), __('error_validator_required', array('[field]' => __('email'))));
			$is_valid_user_data = false;

		} elseif (!fn_validate_email($_REQUEST['user_data']['email'])) {
			fn_set_notification('W', __('error'), __('text_not_valid_email', array('[email]' => $_REQUEST['user_data']['email'])));
			$is_valid_user_data = false;
		} else if (empty($_REQUEST['user_data']['password1']) || empty($_REQUEST['user_data']['password2'])) {

			if (empty($_REQUEST['user_data']['password1'])) {
				fn_set_notification('W', __('warning'), __('error_validator_required', array('[field]' => __('password'))));
			}

			if (empty($_REQUEST['user_data']['password2'])) {
				fn_set_notification('W', __('warning'), __('error_validator_required', array('[field]' => __('confirm_password'))));
			}
			$is_valid_user_data = false;

		} elseif ($_REQUEST['user_data']['password1'] !== $_REQUEST['user_data']['password2']) {
			fn_set_notification('W', __('warning'), __('error_validator_password', array('[field2]' => __('password'), '[field]' => __('confirm_password'))));
			$is_valid_user_data = false;
		}
		
		$is_exist = db_get_field("SELECT user_id FROM ?:users WHERE fmail=?s AND user_id <> ?i", $user_data['email'], $auth['user_id']);
		
        if ($is_exist) {
            fn_set_notification('E', __('error'), __('error_user_exists'), '', 'user_exist');

            $is_valid_user_data = false;
        } else {
			$profile_fields = fn_get_profile_fields();
			
			foreach ($profile_fields['C'] as $field) {
				if ('Y' == $field['required']) {
					if (!empty($field['field_name'])) {
						
						if (empty($_REQUEST['user_data'][$field['field_name']])) {
							$is_valid_user_data = false;
							fn_set_notification('W', __('warning'), __('error_validator_required', array('[field]' => $field['description'])));
							break;
						}
					} else {
						if (empty($_REQUEST['user_data']['fields'][$field['field_id']])) {
							$is_valid_user_data = false;
							fn_set_notification('W', __('warning'), __('error_validator_required', array('[field]' => $field['description'])));
							break;
						}
					}
				}
			}
			
		}
		
		if (!$is_valid_user_data) {
			return array(CONTROLLER_STATUS_REDIRECT, 'user_info.update');
		}
		
		Mailer::sendMail(array(
            'to' => $user_data['fmail'],
            'from' => 'company_users_department',
            'data' => array(
                'user_data' => $user_data,
            ),
            'tpl' => 'addons/my_users/update_user_info.tpl',
            'company_id' => $user_data['company_id']
        ),  'C', CART_LANGUAGE);
      
		Mailer::sendMail(array(
          'to' => 'company_users_department',
          'from' => 'company_users_department',
          'reply_to' => $user_data['fmail'],
          'data' => array(
            'request_data' => $_REQUEST['user_data'],
            'user_data' => $user_data,
            'profile_fields' => $profile_fields['C'],
          ),
          'tpl' => 'addons/my_users/update_user_info_request.tpl'
		), 'A', Registry::get('settings.Appearance.backend_default_language'));
  
		fn_set_notification('N', __('notice'), __('text_user_info_submitted'), 'I', 'notice_user_info_submitted');
     
		return array(CONTROLLER_STATUS_OK, 'index');
	}
}

if ($mode == 'update') {

    if (empty($auth['user_id'])) {
        return array(CONTROLLER_STATUS_REDIRECT, 'auth.login_form?return_url=' . urlencode(Registry::get('config.current_url')));
    }

    $user_data = fn_get_user_info($auth['user_id'], false);
  
    if (empty($user_data)) {
        return array(CONTROLLER_STATUS_NO_PAGE);
    }

    $restored_user_data = fn_restore_post_data('user_data');
    if ($restored_user_data) {
        $user_data = fn_array_merge($user_data, $restored_user_data);
    }

    $profile_fields = fn_get_profile_fields();

    Tygh::$app['view']->assign('profile_fields', $profile_fields);
    Tygh::$app['view']->assign('user_data', $user_data);
    
}