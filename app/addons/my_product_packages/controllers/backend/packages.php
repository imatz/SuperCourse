<?php

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$_REQUEST['product_id'] = empty($_REQUEST['product_id']) ? 0 : $_REQUEST['product_id'];

if (fn_allowed_for('MULTIVENDOR')) {
    if (
        isset($_REQUEST['product_id']) && !fn_company_products_check($_REQUEST['product_id'])
        ||
        isset($_REQUEST['product_ids']) && !fn_company_products_check($_REQUEST['product_ids'])
    ) {
        return array(CONTROLLER_STATUS_DENIED);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $suffix = '';
  // Processing deleting of multiple product elements
  //
  if ($mode == 'm_delete') {
      if (isset($_REQUEST['product_ids'])) {
          foreach ($_REQUEST['product_ids'] as $v) {
              fn_delete_product($v);
          }
      }
      unset($_SESSION['product_ids']);
      fn_set_notification('N', __('notice'), __('text_products_have_been_deleted'));
      $suffix = ".manage";
  }
    
  return array(CONTROLLER_STATUS_OK, 'packages' . $suffix);
}

if ($mode == 'manage' ) {
/*    unset($_SESSION['product_ids']);
    unset($_SESSION['selected_fields']);
*/
    $params = $_REQUEST;

    $params['packages']='Y';
	
    if (fn_allowed_for('ULTIMATE')) {
        $params['extend'][] = 'sharing';
    }


    list($products, $search) = fn_get_products($params, Registry::get('settings.Appearance.admin_products_per_page'), DESCR_SL);
    fn_gather_additional_products_data($products, array('get_discounts' => true));
    fn_my_product_packages_get_retail_data($products);
    fn_my_product_packages_get_package_data_no_discount($products);
//fn_print_r($products);
    $page = $search['page'];
    $valid_page = db_get_valid_page($page, $search['items_per_page'], $search['total_items']);

    if ($page > $valid_page) {
        $_REQUEST['page'] = $valid_page;

        return array(CONTROLLER_STATUS_REDIRECT, Registry::get('config.current_url'));
    }

    Tygh::$app['view']->assign('show_package_retail_price', 'Y');
    Tygh::$app['view']->assign('products', $products);
    Tygh::$app['view']->assign('search', $search);

} else if ($mode == 'suggest') {
  echo json_encode(fn_my_product_packages_suggest_title($_GET['term']));
  exit;
}