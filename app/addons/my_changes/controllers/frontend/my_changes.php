<?php
require('tcpdf_barcodes_1d.php');
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
use Tygh\Mailer;
use Tygh\Pdf;


if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
	// nikosgkil
	if ($mode == 'login2') 
	{
		// me to session_start() apothikeuo to fmail sto session tou xristi gia na mporeso na to exo kai kata tin eisagwgi tou password.
		session_start();
		
		// Pairnei oti yparxei sto $_REQUEST[user_login] kai to pernaei sto fmail
		// Sthn synexeia, pairnei to row apo th vasi to opoio isodunamei me to fmail pou pliktrologise o xristis gia na doume an telika yparxei.
		$mail_array = explode(" ", $_REQUEST['user_login']);
		$fmail = $mail_array[0];
		
		// eggrafi sti vasi me to fmail pou pliktrologise o xristis
		$user_info = db_get_row("SELECT * FROM ?:users WHERE fmail = '$fmail' AND status = 'A'");
		$_SESSION['user_info'] = $user_info;
		$check_mail = $user_info[email];
		$_SESSION['fmail'] = $fmail;
		/*$check_for_email = db_query("SELECT * FROM ?:users WHERE fmail='$fmail'");
		fn_print_die($check_for_email);*/
			
		// metra poses fores yparxei to email pou exei pliktrologisei o xristis.
		$emails_counter = db_get_array("SELECT COUNT(*) FROM ?:users WHERE fmail = '$fmail' AND status = 'A'");
		$num_of_rows = $emails_counter[0]['COUNT(*)'];
		
		fn_get_user_short_info();
		
		if($num_of_rows > 1)
		{
			$redirect_url = 'index.php?dispatch=my_changes.my_emails';
			return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
		}
		else if($num_of_rows == 1)
		{
			$redirect_url = 'index.php?dispatch=my_changes.my_pass';
			
		$fmail = $_SESSION['fmail'];
		$counter = $_SESSION['counter'];
		$user_info = $_SESSION['user_info'];
		$u_data = fn_get_user_info($uid, false);
		
		$uid = $user_info[user_id];
	
		$selected_uid = $_REQUEST['user_id'];
		$customer_code = db_get_field("SELECT user_login FROM ?:users WHERE user_id = '$selected_uid'");
	
		// fernei to pass apo th gefyra - nikosgkil
		$password = db_get_field("SELECT password FROM supeshop_bridge.customer WHERE supeshop_bridge.customer.Code = '$customer_code'");
		$_SESSION['my_user_pass'] = $password;
	
		$_SESSION['login_id'] = $uid;
			
			return array(CONTROLLER_STATUS_REDIRECT, $redirect_url); 
		}
		else
		{
			$redirect_url = 'index.php?dispatch=my_changes.my_info';
			fn_set_notification('E', __('error'), __('error_incorrect_login'));
			return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
		}
	}
	if($mode == 'login3')
	{
		//
		// Login Process apo app\common\auth.php 27-143 me liga dika mou
		//	
		session_start();	
		$fmail = $_SESSION['fmail'];
		$user_id = $_SESSION['login_id'];
		$password = $_SESSION['my_user_pass'];
		
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
			
			// fernei ta dedomena tou xristi - nikosgkil
			$u_data = fn_get_user_info($uid, false);
			$user_login1 = $u_data['user_login']; 
			//fn_print_die($u_data);
			// fernei to pass apo th gefyra - nikosgkil
			$password = db_get_field("SELECT password FROM supeshop_bridge.customer WHERE supeshop_bridge.customer.Code = '$user_login1'");
			
			$email_for_login = $u_data['email'];
			$user_login = $email_for_login;
			$_REQUEST['user_login'] = $email_for_login;
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
			
			//fn_print_die($_SESSION);
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
				
				// vlepei an yparxoun ypokatastimata kai ta emfanizei gia epilogi - nikosgkil
				//$uid = db_get_field("SELECT user_id FROM ?:users WHERE email = '$fmail'");

				$ypok_array = array();
				$ypok_array = db_get_array("SELECT profile_id FROM ?:user_profiles WHERE user_id = '$uid'");
				$profiles_counter = db_get_array("SELECT COUNT(*) FROM ?:user_profiles WHERE user_id = '$uid'");
				$num_of_profiles = $profiles_counter[0]['COUNT(*)'];

				$_SESSION['num_of_profiles'] = $num_of_profiles;
				
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


				/**********************************************************************************************************************************/
				/**********************************************************************************************************************************/
				// pare se array ola ta quick packages toy xristi
				$quick_packages = db_get_array("SELECT package_id FROM ?:package_data WHERE creation = 'Q' AND user_id = ?i", $user_id);
				foreach($quick_packages as $key=>$quick)
				{
					$product_ids_from_packages[] = db_get_array("SELECT product_id, product_code FROM ?:products WHERE product_id = ?i", $quick_packages[$key][package_id]);
					$real_products[] = db_get_array("SELECT product_id FROM ?:package_products WHERE package_id = ?i", $quick_packages[$key][package_id]);
				}
				$_SESSION['product_ids_from_pack'] = $product_ids_from_packages;
				$_SESSION['real_products'] = $real_products;
				//fn_print_die($product_ids_from_packages, $real_products);


				/**********************************************************************************************************************************/
				/**********************************************************************************************************************************/
				
				// steile ton pelati stin arxiki meta apo epityximeno login - nikosgkil
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
	// nikosgkil
	if($mode == 'my_pass')
	{
		session_start();	
		$fmail = $_SESSION['fmail'];
		$counter = $_SESSION['counter'];
		$user_info = $_SESSION['user_info'];
		$u_data = fn_get_user_info($uid, false);
	
		// fernei to user_id tou xrhsth
		//$uid = db_get_field("SELECT user_id FROM ?:users WHERE email = '$fmail'");
		
		$uid = $user_info[user_id];
	
		$selected_uid = $_REQUEST['user_id'];
		$customer_code = db_get_field("SELECT user_login FROM ?:users WHERE user_id = '$selected_uid'");
	
		// fernei to pass apo th gefyra - nikosgkil
		$password = db_get_field("SELECT password FROM supeshop_bridge.customer WHERE supeshop_bridge.customer.Code = '$customer_code'");
		$_SESSION['my_user_pass'] = $password;
	
		if(isset($_REQUEST['user_id']))
		{
			$user_id = (int)$_REQUEST['user_id'];
		}
		else 
		{
			if($counter == 1)
			{
				$user_id = $uid;
			}
			else if($counter >1)
			{
				$user_id = $selected_uid;
			}
			else
			{
				$redirect_url = 'index.php';
				return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
			}
		}
		$_SESSION['login_id'] = $user_id;
	}

	// -------------------------------------------------------------------------------------------------------------------
	// ------------------------- dokimes gia apothikeusi tou cart nikosgkil - arxi ---------------------------------------
	// -------------------------------------------------------------------------------------------------------------------	
	if($mode == 'prof_choice')
	{
		session_start();
		
		$num_of_profiles = $_SESSION['num_of_profiles'];
		fn_my_users_pre_save_cart($_SESSION['cart'], $_SESSION['login_id'], $type = 'C');


		//fn_print_r($num_of_profiles);
		
		
		unset($_SESSION['cart']);
		$redirect_url = 'index.php?dispatch=profiles.choose';
		return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
		
		
		Tygh::$app['view']->assign('num_of_profiles', $num_of_profiles);
	}
	// -------------------------------------------------------------------------------------------------------------------
	// ------------------------ dokimes gia apothikeusi tou cart nikosgkil - telos ---------------------------------------
	// -------------------------------------------------------------------------------------------------------------------
	if($mode == 'my_print')
	{
		//
		// nikosgkil - palios tropos 
		//
		/*$prod_id = $_REQUEST['product_id'];
		$product_name = db_get_field("SELECT product FROM ?:product_descriptions WHERE product_id = ?i", $prod_id);
		$code = fn_my_product_packages_get_code($_REQUEST['product_id']);*/
		//
		// nikosgkil
		//
		
		/************************************************************************************************/
		/************ nikosgkil pare to prod_id, onoma, classroom kai typwse ta se pdf ******************/
		/************************************************************************************************/
		//fn_print_die($_SESSION);
		$dispatch = $_REQUEST[dispatch];
		$pieces = explode(".", $dispatch);
		$prod_id = (int)$pieces[2];
		$classroom = $_REQUEST[classroom];
		$code = fn_my_product_packages_get_code($prod_id);
		$name = $_SESSION['cart']['user_data']['firstname'];
		$name_question = $_REQUEST[onoma];
		/************************************************************************************************/
		/************ nikosgkil pare to prod_id, onoma, classroom kai typwse ta se pdf ******************/
		/************************************************************************************************/
		
		/***** retail price *****/
		$user_id = $_SESSION['login_id'];
		$teliko_prodid[] =  db_get_fields("SELECT product_id FROM ?:package_products WHERE package_id = ?i", $prod_id);
		$usergroup_id = db_get_field("SELECT usergroup_id FROM cscart_usergroup_links WHERE status = 'A' AND user_id = '$user_id' AND link_id = '$teliko_prodid'");
		
		
		$metritis_proiontwn = count($teliko_prodid[0]);
		if($metritis_proiontwn > 1)
		{
			for($i = 0; $i < $metritis_proiontwn; $i++)
			{
				$products_id[$i] = $teliko_prodid[0][$i];
				$product_id = $products_id[$i];
				$retail_price = $retail_price + db_get_field("SELECT price FROM cscart_product_prices WHERE usergroup_id = '0' AND product_id = '$product_id'");
			}
		}
		elseif ($metritis_proiontwn == 1)
		{
			$product_id = $teliko_prodid[0][0];
			$retail_price = db_get_field("SELECT price FROM cscart_product_prices WHERE usergroup_id = '0' AND product_id = '$product_id'");
		}

		/**********************/

		$code_in_string = (string)$code;		
		$barcodeobj = new TCPDFBarcode($code_in_string, 'C128');
		$tcpdfbarcode = $barcodeobj->getBarcodePNG(1, 30, array(0,0,0));
		file_put_contents('http://supercourse-eshop.gr/barcode.png', $tcpdfbarcode);
		
		$cols = 2;
		$rows = 4;
		//
		// to if menei se periptosi p thelisoume na typosoume kai onoma kapoia stigmi
		//
		//if($name_question == 'yes')
		//{
			//$name = (string)$name;
			// ektyposi onomatos front kai classroom
			//$code_arr = array_fill(0, $cols, __('class_package_name').': '.$classroom.'<br>'.__('kwdikos_paraggelias_synthesis').'<br>'.$code.'<br><img src="/barcode.png" alt="Package Code" width="200"><br>'.'<p style="font-size:14px">'.$name.'</p>');
		//}
		//else if($name_question == 'no')
		//{
			$code_arr = array_fill(0, $cols,'<p style="text-align:center">'.__('onoma').'<br><br><br>'.__('class_package_name').': '.$classroom.__('kwdikos_paraggelias_synthesis').$code.'<br><img src="http://supercourse-eshop.gr/barcode.png" alt="Package Code" width="200"><br>'.__('timi_lianikis').$retail_price.'&euro;</p>');
		//}
		//$tr = '<tr><td style="border:1px dashed #555; padding: 4px; height:245; width:50%; text-align:left;">'.implode('</td><td style="border:1px dashed #555; padding: 4px; width:50%; text-align:left;">', $code_arr).'</td></tr>';
		$tr = '<tr><td style = "border:1px dashed #555; border-right-style: none; background-color:#c5c5c5;"><div style="margin-left: 20%; margin-top: 180px;">'.__("url_eshop").'</div></td><td style="border:1px dashed #555; border-left-style: none; padding: 2px; height:245; width:45%; text-align:left;">'.implode('</td><td style = "border:1px dashed #555; border-right-style: none; background-color:#c5c5c5;"><div style="margin-left: 20%; margin-top: 180px;">'.__("url_eshop").'</div></td><td style="border:1px dashed #555; border-left-style: none; padding: 2px; width:45%; text-align:left;">', $code_arr).'</td></tr>';
		fn_disable_live_editor_mode();
		$html = '<table style="height:100%; width:100%; font-size:20">';
		for($i=0; $i < $rows; $i++) $html.= $tr;
		$html.= '</table>';
        	Pdf::render(array($html), $code);
	}

	if($mode == 'my_print_quick')
	{
		//
		// nikosgkil 
		//
		
		/************************************************************************************************/
		/************ nikosgkil pare to prod_id, onoma, classroom kai typwse ta se pdf ******************/
		/************************************************************************************************/
		//fn_print_die($_SESSION);
		$dispatch = $_REQUEST[dispatch];
		$pieces = explode(".", $dispatch);
		$prod_id = (int)$pieces[2];
		$classroom = $_REQUEST[classroom];
		$code = fn_my_product_packages_get_code($prod_id);
		$name = $_SESSION['cart']['user_data']['firstname'];
		$name_question = $_REQUEST[onoma];
		/************************************************************************************************/
		/************ nikosgkil-pare to prod_id, onoma, classroom kai typwse ta se pdf ******************/
		/************************************************************************************************/
		
		$code_in_string = (string)$code;		
		$barcodeobj = new TCPDFBarcode($code_in_string, 'C128');
		$tcpdfbarcode = $barcodeobj->getBarcodePNG(1, 30, array(0,0,0));
		file_put_contents('http://supercourse-eshop.gr/barcode.png', $tcpdfbarcode);
		
		$cols = 2;
		$rows = 4;
		
		$code_arr = array_fill(0, $cols, __('class_package_name').': '.$classroom.'<br>'.__('kwdikos_paraggelias_synthesis').'<br>'.$code.'<br><img src="http://supercourse-eshop.gr/barcode.png" alt="Package Code" width="200"><br>');
		$tr = '<tr><td style="border:1px dashed #555; padding: 8px; height:245; width:50%; text-align:center;">'.implode('</td><td style="border:1px dashed #555; padding: 8px; width:50%; text-align:center;">', $code_arr).'</td></tr>';
		fn_disable_live_editor_mode();
		$html = '<table style="height:100%; width:100%; font-size:20">';
		for($i=0; $i < $rows; $i++) $html.= $tr;
		$html.= '</table>';
       		Pdf::render(array($html), $code);
	}

}
// nikosgkil
if($mode == 'my_emails') 
{
	session_start();	
	$fmail = $_SESSION['fmail'];
	$user_info = $_SESSION['user_info'];
	$redirect_url = '';
	
	if($fmail=='')
	{
		$redirect_url = 'index.php';
		return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
	}
	
	$uid = db_get_field("SELECT user_id FROM ?:users WHERE email = '$fmail' AND status = 'A'");
	// fere ta dedomena tou xristi
	$u_data = fn_get_user_info($uid, false);
	
	// array me ta email twn xristwn poy antistoixoun sto fmail
	$emails = db_get_fields("SELECT email FROM ?:users WHERE fmail LIKE '%$fmail%' AND status = 'A'");
	$counter = count($emails);
	$_SESSION['counter'] = $counter;
	
	// array me ta user_ids twn xristwn me to idio fmail
	$uid_pre = db_get_array("SELECT user_id FROM ?:users WHERE fmail = '$fmail' AND status = 'A'");
	
	// array me ta dedomena twn xristwn me to idio fmail
	$index = 0;		
	$udata_array = array();
	for($index=0; $index<$counter; $index++)
	{
		$udata_array[$index] = fn_get_user_info($uid_pre[$index][user_id], false);
	}
	
	Tygh::$app['view']->assign('uid_pre', $uid_pre);		
	Tygh::$app['view']->assign('udata_array', $udata_array);
}
