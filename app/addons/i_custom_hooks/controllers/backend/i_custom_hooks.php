<?php
//
// Author Ioannis Matziaris [imatz] - imatzgr@gmail.com - February 2014
//

use Tygh\Registry;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$suffix = '';
	fn_trusted_vars (
        'hook_data'
    );

	if ($mode == 'update') {
		if(!isset($_REQUEST["hook_data"]) || empty($_REQUEST["hook_data"])){
			return array(CONTROLLER_STATUS_OK, "i_custom_hooks.manage");
		}
		if(isset($_REQUEST["hook_id"]) && !empty($_REQUEST["hook_id"])){
			$prv_hook_data=fn_get_custom_hook($_REQUEST["hook_id"]);
		}

		$hook_id=fn_update_custom_hook($_REQUEST["hook_id"],$_REQUEST["hook_data"]);

		if(isset($_REQUEST["hook_id"]) && !empty($_REQUEST["hook_id"]) && $prv_hook_data["installed"]=="S"){
			$hook_data=fn_get_custom_hook($hook_id);
			$install_hook=false;
			foreach($prv_hook_data as $field=>$value){
				if($field!="custom_hook_id"&&$field!="timestamp"&&$field!="installed"){
					if($value!=$hook_data[$field]){
						$install_hook=true;
						break;
					}
				}
			}
			if($install_hook){
				$uninstall_result=fn_uninstall_custom_hook($hook_id,$prv_hook_data);
				if($uninstall_result=="S"){
					$install_result=fn_install_custom_hook($hook_id);
				}
				if($uninstall_result=="S"){
					fn_set_notification('N', __('notice'), __('text_successful_hook_uninstall'));
					if($install_result=="S"){
						db_query("UPDATE ?:i_custom_hooks SET installed=?s WHERE custom_hook_id=?i",$install_result,$hook_id);
						fn_set_notification('N', __('notice'), __('text_successful_hook_install'));
					}elseif($result=="FS"){
						fn_set_notification('E', __('error'), __("text_error_file_does_not_exist"));
					}elseif($result=="OHP"){
						fn_set_notification('E', __('error'), __("text_error_open_hook_position_not_detected_or_empty"));
					}elseif($result=="CHP"){
						fn_set_notification('E', __('error'), __("text_error_close_hook_position_not_detected"));
					}else{
						fn_set_notification('E', __('error'), __('text_error_hook_install'));
					}
				}else{
					fn_set_notification('E', __('error'), __('text_error_hook_uninstall'));
				}
			}
		}
		if (!empty($hook_id)) {
            $suffix = ".update?hook_id=$hook_id" . (!empty($_REQUEST['hook_data']['block_id']) ? "&selected_block_id=" . $_REQUEST['hook_data']['block_id'] : "");
        } else {
            $suffix = '.manage';
        }
	}
	if ($mode == 'm_install') {
		if (isset($_REQUEST['hook_ids'])) {
			$successful_installs=0;
			$faild_installs=0;
			foreach ($_REQUEST['hook_ids'] as $hook_id) {
				$result=fn_install_custom_hook($hook_id);
				db_query("UPDATE ?:i_custom_hooks SET installed=?s WHERE custom_hook_id=?i",$result,$hook_id);
				if($result=="S"){
					$successful_installs++;
				}else{
					$faild_installs++;
				}				
			}
			$notification_text=__("selected_install_hooks_result_notification");
			$r_what=array("[successful_installs]","[faild_installs]","[total_installs]");
			$r_with=array($successful_installs,$faild_installs,count($_REQUEST['hook_ids']));
			$notification_text=str_replace($r_what, $r_with, $notification_text);
			fn_set_notification('N', __('notice'), $notification_text);
			fn_clear_cache();
		}	
		$suffix = '.manage';
	}
	if ($mode == 'm_uninstall') {
		if (isset($_REQUEST['hook_ids'])) {
			$successful_uninstalls=0;
			$faild_uninstalls=0;
			foreach ($_REQUEST['hook_ids'] as $hook_id) {
				$result=fn_uninstall_custom_hook($hook_id);				
				if($result=="S"){
					db_query("UPDATE ?:i_custom_hooks SET installed=?s WHERE custom_hook_id=?i","N",$hook_id);
					$successful_uninstalls++;
				}elseif($result=="F"){
					$faild_uninstalls++;
				}	
			}
			$notification_text=__("selected_uninstall_hooks_result_notification");
			$r_what=array("[successful_uninstalls]","[faild_uninstalls]","[total_uninstalls]");
			$r_with=array($successful_uninstalls,$faild_uninstalls,count($_REQUEST['hook_ids']));
			$notification_text=str_replace($r_what, $r_with, $notification_text);
			fn_set_notification('N', __('notice'), $notification_text);
			fn_clear_cache();
		}
		$suffix = '.manage';
	}
	if ($mode == 'm_delete') {
		if (isset($_REQUEST['hook_ids'])) {
			$successful_deletes_uninstalls=0;
			$successful_deletes=0;
			$faild_uninstalls=0;
			$faild_delete=0;
			foreach ($_REQUEST['hook_ids'] as $hook_id) {
				$hook_data=fn_get_custom_hook($hook_id,array());
				$uninstall=($hook_data["installed"]=="S");
        		$result=fn_delete_custom_hooks($hook_id,$uninstall);
        		if($uninstall){
			    	if($result=="SD"){
			    		$successful_deletes_uninstalls++;
			    	}else{
			    		$faild_uninstalls++;
			    	}
			    }else{
			    	if($result=="SD"){
			    		$successful_deletes++;
			    	}else{
			    		$faild_delete++;
			    	}
			    }
			}
			$notification_text=__("selected_delete_hooks_result_notification");
			$r_what=array("[successful_deletes_uninstalls]","[successful_deletes]","[faild_uninstalls]","[faild_delete]","[total_deletes]");
			$r_with=array($successful_deletes_uninstalls,$successful_deletes,$faild_uninstalls,$faild_delete,count($_REQUEST['hook_ids']));
			$notification_text=str_replace($r_what, $r_with, $notification_text);
			fn_set_notification('N', __('notice'), $notification_text);
			fn_clear_cache();
		}
		$suffix = '.manage';
	}
	if ($mode == 'm_check_installation') {
		if (isset($_REQUEST['hook_ids'])) {
			$installed_hooks=0;
			$not_installed_hooks=0;
			foreach ($_REQUEST['hook_ids'] as $hook_id) {
				$result=fn_check_custom_hook_installation($hook_id);
				if($result){
					$installed_hooks++;
				}else{
					$not_installed_hooks++;
				}
			}
			$notification_text=__("selected_is_installed_hooks_result_notification");
			$r_what=array("[installed_hooks]","[not_installed_hooks]","[total_hooks]");
			$r_with=array($installed_hooks,$not_installed_hooks,count($_REQUEST['hook_ids']));
			$notification_text=str_replace($r_what, $r_with, $notification_text);
			fn_set_notification('N', __('notice'), $notification_text);
		}
		$suffix = '.manage';
	}
	if(!empty($suffix)){
		return array(CONTROLLER_STATUS_OK, "i_custom_hooks$suffix");
	}else{
		return;
	}
}

