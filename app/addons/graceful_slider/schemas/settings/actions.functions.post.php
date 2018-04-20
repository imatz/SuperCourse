<?php
/*******************************
*                              *
*   (c) 2014 Cart-Power.com    *
*                              *
********************************/

use Tygh\Http;
use Tygh\Registry;
use Tygh\Settings;
use Tygh\Languages\Languages;

function fn_settings_actions_addons_slides_slide_multilang($new_value, $old_value)
{
    if ($new_value == 'N') {
        $lang_codes = Languages::getAll();
        unset($lang_codes[DEFAULT_LANGUAGE]);

        $slides_multilang = array();
        foreach ($lang_codes as $lang_code => $lang_data) {
            list($slides) = fn_get_slides(array(), $lang_code);

            foreach ($slides as $slide) {
                $slides_multilang[$lang_code][$slide['slide_id']] = $slide;
            }
        }

        list($slides) = fn_get_slides(array(), DEFAULT_LANGUAGE);

        foreach ($slides as $slide) {
            if ($slide['type'] != 'G') {
                continue;
            }

            $main_image_id = !empty($slide['main_pair']['image_id']) ? $slide['main_pair']['image_id'] : 0;

            foreach ($lang_codes as $lang_code => $lang_data) {
                $slide_lang = $slides_multilang[$lang_code][$slide['slide_id']];
                $lang_image_id = !empty($slide_lang['main_pair']['image_id']) ? $slide_lang['main_pair']['image_id'] : 0;

                if ($lang_image_id != 0 && ($main_image_id == 0 || $main_image_id != $lang_image_id)) {
                    fn_delete_image($lang_image_id, $slide_lang['main_pair']['pair_id'], 'grslide');
                    $lang_image_id = 0;
                }

                if ($lang_image_id == 0 && $main_image_id != 0) {
                    $data_slide_image = array(
                        'slide_id' => $slide['slide_id'],
                        'lang_code' => $lang_code
                    );
                    $slide_image_id = db_query("INSERT INTO ?:slide_images ?e", $data_slide_image);
                    fn_add_image_link($slide_image_id, $slide['main_pair']['pair_id']);

                    $data_desc = array (
                        'description' => empty($slide['main_pair']['icon']['alt']) ? '' : $slide['main_pair']['icon']['alt'],
                        'object_holder' => 'images'
                    );

                    fn_create_description('common_descriptions', 'object_id', $main_image_id, $data_desc);
                }

                db_query("UPDATE ?:slide_descriptions SET url = ?s WHERE slide_id = ?i", $slide['url'], $slide['slide_id']);
            }

        }

    }

    return true;
}

function fn_settings_actions_addons_graceful_slider(&$new_value, $old_value) {

    fn_cp_check_state($new_value, $old_value, ($_REQUEST['id'])?$_REQUEST['id']:$_REQUEST['addon']);

    return true;
}

if (function_exists('fn_cp_check_state') != true) {
    function fn_cp_check_state($new_value, $old_value, $name) {
        $store_ip = fn_get_ip();
        $store_ip = $store_ip['host'];
        $extra_fields = array();
        $_REQUEST = array(
            'addon_status' => $new_value,
            'ver' => PRODUCT_VERSION,
            'product_status' => PRODUCT_STATUS,
            'product_build' => strtoupper(PRODUCT_BUILD),
            'edition' => PRODUCT_EDITION,
            'lang' => strtoupper(CART_LANGUAGE),
            'store_uri' => fn_url('', 'C', 'http'),
            'secure_store_uri' => fn_url('', 'C', 'https'),
            'https_enabled' => (Registry::get('settings.General.secure_checkout') == 'Y' || Registry::get('settings.General.secure_admin') == 'Y' || Registry::get('settings.General.secure_auth') == 'Y') ? 'Y' : 'N',
            'admin_uri' => fn_url('', 'A', 'http'),
            'store_host' => Registry::get('config.http_host'),
            'store_ip' => $store_ip,
            'addon' => $name,
            'license' => Registry::get('addons.graceful_slider.licensekey')
        );
        
        $request = json_encode($_REQUEST);

        $check_host = "http://cart-power.com/index.php?dispatch=check_license.check_status";

        $data = Http::get($check_host, array('request' => urlencode($request)), array(
            'timeout' => 60
        )); 
        
        preg_match('/\<status\>(.*)\<\/status\>/u', $data, $result);
          
        $_status = 'FALSE';
        if (isset($result[1])) {
          $_status = $result[1];
        }
            
        if ($_REQUEST['dispatch'] == 'addons.update_status' && $_status != 'TRUE') {
          db_query("UPDATE ?:addons SET status = ?s WHERE addon = ?s", 'D', $name);
          fn_set_notification('W', __('warning'), __('cp_your_license_is_not_valid'));        
          exit;
        }
        
        return true;
    }
}