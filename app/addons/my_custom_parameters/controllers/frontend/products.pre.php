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
use Tygh\BlockManager\ProductTabs;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'view' || $mode == 'quick_view') {

    if ((empty($_SESSION['current_category_id']) || empty($product['category_ids'][$_SESSION['current_category_id']])) && !empty($product['main_category'])) {
        if (!empty($_SESSION['breadcrumb_category_id']) && in_array($_SESSION['breadcrumb_category_id'], $product['category_ids'])) {
            $_SESSION['current_category_id'] = $_SESSION['breadcrumb_category_id'];
        } else {
            $_SESSION['current_category_id'] = $product['main_category'];
        }
    }

    if (!empty($_SESSION['current_category_id'])) $redirect="categories.view?category_id={$_SESSION['current_category_id']}";
	else $redirect='index.php';
	
	return array(CONTROLLER_STATUS_REDIRECT, $redirect);    
	
}