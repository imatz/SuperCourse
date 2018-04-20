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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	if ($mode == 'add' && $action == 'package' && !empty($_REQUEST['package_code'])) {
		$package_id=fn_my_product_packages_get_package_id($_REQUEST['package_code']);
		if (empty($package_id)) {
			fn_set_notification('E', __('error'), __('text_no_packages'));
		} else {
			$_REQUEST['product_data'][$package_id]=array('product_id'=>$package_id, 'amount'=>1);
		}
		
	}
	
}
