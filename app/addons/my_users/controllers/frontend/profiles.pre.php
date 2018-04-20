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
	// krata mono osa ueloyme
	if ($mode == 'update') { 
    return array(CONTROLLER_STATUS_NO_PAGE);
		$parathrhseis_id=fn_my_users_get_setting_field('s_delivery_notes');
		
		$user_data=array(
			'email' => $_REQUEST['user_data']['fmail'],
			'password1' => $_REQUEST['user_data']['password1'],
			'password2' => $_REQUEST['user_data']['password2'],
			'phone' => $_REQUEST['user_data']['phone'],
			'fields' => fn_my_users_get_user_profile_data($auth['user_id'])
		);
		
		$user_data['fields'][$parathrhseis_id]=$_REQUEST['user_data']['fields'][$parathrhseis_id];
		$_REQUEST['user_data']=$user_data;
		
  } else if ($mode == 'choose') {
    if(isset($_REQUEST['profile_id']))
      $profile_id = (int)$_REQUEST['profile_id'];
    if (empty($profile_id))
      fn_set_notification('W', __('warning'), __('choose_a_profile'));
    else {
      $auth['profile_id'] = $_SESSION['cart']['profile_id'] = $profile_id; // default checkout profile
      
      fn_extract_cart_content($_SESSION['cart'], $auth['user_id'], 'C');

	  	//----------------------------------------------------------------------------------------------------------------------
	  	//----------------- nikosgkil - elegxos gia apenergopoiimenes syntheseis tou pelati pou kanei login --------------------
	  	//----------------------------------------------------------------------------------------------------------------------
	  	$uid = $auth['user_id'];
	  	$syntheseis = db_get_fields("SELECT package_id FROM ?:package_data WHERE user_id = ?i", $uid);
	  	$synth_counter = count($syntheseis);
	  	$flag = 0;
	  	
		for($i=0; $i<$synth_counter; $i++)
	  	{
			$synth_status[] = db_get_field("SELECT status FROM ?:products WHERE product_id = ?i", $syntheseis[$i]);
		}
	  	for($i=0; $i<$synth_counter; $i++)
	  	{
		  	if($synth_status[$i] == 'D')
		  	{
			  	if(db_get_field("SELECT flag FROM ?:products WHERE product_id = ?i", $syntheseis[$i]) == '1')
			  	{
					$flag = 1;
				 	db_query("UPDATE ?:products SET flag='0' WHERE product_id = ?i", $syntheseis[$i]);
			  	}
		  	}
		}	  
	  	// gia redirect kai emfanisi minimatos lathous se periptosi euresis apenergopoiimenis synthesis...
	  	// to if($flag = 1) else den ypirxe, itan mono to:
	  	// return array(CONTROLLER_STATUS_REDIRECT, 'index.php');
	  	if($flag == 1)
	  	{
			$flag = 0;
		  	fn_set_notification('E', 'Ανενεργά προϊόντα στις συνθέσεις σας', 'Κάποιο από τα προϊόντα που υπάρχει στις συνθέσεις είναι πλέον ανενεργό. Παρακαλούμε, ελέγξτε τις δημιουργημένες συνθέσεις σας και κάντε τις απαραίτητες διορθώσεις.', 'K'); 
		  	$redirect_url = '?search_performed=Y&status=D&creation=&dispatch%5Bproducts.manage%5D=&security_hash=3dc28eadebad95b18b8943b63758fe86';
		  	return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
	  	}
	  	else
	  	{
		  	return array(CONTROLLER_STATUS_REDIRECT, 'index.php');
	  	}	
		//----------------------------------------------------------------------------------------------------------------------
		//----------------- nikosgkil - elegxos gia apenergopoiimenes syntheseis tou pelati pou kanei login --------------------
		//----------------------------------------------------------------------------------------------------------------------
      
      //return array(CONTROLLER_STATUS_REDIRECT, 'index.php');
    }
    
	} else if ($mode == 'register') {
        if (fn_image_verification('use_for_register', $_REQUEST) == false) {
			
            fn_save_post_data('user_data');

            return array(CONTROLLER_STATUS_REDIRECT, 'profiles.register');
        }
		
		$is_valid_user_data = true;
		$redirect_url = "profiles.register";
		if (empty($_REQUEST['user_data']['password1']) || empty($_REQUEST['user_data']['password2'])) {

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
		} elseif (fn_is_user_exists(0, $_REQUEST['user_data'])) {
      fn_set_notification('E', __('error'), __('error_user_exists'), '', 'user_exist');
      $is_valid_user_data = false;
    } else {
      $redirect_url = "profiles.success_register";
      fn_set_notification('W', __('important'), __('text_profile_should_be_approved'));
	  //fn_print_die($_REQUEST[user_data]);
      // send confirmation to customer
      Mailer::sendMail(array(
          'to' => $_REQUEST['user_data']['email'],
          'from' => 'company_users_department',
          'data' => array(
            'user_data' => $_REQUEST['user_data'],
          ),
          'tpl' => 'addons/my_users/create_profile.tpl'
      ), 'C', CART_LANGUAGE);
     
    // $to=('f2151605@mvrht.com'==$_REQUEST['user_data']['email'])?$_REQUEST['user_data']['email']:'company_users_department';
      // Notify administrator about new profile
      Mailer::sendMail(array(
          'to' => 'company_users_department',
          'from' => 'company_users_department',
          'reply_to' => $_REQUEST['user_data']['email'],
          'data' => array(
            'user_data' => $_REQUEST['user_data'],
          ),
          'tpl' => 'addons/my_users/activate_profile2.tpl'
      ), 'A', Registry::get('settings.Appearance.backend_default_language'));
    }
    
    if (!$is_valid_user_data) {
			fn_save_post_data('user_data');
			
			return array(CONTROLLER_STATUS_REDIRECT, 'profiles.register');
		}
    /*elseif (empty($_REQUEST['user_data']['s_phones'][0])) {
			fn_set_notification('W', __('warning'), __('error_validator_phone'));
			$is_valid_user_data = false;
		}
		
		// bres ton xrhsth an yparxei
		$afm_id=fn_my_users_get_setting_field('afm');
		$afm=$_REQUEST['user_data']['fields'][$afm_id];
		$tel=$_REQUEST['user_data']['s_phones'][0];
		
		$user_data=fn_my_users_get_unregistered_user($afm,$tel);
		
		if(empty($user_data)) {
			fn_set_notification('W', __('warning'), __('text_unregistered_user_not_found'));
			$is_valid_user_data = false;
		} elseif ('A'==$user_data['status']) {
			fn_set_notification('W', __('warning'), __('text_profile_activated'));
			$is_valid_user_data = false;
		} elseif (!empty($user_data['password'])) { 
			fn_set_notification('W', __('warning'), __('text_registered_and_waiting'));
			$is_valid_user_data = false;
		} else {
			$user_id=$user_data['user_id'];
		}
		
		if (!$is_valid_user_data) {
			fn_save_post_data('user_data');
			
			return array(CONTROLLER_STATUS_REDIRECT, 'profiles.register');
		}
       
        fn_restore_processed_user_password($_REQUEST['user_data'], $_POST['user_data']);
		
		//vraia bale oti exei h bash kai bgale ta thlefvna
		unset($_REQUEST['user_data']['s_phones']);
		$_REQUEST['user_data']['fields']=fn_my_users_get_user_profile_data($user_id);
		
		// gia to mail poy paei ston pelath pros epibebaivsh ypobolhs
		Registry::get('view')->assign('req_password',$_REQUEST['user_data']['password1']);
		
        $res = fn_update_user($user_id, $_REQUEST['user_data'], $auth, false, true);

        if ($res) {
            list($user_id, $profile_id) = $res;

            // Cleanup user info stored in cart
            if (!empty($_SESSION['cart']) && !empty($_SESSION['cart']['user_data'])) {
                $_SESSION['cart']['user_data'] = fn_array_merge($_SESSION['cart']['user_data'], $_REQUEST['user_data']);
            }

            // Delete anonymous authentication
            if ($cu_id = fn_get_session_data('cu_id') && !empty($auth['user_id'])) {
                fn_delete_session_data('cu_id');
            }

            Session::regenerateId();
			// mail admin gia energopoihsh FIXME ueloyn allagh oi metablhtes
			if (Registry::get('settings.General.approve_user_profiles') == 'Y') {
                fn_set_notification('W', __('important'), __('text_profile_should_be_approved'));
				
				$userBridge = new Sync\User();
				
				$mail_data=array(
					'code'=>$userBridge->get_customer_by_shop_id($user_id),
					'afm'=>$afm,
					'tel'=>$tel,
					'email'=>$user_data['email']
				);
				
                // Notify administrator about new profile
                Mailer::sendMail(array(
                    'to' => 'company_users_department',
                    'from' => 'company_users_department',
                    'reply_to' => $user_data['email'],
                    'data' => array(
                        'user_data' => $mail_data,
                    ),
                    'tpl' => 'addons/my_users/activate_profile.tpl',
                    'company_id' => $user_data['company_id']
                ), 'A', Registry::get('settings.Appearance.backend_default_language'));
				}

            if (!empty($_REQUEST['return_url'])) {
                return array(CONTROLLER_STATUS_OK, $_REQUEST['return_url']);
            }

        } else {
            fn_save_post_data('user_data');
            fn_delete_notification('changes_saved');
        }

        if (!empty($user_id)) {
            $redirect_url = "profiles.success_register";
        } else {
            $redirect_url = "profiles.register";
*/
            if (!empty($_REQUEST['return_url'])) {
                $redirect_url .= 'return_url=' . urlencode($_REQUEST['return_url']);
            }
 //       }

        return array(CONTROLLER_STATUS_OK, $redirect_url);
    }
	
}

