<?php

use Tygh\Settings;
use Tygh\Registry;
use Tygh\Http;

if (!defined('BOOTSTRAP')) { die('Access denied'); }
	  
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    fn_trusted_vars (
        'addon_data'
    );
    
    if ($mode == 'update') {
        if (isset($_REQUEST['addon_data'])) {
			if ($_REQUEST['addon'] == 'cp_floating_menu') {
				foreach($_REQUEST['addon_data']['options'] as $object_id => $val) {
					$data = db_get_row('SELECT name, value FROM ?:settings_objects WHERE object_id = ?i', $object_id);
					if ($data['name'] == 'licensekey') {
						$_REQUEST = array(
							'store_uri' => fn_url('', 'C', 'http'),
							'secure_store_uri' => fn_url('', 'C', 'https'),
							'addon' => $_REQUEST['addon'],
							'license' => $val
						);
		
						$request = json_encode($_REQUEST);
		
						$check_host = 'http://cart-power.com/index.php?dispatch=check_license.check_status_pre';

						$data = Http::get($check_host, array('request' => urlencode($request)), array(
							'timeout' => 60
						));
		  
						preg_match('/\<status\>(.*)\<\/status\>/u', $data, $result);
		  
						$_status = 'FALSE';
						if (isset($result[1])) {
							$_status = $result[1];
						}
			  
						if ($_status != 'TRUE') {
							db_query("UPDATE ?:addons SET status = ?s WHERE addon = ?s", 'D', $_REQUEST['addon']);
							fn_set_notification('W', __('warning'), __('cp_your_license_is_not_valid'));		  
							return array(CONTROLLER_STATUS_REDIRECT, 'addons.manage');
						}
					}
				}
			}
		}
    }
}