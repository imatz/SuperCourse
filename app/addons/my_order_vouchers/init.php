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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if (defined('IS_WINDOWS') && !defined('SYS_LOCALE_CHARSET')) { 
	$tmp = setlocale(LC_CTYPE, 0); 
	define('SYS_LOCALE_CHARSET', 'Windows-'.substr($tmp, -4)); 
}

fn_register_hooks(
 'get_orders',
 'get_orders_post'
);


Registry::set('config.storage.vouchers', array(
    'prefix' => 'vouchers',
    'secured' => true,
    'dir' => Registry::get('config.dir.var')
));