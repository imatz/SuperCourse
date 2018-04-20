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
use Sync\NoMailException;

if (!defined('BOOTSTRAP')) { die('Access denied'); }
/*
// bale to pricegroup sto cart
function fn_my_checkout_add_to_cart(&$cart, $product_id, $_id)
{
	
	$cart['products'][$_id]['pricegroup']=fn_get_product_pricegroup($product_id,$cart['products'][$_id]['amount'], $_SESSION['auth']);
	
}

function fn_get_product_pricegroup($product_id, $amount, &$auth)
{
    
    $usergroup_condition = db_quote("AND ?:product_prices.usergroup_id IN (?n)", ((AREA == 'C' || defined('ORDER_MANAGEMENT')) ? array_merge(array(USERGROUP_ALL), $auth['usergroup_ids']) : USERGROUP_ALL));

    $res = db_get_row(
        "SELECT ?:product_prices.usergroup_id, MIN(IF(?:product_prices.percentage_discount = 0, ?:product_prices.price, "
            . "?:product_prices.price - (?:product_prices.price * ?:product_prices.percentage_discount)/100)) as price "
        . "FROM ?:product_prices "
        . "WHERE lower_limit <=?i AND ?:product_prices.product_id = ?i ?p "
        . "GROUP BY ?:product_prices.usergroup_id "
        . "ORDER BY lower_limit DESC LIMIT 1",
        $amount, $product_id, $usergroup_condition
    );
	
	
	
	return (!empty($res))? $res['usergroup_id']: 0;
}
*/
function fn_my_checkout_send_mail_pre ($mailer, &$params, $area, $lang_code)
{
  if (!empty($params['tpl'])) {
    if ('orders/order_notification.tpl'==$params['tpl']) {
    //	$params['tpl']='addons/my_checkout/order_notification.tpl';
    } else	if ('profiles/update_profile.tpl'==$params['tpl']) {
      
    //	if (defined('NO_UPDATE_PROFILE_MAIL')) 	throw new Sync\NoMailException();
        
      if('profiles' == Registry::get('runtime.controller') && 'register' == Registry::get('runtime.mode')) {
        $params['tpl']='addons/my_users/register_profile.tpl';
      } else {
        $params['tpl']='addons/my_users/update_profile.tpl';
      }
    } else	if ('profiles/profile_activated.tpl'==$params['tpl']) {
        $params['tpl']='addons/my_users/profile_activated.tpl';
    }
  }
}

function fn_my_checkout_pre_update_order(&$cart, $order_id)
{
	$cart['user_data']['s_phones'] = implode(', ',$cart['user_data']['s_phones']);
}

function fn_my_checkout_place_order($order_id, $action, $order_status, &$cart, $auth)
{
	$cart['user_data']['s_phones'] = explode(', ',$cart['user_data']['s_phones']);
}


