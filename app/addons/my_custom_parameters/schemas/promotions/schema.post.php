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

$schema['conditions']['package'] = array (
	'operators' => array ('eq', 'neq'),
	'type' => 'input',
	'field' => '@product.package',
	//'field_function' => array('fn_my_product_packages_get_package_field', '@product'),
	'zones' => array('catalog')
);

$schema['conditions']['account_type'] = array(
	'operators' => array ('eq'),
	'type' => 'select',
	'variants_function' => array('fn_my_users_get_account_types'),
	'field_function' => array('fn_my_users_get_account_type', '@auth'),
	'zones' => array('catalog')
);

return $schema;