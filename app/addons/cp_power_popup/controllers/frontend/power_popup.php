<?php
use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if($mode == 'submit') {
	if (empty($_REQUEST['popup_id'])) {
		echo 'false';
		exit;
	}
	$popup_id = $_REQUEST['popup_id'];
	$popup = fn_cp_get_popup_data($popup_id);

	setcookie("power_popup_" . $popup_id, "power_popup_" . $popup_id, time() + (60 * 60 * $popup['ttl']), '/');
	echo 'true';
	exit;
}

if($mode == 'verify_age') {
	if (empty($_REQUEST['popup_id'])) {
		echo 'false';
		exit;
	}
	
	$popup_id = $_REQUEST['popup_id'];
	$popup = fn_cp_get_popup_data($popup_id);

	if(!empty($_REQUEST['age'])) {
		list($day, $month, $year) = explode('/', $_REQUEST['age']);
		$birth = mktime(0, 0, 0, $month, $day, $year);
		$age = intval((time() - $birth) / 31536000);

		if(!empty($age) && $age >= $popup['age_limit']) {
			setcookie("power_popup_" . $popup_id, "power_popup_" . $popup_id, time() + (60 * 60 * $popup['ttl']), '/');
			echo 'true';
			exit;
		} else {
		    if (!empty($popup['redirect_url'])) {
	            $url = fn_url($popup['redirect_url']);
	            Registry::get('ajax')->assign('force_redirection', $url);
                exit;
           } else {
                echo 'false';
                exit;
			}
		}
	} else {
        if (!empty($popup['redirect_url'])) {
            $url = fn_url($popup['redirect_url']);
            Registry::get('ajax')->assign('force_redirection', $url); 
            exit;
        } else {
            echo 'false';
            exit;
        }
	}
}