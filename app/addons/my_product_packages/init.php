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
  'add_to_cart',
  'pre_get_cart_product_data',
  'get_cart_product_data_post',
  'pre_place_order',
  'delete_product_post',
  'get_product_price_post',
  'get_products_before_select',
  'get_products',
  'get_categories',
  'get_products_post',
  'promotion_apply_pre',
  'update_product_post',
  'gather_additional_product_data_before_discounts',
  'gather_additional_product_data_post',
  'gather_additional_products_data_params',
  'post_delete_user'
);

?>