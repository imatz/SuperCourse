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


//
// $Id$
//

if ( !defined('AREA') ) { die('Access denied'); }



fn_register_hooks(
      'calculate_cart_items',
      'generate_cart_id',
      'pre_add_to_cart',
      'post_add_to_cart',
      'delete_cart_product',
      'apply_options_rules_post',
      'get_product_data_post',
      'gather_additional_product_data_post',	
      'clone_product_post',
      'delete_product_post',
      'add_product_to_cart_check_price',
      'get_products_before_select',
      'pre_place_order',
      'get_cart_product_data',
      'reorder',
      'update_cart_products_pre'
);

?>