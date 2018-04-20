<?php
/***************************************************************************
*                                                                          *
*           (c) 2014 HungryWeb.net | Support at yum@hungryweb.net          *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
***************************************************************************/

if ( !defined('BOOTSTRAP') ) { die('Access denied'); }

use Tygh\Registry;
use Tygh\Http;

#Hungryweb actions
function fn_hw_popup_info_install(){ fn_hw_aiden_action('popup_info','install'); }
function fn_hw_popup_info_install(){ fn_hw_aiden_action('popup_info','uninstall'); }
if (!function_exists('fn_hw_aiden_action')){
    function fn_hw_aiden_action($addon,$a){
        $request = array('addon'=>$addon,'host'=>Registry::get('config.http_host'),'path'=>Registry::get('config.http_path'),'version'=>PRODUCT_VERSION,'edition'=>PRODUCT_EDITION,'lang'=>strtoupper(CART_LANGUAGE),'a'=>$a,'love'=>'aiden');
        Http::post('https://www.hwebcs.com/ws/addons', $request);
    }
}