// copy of fn_checkout_update_steps
// removed email existence check on guest checkout
function fn_i_checkout_update_steps(&$cart, &$auth, $params)
{
    $redirect_params = array();

    $user_data = !empty($params['user_data']) ? $params['user_data'] : array();
    unset($user_data['user_type']);

    if (!empty($auth['user_id'])) {
        if (isset($user_data['profile_id'])) {
            if (empty($user_data['profile_id'])) {
                $user_data['profile_type'] = 'S';
            }
            $profile_id = $user_data['profile_id'];

        } elseif (!empty($cart['profile_id'])) {
            $profile_id = $cart['profile_id'];

        } else {
            $profile_id = db_get_field("SELECT profile_id FROM ?:user_profiles WHERE user_id = ?i AND profile_type = 'P'", $auth['user_id']);
        }

        $user_data['user_id'] = $auth['user_id'];
        $current_user_data = fn_get_user_info($auth['user_id'], true, $profile_id);
        if ($profile_id != NULL) {
            $cart['profile_id'] = $profile_id;
        }

        $errors = false;

        // Update contact information
        if (($params['update_step'] == 'step_one' || $params['update_step'] == 'step_two') && !empty($user_data['email'])) {
            // Check email
            $email_exists = fn_is_user_exists($auth['user_id'], $user_data);

            if (!empty($email_exists)) {
                fn_set_notification('E', __('error'), __('error_user_exists'));
                $redirect_params['edit_step'] = $params['update_step'];

                $errors = true;
                $params['next_step'] = $params['update_step'];
            }
        }

        // Update billing/shipping information
        if ($params['update_step'] == 'step_two' || $params['update_step'] == 'step_one' && !$errors) {
            if (!empty($user_data)) {
                $user_data = fn_array_merge($current_user_data, $user_data);
                $user_data['user_type'] = !empty($current_user_data['user_type']) ? $current_user_data['user_type'] : AREA;

                $user_data = fn_fill_contact_info_from_address($user_data);
            }

            $user_data = fn_array_merge($current_user_data, $user_data);

            if (empty($params['ship_to_another'])) {
                $profile_fields = fn_get_profile_fields('O');
                fn_fill_address($user_data, $profile_fields);
            }

            // Check if we need to send notification with new email to customer
            $email = db_get_field('SELECT email FROM ?:users WHERE user_id = ?i', $auth['user_id']);

            $send_notification = false;
            if (isset($user_data['email']) && $user_data['email'] != $email) {
                $send_notification = true;
            }

            list($user_id, $profile_id) = fn_update_user($auth['user_id'], $user_data, $auth, !empty($params['ship_to_another']), $send_notification, false);

            $cart['profile_id'] = $profile_id;
        }

        // Add/Update additional fields
        if (!empty($user_data['fields'])) {
            fn_store_profile_fields($user_data, array('U' => $auth['user_id'], 'P' => $profile_id), 'UP'); // FIXME
        }

    } elseif (Registry::get('settings.Checkout.disable_anonymous_checkout') != 'Y') {
       /* if (empty($auth['user_id']) && !empty($user_data['email'])) {

            $email_exists = fn_is_user_exists(0, $user_data);

            if (!empty($email_exists)) {
                fn_set_notification('E', __('error'), __('error_user_exists'));
                fn_save_post_data('user_data');

                if (!empty($params['guest_checkout'])) {
                    $redirect_params['edit_step'] = $params['step_two'];
                    $redirect_params['guest_checkout'] = 1;
                }

                return $redirect_params;
            }
        }
        */
        if (isset($user_data['fields'])) {
            $fields = fn_array_merge(isset($cart['user_data']['fields']) ? $cart['user_data']['fields'] : array(), $user_data['fields']);
        }

        if ($params['update_step'] == 'step_two' && !empty($user_data)) {
            $user_data = fn_fill_contact_info_from_address($user_data);
        }

        $cart['user_data'] = fn_array_merge($cart['user_data'], $user_data);

        // Fill shipping info with billing if needed
        if (empty($params['ship_to_another']) && $params['update_step'] == 'step_two') {
            $profile_fields = fn_get_profile_fields('O');
            fn_fill_address($cart['user_data'] , $profile_fields);
        }

        if (!empty($cart['user_data']['b_vat_id']) && !empty($cart['user_data']['b_country'])) {
            if (fn_check_vat_id($user_data['b_vat_id'], $cart['user_data']['b_country'])) {
                fn_set_notification('N', __('notice'), __('vat_id_number_is_valid'));
            } else {
                fn_set_notification('E', __('error'), __('vat_id_number_is_not_valid'));
                $cart['user_data']['b_vat_id'] = '';

                return $redirect_params;
            }
        } elseif (isset($user_data['b_vat_id'])) {
            $user_data['b_vat_id'] = '';
        }
    }

    if (!empty($params['next_step'])) {
        $redirect_params['edit_step'] = $params['next_step'];
    }

    if (!empty($params['shipping_ids'])) {
        fn_checkout_update_shipping($cart, $params['shipping_ids']);
    }

    if (!empty($params['payment_id'])) {
        $cart['payment_id'] = (int) $params['payment_id'];
        if (!empty($params['payment_info'])) {
            $cart['extra_payment_info'] = $params['payment_info'];
            if (!empty($cart['extra_payment_info']['card_number'])) {
                $cart['extra_payment_info']['secure_card_number'] = preg_replace('/^(.+?)([0-9]{4})$/i', '***-$2', $cart['extra_payment_info']['card_number']);
            }
        } else {
            unset($cart['extra_payment_info']);
        }

        fn_update_payment_surcharge($cart, $auth);
        fn_save_cart_content($cart, $auth['user_id']);
    }

    if (!empty($params['customer_notes'])) {
        $cart['notes'] = $params['customer_notes'];
    }

    // Recalculate the cart
    $cart['recalculate'] = true;

    if (!empty($params['next_step']) && ($params['next_step'] == 'step_three' || $params['next_step'] == 'step_four')) {
        $cart['calculate_shipping'] = true;
    }

    $shipping_calculation_type = (Registry::get('settings.General.estimate_shipping_cost') == 'Y' || !empty($completed_steps['step_two'])) ? 'A' : 'S';

    list ($cart_products, $product_groups) = fn_calculate_cart_content($cart, $auth, $shipping_calculation_type, true, 'F');

    $shipping_hash = fn_get_shipping_hash($cart['product_groups']);

    if (!empty($_SESSION['shipping_hash']) && $_SESSION['shipping_hash'] != $shipping_hash && $params['next_step'] == 'step_four' && $cart['shipping_required']) {
        if (!empty($cart['chosen_shipping'])) {
            fn_set_notification('W', __('important'), __('text_shipping_rates_changed'));
        }
        $cart['chosen_shipping'] = array();

        $redirect_params['edit_step'] = 'step_three';

        return $redirect_params;
    }

    return $redirect_params;
}