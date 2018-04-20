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

if ($mode == 'm_delete') {

	if (!empty($_REQUEST['state_ids'])) {
		$bridge= new Sync\State();
		foreach ($_REQUEST['state_ids'] as $v) {
			$bridge->clear_shop_id($v); 
		}
	}
	
} elseif ($mode == 'delete') {

	if (!empty($_REQUEST['state_id'])) {
		$bridge= new Sync\State();
		$bridge->clear_shop_id($_REQUEST['state_id']); 
	}
}