if ($mode == 'choose') {
  if (empty($auth['user_id'])) {
    return array(CONTROLLER_STATUS_REDIRECT, "index.php");
  }
  
  $profiles = db_get_array("SELECT * FROM ?:user_profiles WHERE user_id = ?i", $auth['user_id']);
  
//an exei 1 den xreiazetai na epilejei <- imatz 
  // --> nikosgkil elegxos synthesewn kai redirect <-- nikosgkil
  if (1==count($profiles)) 
  {
    $auth['profile_id'] = $profiles[0]['profile_id'];
    fn_extract_cart_content($_SESSION['cart'], $auth['user_id'], 'C');
	
		//----------------------------------------------------------------------------------------------------------------------
	  	//----------------- nikosgkil - elegxos gia apenergopoiimenes syntheseis tou pelati pou kanei login --------------------
	  	//----------------------------------------------------------------------------------------------------------------------
	  	$uid = $auth['user_id'];
	  	$syntheseis = db_get_fields("SELECT package_id FROM ?:package_data WHERE user_id = ?i", $uid);
	  	$synth_counter = count($syntheseis);
	  	$flag = 0;
	  	
		for($i=0; $i<$synth_counter; $i++)
	  	{
			$synth_status[] = db_get_field("SELECT status FROM ?:products WHERE product_id = ?i", $syntheseis[$i]);
		}
	  	for($i=0; $i<$synth_counter; $i++)
	  	{
		  	if($synth_status[$i] == 'D')
		  	{
			  	if(db_get_field("SELECT flag FROM ?:products WHERE product_id = ?i", $syntheseis[$i]) == '1')
			  	{
					$flag = 1;
				 	db_query("UPDATE ?:products SET flag='0' WHERE product_id = ?i", $syntheseis[$i]);
			  	}
		  	}
		}	  
	  	// gia redirect kai emfanisi minimatos lathous se periptosi euresis apenergopoiimenis synthesis...
	  	// to if($flag = 1) else den ypirxe, itan mono to:
	  	// return array(CONTROLLER_STATUS_REDIRECT, 'index.php');
	  	if($flag == 1)
	  	{
			$flag = 0;
		  	fn_set_notification('E', 'Ανενεργά προϊόντα στις συνθέσεις σας', 'Κάποιο από τα προϊόντα που υπάρχει στις συνθέσεις είναι πλέον ανενεργό. Παρακαλούμε, ελέγξτε τις δημιουργημένες συνθέσεις σας και κάντε τις απαραίτητες διορθώσεις.', 'K'); 
		  	$redirect_url = '?search_performed=Y&status=D&creation=&dispatch%5Bproducts.manage%5D=&security_hash=3dc28eadebad95b18b8943b63758fe86';
		  	return array(CONTROLLER_STATUS_REDIRECT, $redirect_url);
	  	}
	  	else
	  	{
		  	return array(CONTROLLER_STATUS_REDIRECT, 'index.php');
	  	}
    }
	
	//----------------------------------------------------------------------------------------------------------------------
	//----------------- nikosgkil - elegxos gia apenergopoiimenes syntheseis tou pelati pou kanei login --------------------
	//----------------------------------------------------------------------------------------------------------------------
	// pare se array ola ta quick packages toy xristi
		$user_id = $_SESSION['login_id'];
		$quick_packages = db_get_array("SELECT package_id FROM ?:package_data WHERE creation = 'Q' AND user_id = ?i", $user_id);
		foreach($quick_packages as $key=>$quick)
		{
			$product_ids_from_packages[] = db_get_array("SELECT product_id, product_code FROM ?:products WHERE product_id = ?i", $quick_packages[$key][package_id]);
			$real_products[] = db_get_array("SELECT product_id FROM ?:package_products WHERE package_id = ?i", $quick_packages[$key][package_id]);
		}
		$_SESSION['product_ids_from_pack'] = $product_ids_from_packages;
		$_SESSION['real_products'] = $real_products;
	
    //return array(CONTROLLER_STATUS_REDIRECT, "index.php");
  
  foreach ($profiles as &$profile) {
    $phones = fn_my_users_get_user_profile_phones('S',$profile['user_id'],$profile['profile_id']);
    $profile['s_phones'] = implode(',',$phones);
    fn_add_user_data_descriptions($profile);
  }
  
  
  $profile_fields = fn_get_profile_fields();
  $map =array();
  
  $map['s_address'] = 's_address';
  $map['s_city'] = 's_city';
  $map['s_state'] = 's_state_descr';
  $map['s_state_descr'] = '';
  $map['s_zipcode'] = 's_zipcode';
  $map['s_phones'] = 's_phones';
  
  foreach ($profile_fields['S'] as $pf) {
    if(isset($map[$pf['field_name']]))
      $map[$pf['field_name']] = $pf['description'];
  }
  
  $map['s_state_descr'] = $map['s_state'];
  unset($map['s_state']);
  
  Tygh::$app['view']->assign('profile_fields',$map);
  Tygh::$app['view']->assign('profiles',$profiles);
  
} else if ($mode == 'register') {

    if (!empty($auth['user_id'])) {
        return array(CONTROLLER_STATUS_REDIRECT, "profiles.update");
    }

    fn_add_breadcrumb(__('registration'));

    $user_data = array();
    if (!empty($_SESSION['cart']) && !empty($_SESSION['cart']['user_data'])) {
        $user_data = $_SESSION['cart']['user_data'];
    }

    $restored_user_data = fn_restore_post_data('user_data');
    if ($restored_user_data) {
        $user_data = fn_array_merge($user_data, $restored_user_data);
    }

    Registry::set('navigation.tabs.general', array (
        'title' => __('general'),
        'js' => true
    ));

    $params = array();
    if (isset($_REQUEST['user_type'])) {
        $params['user_type'] = $_REQUEST['user_type'];
    }
//	var_dump(fn_get_profile_fields('C', array(), CART_LANGUAGE, $params));
	// uelv mono thl kai afm
	/*$afm_id=fn_my_users_get_setting_field('afm');
	$params['field_id']=$afm_id;
	$afm_field = fn_get_profile_fields('C', array(), CART_LANGUAGE, $params);
	$tel_id=fn_my_users_get_setting_field('s_phones');
	$params['field_id']=$tel_id;
	$tel_field = fn_get_profile_fields('C', array(), CART_LANGUAGE, $params);
    $profile_fields = array('C'=>array($afm_id=>$afm_field),'S'=>array($tel_id=>$tel_field));
*/
  $params['get_profile_required']=true;
	$profile_fields = fn_get_profile_fields('C', array(), CART_LANGUAGE, $params);
	
    Registry::get('view')->assign('profile_fields', $profile_fields);
    Registry::get('view')->assign('user_data', $user_data);
   // Registry::get('view')->assign('ship_to_another', fn_check_shipping_billing($user_data, $profile_fields));
   // Registry::get('view')->assign('countries', fn_get_simple_countries(true, CART_LANGUAGE));
   // Registry::get('view')->assign('states', fn_get_all_states());

} elseif ($mode == 'add') {

    return array(CONTROLLER_STATUS_REDIRECT, "profiles.register");
    
} elseif ($mode == 'success_register') {
/*
    if (empty($auth['user_id'])) {
        return array(CONTROLLER_STATUS_REDIRECT, "profiles.register");
    }
*/
    fn_add_breadcrumb(__('registration'));
	
} elseif ($mode == 'delete_profile') { //not allowed

    return array(CONTROLLER_STATUS_DENIED);

} elseif ($mode == 'update') { //not allowed

    return array(CONTROLLER_STATUS_NO_PAGE);

}