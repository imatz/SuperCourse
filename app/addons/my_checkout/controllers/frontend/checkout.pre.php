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
use Tygh\Storage;
use Tygh\Session;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_enable_checkout_mode();

fn_define('ORDERS_TIMEOUT', 60);

// Cart is empty, create it
if (empty($_SESSION['cart'])) {
    fn_clear_cart($_SESSION['cart']);
}

$cart = & $_SESSION['cart'];
//fn_print_r($cart);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	// stoys registered janagemizv ta $_REQUEST['user_data'] me ta db gt den epitrepetai allagh
	if (!empty($auth['user_id']) && !empty($_REQUEST['user_data'])) {
        if (isset($_REQUEST['user_data']['profile_id'])) {
            if (empty($_REQUEST['user_data']['profile_id'])) {
                $_REQUEST['user_data']['profile_type'] = 'S';
            }
            $profile_id = $_REQUEST['user_data']['profile_id'];

        } elseif (!empty($cart['profile_id'])) {
            $profile_id = $cart['profile_id'];

        } else {
            $profile_id = db_get_field("SELECT profile_id FROM ?:user_profiles WHERE user_id = ?i AND profile_type = 'P'", $auth['user_id']);
        }

        $user_data = fn_get_user_info($auth['user_id'], true, $profile_id);
		
		
		if (empty($user_data['lastname'])) $user_data['lastname']='.';
		//mono ayta epitrepontai
		$delivery_notes_field_id=fn_my_users_get_setting_field('s_delivery_notes');
		if (isset($_REQUEST['user_data']['fields'][$delivery_notes_field_id])) // balto kai sto cart gia na katsei sthn paraggelia
			$user_data['fields'][$delivery_notes_field_id] = $cart['delivery_notes'] = $_REQUEST['user_data']['fields'][$delivery_notes_field_id];
		if (isset($_REQUEST['user_data']['s_firstname'])) $user_data['s_firstname']=$_REQUEST['user_data']['s_firstname'];
		if (isset($_REQUEST['user_data']['s_lastname'])) $user_data['s_lastname']=$_REQUEST['user_data']['s_lastname'];
		if (isset($_REQUEST['user_data']['s_phone'])) $user_data['s_phone']=$_REQUEST['user_data']['s_phone'];
		//an ues na gemiseis ta billing balto edv
		
		// telos. perna ta apo panv
		$_REQUEST['user_data']=$user_data;
	}
  
   // fill s_firstname and s_lastname by default
    if (empty($auth['user_id'])) {
      if (!empty($_REQUEST['user_data']['firstname'])  && empty($cart['user_data']['s_firstname'])) 
        $cart['user_data']['s_firstname']=$_REQUEST['user_data']['firstname'];
      if (!empty($_REQUEST['user_data']['lastname'])  && empty($cart['user_data']['s_lastname'])) 
        $cart['user_data']['s_lastname']=$_REQUEST['user_data']['lastname'];
    }
	
	if ($mode == 'update_steps') {

		// an epitrepetai allaje to tim_lian
		if (!empty($_REQUEST['tim_lian']) && in_array($auth['tim_lian'], array("TL","LT"))) {
			$cart['tim_lian'] = $_REQUEST['tim_lian'];
		}

		/*Stathis Liampas---prosthiki periptwsewn opou o xristis den exei epilogh parastatikoy,ara i paraggelia prepei na parei to prokathorismeno parastatiko tou xristi */

		//an einai Timologio,tote i paraggelia kobetai ws Timologio
		if (!empty($_REQUEST['tim_lian']) && in_array($auth['tim_lian'], array("T"))) {
			$cart['tim_lian'] = "T";
		}

		//an einai Apodeiksi Lianikhs,totei paraggelia kobetai ws Apodeiksh Lianikhs
		if (!empty($_REQUEST['tim_lian']) && in_array($auth['tim_lian'], array("L"))) {
			$cart['tim_lian'] = "L";
		}

		/*Stathis Liampas----Allages mexri edw */
		
		if (!empty($_REQUEST['product_notes'])) { 
			foreach($_REQUEST['product_notes'] as $cart_id=>$note) {
				$cart['products'][$cart_id]['extra']['notes']=$note;
			}
		}
		
		fn_save_cart_content($cart, $auth['user_id']);
    
		$redirect_params = fn_i_checkout_update_steps($cart, $auth, $_REQUEST); // disabled email check on guest checkout
	
	
		// an to fmail yparxei emfanise mnm gia syndesh h synexeia vs pelaths lianikhs
		if (empty($auth['user_id']) && 'step_one' == $_REQUEST['update_step'] && !empty($_REQUEST['user_data']['email'])) {
			$account_exists_and_enabled = fn_fmail_user_exists($_REQUEST['user_data']['email']);
			if ($account_exists_and_enabled) {
				Tygh::$app['view']->assign('checkout_account_exists',__('checkout_email_exists_info'));
			}
		}

		return array(CONTROLLER_STATUS_REDIRECT, 'checkout.checkout?' . http_build_query($redirect_params));
	}
  
   
    
} elseif ($mode=='checkout') {
	if (empty($auth['user_id']) ){
	//	if(empty($cart['guest_checkout'])) {
	//		$cart['user_data']['email']=' '; // gia na mhn paei sto arxiko bhma epiloghs
			$cart['guest_checkout']=true;
	//		fn_set_notification('E', __('error'),2);
	//	}
	} else {
		unset($cart['guest_checkout']);
	}
	
}
