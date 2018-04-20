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

use Tygh\Mailer;
use Tygh\Registry;

session_start();
$user_id = $_SESSION['login_id'];
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (AREA != 'A')) {
    if ($mode == 'recover_password') {
      $user_email = !empty($_REQUEST['user_email']) ? $_REQUEST['user_email'] : '';
      $redirect_url = '';
      
      // copy of fn_recover_password_generate_key()
	  //fn_print_r($user_id);
      
      $result = true;

      if ($user_email) {
        $condition = '';

        if (fn_allowed_for('ULTIMATE')) {
          if (Registry::get('settings.Stores.share_users') == 'N' && AREA != 'A') {
            $condition = fn_get_company_condition('?:users.company_id');
          }
        }
		
		// ekana comment tin apo kato grammi gia na doso ego karfoto to id poy eixe epilexei sto my_emails 
        //$uid = db_get_field("SELECT user_id FROM ?:users WHERE email = ?s" . $condition, $user_email); // i. matziaris
		$uid = $user_id; // nikosgkil

        $u_data = fn_get_user_info($uid, false);
        if (isset($u_data['status']) && $u_data['status'] == 'D') {
          fn_set_notification('E', __('error'), __('error_account_disabled'));
          $redirect_url = "auth.recover_password";
        } else if (!empty($u_data['email'])) {
          // fere to pass ap th gefyra
          $user_bridge = new \Sync\User();
          $password = $user_bridge->get_password_by_shop_id($uid);
          Registry::get('view')->assign('password',$password);
          
          
          Mailer::sendMail(array(
              'to' => $u_data['fmail'],
              'from' => 'company_users_department',
              'data' => array( 
                'user_data' => fn_get_user_info($uid),
              ), 
              'tpl' => 'addons/my_users/profile_activated.tpl',
          ), 'C', $u_data['lang_code']);

          fn_set_notification('N', __('information'), __('text_password_recovery_instructions_sent'));
         
        } else {
          fn_set_notification('E', __('error'), __('error_login_not_exists'));
          $redirect_url = "auth.recover_password";
        }
      } else {
        fn_set_notification('E', __('error'), __('error_login_not_exists'));
        $redirect_url = "auth.recover_password";
      }
     
      return array(CONTROLLER_STATUS_REDIRECT, !empty($redirect_url)? $redirect_url : fn_url());
    }

   
}