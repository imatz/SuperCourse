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

$cart = & $_SESSION['cart'];

// gemise to tim_lian symfvna me to sygxronismeno pedio
if (empty($cart['tim_lian'])) {
	$cart_tim_lian='L';
	if (!empty($auth['tim_lian']))  $cart_tim_lian = substr($auth['tim_lian'],0,1);
	$cart['tim_lian']=$cart_tim_lian;
}

	