if ($mode == 'manage'){
	$custom_hooks=fn_get_custom_hooks($_REQUEST);
	Registry::get('view')->assign('custom_hooks', $custom_hooks);	
}elseif ($mode == 'add'){
	//TODO
} elseif ($mode == 'update') {
	$hook_id=isset($_REQUEST["hook_id"])?$_REQUEST["hook_id"]:0;

	if(empty($hook_id)){
		return array(CONTROLLER_STATUS_NO_PAGE);
	}
	$params=$_REQUEST;
	 
	$hook_data=fn_get_custom_hook($hook_id,$params);

	Registry::get('view')->assign('hook_data', $hook_data);

}elseif ($mode == 'delete') {
	$uninstall=false;
	$caller=isset($_REQUEST["caller"])?$_REQUEST["caller"]:"";
	$hook_id=isset($_REQUEST["hook_id"])?$_REQUEST["hook_id"]:0;
	if (!empty($hook_id)) {
		$hook_data=fn_get_custom_hook($hook_id,array());
		$uninstall=($hook_data["installed"]=="S");
        $result=fn_delete_custom_hooks($hook_id,$uninstall);
    }
    if($uninstall){
    	if($result=="SD"){
    		fn_set_notification('N', __('notice'), __('text_hook_has_been_uninstalled_deleted'));
    	}else{
    		fn_set_notification('N', __('notice'), __('text_hook_hasnot_been_deleted_because_of_unsuccessful_uninstall'));
    	}
    }else{
    	if($result=="SD"){
    		fn_set_notification('N', __('notice'), __('text_hook_has_been_deleted'));
    	}else{
    		fn_set_notification('E', __('error'), __('text_hook_hasnot_been_deleted'));
    	}
    }

	return array(CONTROLLER_STATUS_REDIRECT, "i_custom_hooks.manage");
}elseif($mode=="install"){
	$hook_id=isset($_REQUEST["hook_id"])?$_REQUEST["hook_id"]:0;
	$caller=isset($_REQUEST["caller"])?$_REQUEST["caller"]:"";
	if(!empty($hook_id)){
		$result=fn_install_custom_hook($hook_id);
		if($result=="S"){
			db_query("UPDATE ?:i_custom_hooks SET installed=?s WHERE custom_hook_id=?i",$result,$hook_id);
			fn_clear_cache();
			fn_set_notification('N', __('notice'), __('text_successful_hook_install'));
		}elseif($result=="FS"){
			fn_set_notification('E', __('error'), __("text_error_file_does_not_exist"));
		}elseif($result=="OHP"){
			fn_set_notification('E', __('error'), __("text_error_open_hook_position_not_detected_or_empty"));
		}elseif($result=="CHP"){
			fn_set_notification('E', __('error'), __("text_error_close_hook_position_not_detected"));
		}else{
			fn_set_notification('E', __('error'), __('text_error_hook_install'));
		}
	}
	if($caller=="update"){
		return array(CONTROLLER_STATUS_REDIRECT, "i_custom_hooks.update?hook_id=$hook_id" . (!empty($_REQUEST['hook_data']['block_id']) ? "&selected_block_id=" . $_REQUEST['hook_data']['block_id'] : ""));
	}else{
		return array(CONTROLLER_STATUS_REDIRECT, "i_custom_hooks.manage");
	}
}elseif($mode=="uninstall"){
	$hook_id=isset($_REQUEST["hook_id"])?$_REQUEST["hook_id"]:0;
	$caller=isset($_REQUEST["caller"])?$_REQUEST["caller"]:"";
	if(!empty($hook_id)){		
		$result=fn_uninstall_custom_hook($hook_id);
		if($result=="S"){			
			fn_set_notification('N', __('notice'), __('text_successful_hook_uninstall'));
		}elseif($result=="F"){	
			fn_set_notification('E', __('error'), __('text_error_hook_uninstall'));
		}else{
			fn_set_notification('W', __('warning'), __('text_hook_already_uninstalled'));
		}
	}
	if($caller=="update"){
		return array(CONTROLLER_STATUS_REDIRECT, "i_custom_hooks.update?hook_id=$hook_id" . (!empty($_REQUEST['hook_data']['block_id']) ? "&selected_block_id=" . $_REQUEST['hook_data']['block_id'] : ""));
	}else{
		return array(CONTROLLER_STATUS_REDIRECT, "i_custom_hooks.manage");
	}
}elseif($mode=="check_install"){
	$hook_id=isset($_REQUEST["hook_id"])?$_REQUEST["hook_id"]:0;
	$caller=isset($_REQUEST["caller"])?$_REQUEST["caller"]:"";
	if(!empty($hook_id)){
		$result=fn_check_custom_hook_installation($hook_id);
		if($result){
			fn_set_notification('N', __('notice'), __('text_custom_hook_is_installed'));
		}else{
			fn_set_notification('E', __('error'), __('text_custom_hook_is_not_installed'));
		}
	}
	if($caller=="update"){
		return array(CONTROLLER_STATUS_REDIRECT, "i_custom_hooks.update?hook_id=$hook_id" . (!empty($_REQUEST['hook_data']['block_id']) ? "&selected_block_id=" . $_REQUEST['hook_data']['block_id'] : ""));
	}else{
		return array(CONTROLLER_STATUS_REDIRECT, "i_custom_hooks.manage");
	}
}