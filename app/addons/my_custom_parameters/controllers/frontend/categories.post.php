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

$_REQUEST['category_id'] = empty($_REQUEST['category_id']) ? 0 : $_REQUEST['category_id'];

if ($mode == 'view') {
	$subcategories=fn_get_subcategories($_REQUEST['category_id']);
	if (empty($subcategories)) {
		$path=db_get_field("SELECT id_path FROM ?:categories WHERE category_id = ?i", $_REQUEST['category_id']);
		$category_parent_ids = fn_explode('/', $path);
		array_pop($category_parent_ids);
		$parent_id=array_pop($category_parent_ids);
		// Get siblings list for current category
		if (!empty($parent_id)) Tygh::$app['view']->assign('subcategories', fn_get_subcategories($parent_id));
	}        
}