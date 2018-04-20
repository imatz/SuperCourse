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
use Tygh\Development;
use Tygh\Registry;
use Tygh\Session;
use Tygh\Helpdesk;

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
	if ($mode == 'login2') 
	{
		// me to session_start() apothikeuo to fmail sto session tou xristi gia na mporeso na to exo kai kata tin eisagwgi tou password.
		session_start();
		
		// Pairnei oti yparxei sto $_REQUEST[user_login] kai to pernaei sto fmail
		// Sthn synexeia, pairnei to row apo th vasi to opoio isodunamei me to fmail pou pliktrologise o xristis gia na doume an telika yparxei.
		$mail_array = explode(" ", $_REQUEST['user_login']);
		$fmail = $mail_array[0];
		$check_mail_array = db_get_row("SELECT email FROM ?:users WHERE fmail = '$fmail'");	
		$check_mail = $check_mail_array[email];
		$_SESSION['fmail'] = $fmail;
				
		// Pare oti yparxei aristera tou "@" xoris to "@"
		/*$explode = explode("@",$fmail);
		array_pop($explode);
		$newstring = join('@', $explode);
		$check = db_get_row("SELECT email FROM ?:users WHERE email LIKE '%$newstring%'");*/
		
		// metra poses fores yparxei to email pou exei pliktrologisei o xristis. Yparxei episis kai autos o tropos: $num_of_rows = count($emails_counter);
		$emails_counter = db_get_array("SELECT COUNT(*) FROM ?:users WHERE fmail = '$fmail'");
		$num_of_rows = $emails_counter[0]['COUNT(*)'];
		
		fn_get_user_short_info();
		
		if($num_of_rows == 0)
		{
			$redirect_url = 'index.php?dispatch=my_changes.my_info';
			fn_set_notification('E', __('error'), __('error_incorrect_login'));
			return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
		}
		else if($num_of_rows == 1)
		{
			$redirect_url = 'index.php?dispatch=my_changes.my_pass';
			return array(CONTROLLER_STATUS_REDIRECT, $redirect_url); 
		}
		else
		{
			$redirect_url = 'index.php?dispatch=my_changes.my_emails';
			return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
		}
	}
	if($mode == 'login3')
	{
		//
		// Login Process
		//	
		session_start();	
		$fmail = $_SESSION['fmail'];
		$user_id = $_SESSION['login_id'];
		$redirect_url = '';
	
			if (AREA != 'A') 
			{
				if (fn_image_verification('login', $_REQUEST) == false) 
				{
					fn_save_post_data('user_login');
	
					return array(CONTROLLER_STATUS_REDIRECT);
				}
			}
			
			if ($fmail) 
			{
				$condition = '';
	
				if (fn_allowed_for('ULTIMATE')) 
				{
					if (Registry::get('settings.Stores.share_users') == 'N' && AREA != 'A') 
					{
						$condition = fn_get_company_condition('?:users.company_id');
					}
				}
			}
	
			$uid = $user_id;
			
			// fernei to pass apo th gefyra
			$password = db_get_field("SELECT password FROM supercou_bridge26s.customer WHERE supercou_bridge26s.customer.eShop_Mail = '$fmail'");
			// fernei ta dedomena tou xristi
			$u_data = fn_get_user_info($uid, false);
			
			$user_login = $fmail;
			$_REQUEST['user_login'] = $fmail;
			$redirect_url = 'index.php?return_url=index.php&dispatch=my_changes.my_pass';
			$_REQUEST['redirect_url'] = $redirect_url;
			$return_url = 'index.php';
			$_REQUEST['return_url'] = $return_url;
			fn_restore_processed_user_password($_REQUEST, $_POST);
	
			list($status, $user_data, $user_login, $password, $salt) = fn_auth_routines($_REQUEST, $auth);
	
			if (!empty($_REQUEST['redirect_url'])) 
			{
				$redirect_url = $_REQUEST['redirect_url'];
			} 
			else 
			{
				$redirect_url = fn_url('auth.login' . !empty($_REQUEST['return_url']) ? '?return_url=' . $_REQUEST['return_url'] : '');
			}
	
			if ($status === false) 
			{
				fn_save_post_data('user_login');
	
				return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
			}
			
			//
			// Success login
			//
			if (!empty($user_data) && !empty($password) && fn_generate_salted_password($password, $salt) == $user_data['password']) 
			{
				// Regenerate session_id for security reasons
				Session::regenerateId();
				
				//
				// If customer placed orders before login, assign these orders to this account
				//
				if (!empty($auth['order_ids'])) {
					foreach ($auth['order_ids'] as $k => $v) {
						db_query("UPDATE ?:orders SET ?u WHERE order_id = ?i", array('user_id' => $user_data['user_id']), $v);
					}
				}
				
				fn_login_user($uid);
				
				// vlepei an yparxoun ypokatastimata kai ta emfanizei gia epilogi
				$uid = db_get_field("SELECT user_id FROM ?:users WHERE email = '$fmail'");
				$ypok_array = array();
				$ypok_array = db_get_array("SELECT profile_id FROM ?:user_profiles WHERE user_id = '$uid'");
				$profiles_counter = db_get_array("SELECT COUNT(*) FROM ?:user_profiles WHERE user_id = '$uid'");
				$num_of_profiles = $profiles_counter[0]['COUNT(*)'];
				
				if($num_of_profiles >1)
				{
					$redirect_url = 'index.php?dispatch=profiles.choose';
					return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
				}
				
	
				Helpdesk::auth();
				// Set system notifications
				if (Registry::get('config.demo_mode') != true && AREA == 'A') {
					// If username equals to the password
					if (!fn_is_development() && fn_compare_login_password($user_data, $password)) {
						$lang_var = 'warning_insecure_password_email';
	
						fn_set_notification('E', __('warning'), __($lang_var, array(
							'[link]' => fn_url('profiles.update')
						)), 'S', 'insecure_password');
					}
					if (empty($user_data['company_id']) && !empty($user_data['user_id'])) {
						// Insecure admin script
						if (!fn_is_development() && Registry::get('config.admin_index') == 'admin.php') {
							fn_set_notification('E', __('warning'), __('warning_insecure_admin_script', array('[href]' => Registry::get('config.resources.admin_protection_url'))), 'S');
						}
	
						if (!fn_is_development() && is_file(Registry::get('config.dir.root') . '/install/index.php')) {
							fn_set_notification('W', __('warning'), __('delete_install_folder'), 'S');
						}
	
						if (Development::isEnabled('compile_check')) {
							fn_set_notification('W', __('warning'), __('warning_store_optimization_dev', array('[link]' => fn_url("themes.manage"))));
						}
	
						fn_set_hook('set_admin_notification', $user_data);
					}	
				}
	
				if (!empty($_REQUEST['remember_me'])) {
					fn_set_session_data(AREA . '_user_id', $user_data['user_id'], COOKIE_ALIVE_TIME);
					fn_set_session_data(AREA . '_password', $user_data['password'], COOKIE_ALIVE_TIME);
				}
	
				if (!empty($_REQUEST['return_url'])) {
					$redirect_url = $_REQUEST['return_url'];
				}
	
				unset($_REQUEST['redirect_url']);
	
				if (AREA == 'C') {
					fn_set_notification('N', __('notice'), __('successful_login'));
				}
	
				if (AREA == 'A' && Registry::get('runtime.unsupported_browser')) {
					$redirect_url = "upgrade_center.ie7notify";
				}
	
				unset($_SESSION['cart']['edit_step']);
				
				// steile ton pelati stin arxiki meta apo epityximeno login
				$redirect_url = 'index.php';
				return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
			}
			else
			{
				//
        		// Login incorrect
        		//
				// Log user failed login
				fn_log_event('users', 'failed_login', array (
					'user' => $user_login
				));
	
				$auth = array();
				fn_set_notification('E', __('error'), __('error_incorrect_login'));
				fn_save_post_data('user_login');
	
				return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
			}
	}
}
if($mode == 'my_emails')
{
	//fn_add_breadcrumb($title);
	session_start();	
	$fmail = $_SESSION['fmail'];
	$redirect_utl = '';
	
	if($fmail=='')
	{
		$redirect_url = 'index.php';
		return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
	}
	
	$uid = db_get_field("SELECT user_id FROM ?:users WHERE email = '$fmail'");
	// fere ta dedomena tou xristi
	$u_data = fn_get_user_info($uid, false);
	
	// array me ta email twn xristwn poy antistoixoun sto fmail
	$emails = db_get_fields("SELECT email FROM ?:users WHERE fmail LIKE '%$fmail%'");
	$counter = count($emails);
	
	// array me ta user_ids twn xristwn me to idio fmail
	$uid_pre = db_get_array("SELECT user_id FROM ?:users WHERE fmail = '$fmail'");
	
	// array me ta dedomena twn xristwn me to idio fmail
	$index = 0;		
	$udata_array = array();
	for($index=0; $index<$counter; $index++)
	{
		$udata_array[$index] = fn_get_user_info($uid_pre[$index][user_id], false);
	}
	//fn_print_die($udata_array);
	
	Tygh::$app['view']->assign('uid_pre', $uid_pre);		
	Tygh::$app['view']->assign('udata_array', $udata_array);
}

if($mode == 'my_pass')
{
	session_start();	
	$fmail = $_SESSION['fmail'];
	
	// fernei to user_id tou xrhsth
	$uid = db_get_field("SELECT user_id FROM ?:users WHERE email = '$fmail'");
	
	if(isset($_REQUEST['user_id']))
	{
		$user_id = (int)$_REQUEST['user_id'];
	}
	else 
	{
		$user_id = $uid;
	}
	
	$_SESSION['login_id'] = $user_id;
}