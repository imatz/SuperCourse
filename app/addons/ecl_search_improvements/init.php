<?php
/***************************************************************************
*                                                                          *
*                   All rights reserved! eCom Labs LLC                     *
*                                                                          *
****************************************************************************/

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'additional_fields_in_search',
	'get_users',
    'get_orders'
);
