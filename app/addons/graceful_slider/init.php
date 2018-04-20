<?php
/*******************************
*                              *
*   (c) 2014 Cart-Power.com    *
*                              *
********************************/

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
	'delete_image_pre',
    'update_language_post',
    'delete_languages_post'
);

if (!fn_allowed_for('ULTIMATE:FREE')) {
    fn_register_hooks(
        'localization_objects'
    );
}

if (fn_allowed_for('ULTIMATE')) {
    fn_register_hooks(
        'delete_company',
        'ult_check_store_permission'
    );
}
