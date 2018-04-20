<?php
//
// Author Ioannis Matziaris [imatz] - imatzgr@gmail.com - Fberuary 2014
//
// My Custom Hooks
//

use Tygh\Registry;
use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

//
// Addon Settings
//

function fn_i_custom_hooks_setting_field($field_name,$default_value=''){
	$value='';
	$settings=Registry::get('addons.i_custom_hooks');
	
	if(isset($settings[$field_name])){
		$value=$settings[$field_name];
	}
	
	if(empty($value)){
		$value=$default_value;
	}
	
	return($value);
}

function fn_get_custom_hooks($params){
	$custom_hooks=db_get_array("SELECT * FROM ?:i_custom_hooks ORDER BY file;");
	return($custom_hooks);
}

function fn_get_custom_hook($hooks_id,$params=array()){
	$custom_hook=db_get_row("SELECT * FROM ?:i_custom_hooks WHERE custom_hook_id=?i",$hooks_id);
	return($custom_hook);
}

function fn_update_custom_hook($hook_id,$hook_data){
	if(empty($hook_id)){
		$_data=$hook_data;
		$_data["timestamp"]=time();
		$hook_id=db_query("INSERT INTO ?:i_custom_hooks ?e", $_data);
	}else{
		//If is installed then uninstall and install again
		$_data=$hook_data;
		db_query("UPDATE ?:i_custom_hooks SET ?u WHERE custom_hook_id = ?i", $_data, $hook_id);
	}
	return($hook_id);
}

function fn_delete_custom_hooks($hook_id,$uninstall=false){

	if($uninstall){
		//Uninstall the hook
		$uninstall_result=fn_uninstall_custom_hook($hook_id);
		if($uninstall_result=="F"){
			return("UF");
		}
	}

	db_query("DELETE FROM ?:i_custom_hooks WHERE custom_hook_id = ?i",$hook_id);
	return("SD");
}

function fn_check_custom_hook_installation($hook_id){
	$hook_data=fn_get_custom_hook($hook_id);
	$path=DIR_ROOT."/".$hook_data['file'];
	$path=str_replace("[theme_name]", Settings::instance()->getValue('theme_name', '', 0), $path);

	//Elegxos an to arxeio iparxei
	if(!file_exists($path)){
		return(false);
	}

	$installed=false;
	$open_hook_installed=false;
	$close_hook_installed=false;

    $text = file_get_contents($path);

    if (!empty($hook_data['open_hook'])){
    	$_open_hook="";
    	if(strpos($text,$hook_data['open_hook']."\n\n")!==false){
    		$_open_hook=$hook_data['open_hook']."\n\n";
    	}elseif(strpos($text,$hook_data['open_hook']."\n")!==false){
    		$_open_hook=$hook_data['open_hook']."\n";
    	}elseif(strpos($text,$hook_data['open_hook'])!==false){
    		$_open_hook=$hook_data['open_hook'];
    	}
    	if(!empty($_open_hook)){
        	$open_hook_installed=true;     
    	}
    }
    if (!empty($hook_data['close_hook'])){
    	$_close_hook="";
    	if(strpos($text,$hook_data['close_hook']."\n\n")!==false){
    		$_close_hook=$hook_data['close_hook']."\n\n";
    	}elseif(strpos($text,$hook_data['close_hook']."\n")!==false){
    		$_close_hook=$hook_data['close_hook']."\n";
    	}elseif(strpos($text,$hook_data['close_hook'])!==false){
    		$_close_hook=$hook_data['close_hook'];
    	}
    	if(!empty($_close_hook)){ 	
        	$close_hook_installed=true;     
    	}
    }
    if (!empty($hook_data['open_hook'])&&$open_hook_installed&&!empty($hook_data['close_hook'])&&$close_hook_installed){
    	$installed=true; 
    }elseif(!empty($hook_data['open_hook'])&&$open_hook_installed&&empty($hook_data['close_hook'])){
    	$installed=true; 
    }elseif(!empty($hook_data['close_hook'])&&$close_hook_installed&&empty($hook_data['open_hook'])){
    	$installed=true; 
    }

    db_query("UPDATE ?:i_custom_hooks SET installed=?s WHERE custom_hook_id=?i",($installed?"S":"N"),$hook_id);

    //TODO: Epistrofei akrivos pou emfanistike to provlima: den eiparxei arxeio, den egine aferesei open i close hook
    return($installed);
}

function fn_install_custom_hook($hook_id,$hook_data=array()){
	if(empty($hook_data)){
		$hook_data=fn_get_custom_hook($hook_id);
	}

	$path=DIR_ROOT."/".$hook_data['file'];
	$path=str_replace("[theme_name]", Settings::instance()->getValue('theme_name', '', 0), $path);

	//Elegxos an to arxeio iparxei
	if(!file_exists($path)){
        return("FS");
    }

    $file_content = file_get_contents($path);

    //Elegxos an iparxoun sto arxeio ta simio prosthikis tis open hook kai tou close hook efoson exei oristei
    if (empty($hook_data['open_hook_position'])||strpos($file_content,$hook_data['open_hook_position'])===false){
        return("OHP");
    }
    if (!empty($hook_data['close_hook_position'])&&strpos($file_content,$hook_data['close_hook_position'])===false){
        return("CHP");
    }
    $result_add_open_hook=false;
    $result_add_close_hook=false;

    if(empty($hook_data['open_occurrence'])) $hook_data['open_occurrence']=0;
    if(empty($hook_data['close_occurrence'])) $hook_data['close_occurrence']=0;

    //Open Hook
    if(!empty($hook_data['open_hook_position'])&&!empty($hook_data['open_hook'])&&
    	(empty($hook_data['open_occurrence'])||substr_count($file_content,$hook_data['open_hook_position'])>=$hook_data['open_occurrence'])){

    	$replace_with="";
    	if($hook_data['open_hook_order']=="B"){
		    $replace_with=$hook_data['open_hook_position']."\n\n".$hook_data['open_hook'];
		}else{
		  	$replace_with=$hook_data['open_hook']."\n\n".$hook_data['open_hook_position'];
		}
	    if (strpos($file_content,$replace_with)===false){//Den iparxei to hook anigmatos
	    	if(!empty($hook_data['open_occurrence'])){
	    		$file_new_content = str_replace_nth($hook_data['open_hook_position'],$replace_with,$file_content,$hook_data['open_occurrence']);
	    	}else{
	    		$file_new_content = str_replace($hook_data['open_hook_position'],$replace_with,$file_content);
	    	}
		    $result_add_open_hook=file_put_contents($path, $file_new_content, LOCK_EX);
		}  
	}

	//Close Hook
	$file_content = file_get_contents($path);
	if(!empty($hook_data['close_hook_position'])&&!empty($hook_data['close_hook'])&&
    	(empty($hook_data['close_occurrence'])||substr_count($file_content,$hook_data['close_hook_position'])>=$hook_data['close_occurrence'])){

		$replace_with="";
		if($hook_data['close_hook_order']=="A"){
		  	$replace_with=$hook_data['close_hook']."\n\n".$hook_data['close_hook_position'];	    	
		}else{
		 	$replace_with=$hook_data['close_hook_position']."\n\n".$hook_data['close_hook'];
		}		
		if($result_add_open_hook||strpos($file_content,$replace_with)===false){		
		    if (strpos($file_content,$replace_with)===false){//den iparxei idi to close hook sto arxeio		
		    	if(!empty($hook_data['close_occurrence'])){
		    		$file_new_content = str_replace_nth($hook_data['close_hook_position'],$replace_with,$file_content,$hook_data['close_occurrence']);
		    	}else{    	
			   		$file_new_content = str_replace($hook_data['close_hook_position'],$replace_with,$file_content);
			   	}
			    $result_add_close_hook=file_put_contents($path, $file_new_content, LOCK_EX);
			}    
		}
	}

	if(($result_add_open_hook||(!empty($hook_data['open_hook'])&&strpos($file_content,$hook_data['open_hook'])!==false))
		&&($result_add_close_hook
			||(!empty($hook_data['close_hook'])&&strpos($file_content,$hook_data['close_hook'])!==false)
			||empty($hook_data['close_hook_position']))){
		return("S");
	}else{
		return("F");
	}
}

function str_replace_nth($search, $replace, $subject, $nth)
{
   	$_search=preg_quote($search);
	$_search=str_replace("/", "\/", $_search);
    $found = preg_match_all('/'.$_search.'/', $subject, $matches, PREG_OFFSET_CAPTURE);    
    if (false !== $found && $found >= $nth) {
        return substr_replace($subject, $replace, $matches[0][($nth-1)][1], strlen($search));
    }
    return $subject;
}

function strposOffset($string, $search, $offset)
{
    /*** explode the string ***/
    $arr = explode($search, $string);
    /*** check the search is not out of bounds ***/
    switch( $offset )
    {
        case $offset == 0:
        return false;
        break;
    
        case $offset > max(array_keys($arr)):
        return false;
        break;

        default:
        return strlen(implode($search, array_slice($arr, 0, $offset)));
    }
}

//Returns:
// S: Epitixis apegkatastasi
// F: Anepitixis apegkatastasi
// N: Den egine apegkatastasi epeidi itan min egkatastimeno
function fn_uninstall_custom_hook($hook_id,$hook_data=array()){
	if(empty($hook_data)){
		$hook_data=fn_get_custom_hook($hook_id);
	}

	if($hook_data["installed"]!="S")
		return("N");

	$path=DIR_ROOT."/".$hook_data['file'];
	$path=str_replace("[theme_name]", Settings::instance()->getValue('theme_name', '', 0), $path);

	//Elegxos an to arxeio iparxei
	if(!file_exists($path)){
        return("FS");
    }   	

	$save_files=false;
	$open_hook_removed=false;
	$close_hook_removed=false;

    $text = file_get_contents($path);

    if (!empty($hook_data['open_hook'])){
    	$_open_hook="";
    	if(strpos($text,$hook_data['open_hook']."\n\n")!==false){
    		$_open_hook=$hook_data['open_hook']."\n\n";
    	}elseif(strpos($text,$hook_data['open_hook']."\n")!==false){
    		$_open_hook=$hook_data['open_hook']."\n";
    	}elseif(strpos($text,$hook_data['open_hook'])!==false){
    		$_open_hook=$hook_data['open_hook'];
    	}
    	if(!empty($_open_hook)){
    		$text = str_replace($_open_hook,"",$text);  
        	$open_hook_removed=true;     
    	}
    }
    if (!empty($hook_data['close_hook'])){
    	$_close_hook="";
    	if(strpos($text,$hook_data['close_hook']."\n\n")!==false){
    		$_close_hook=$hook_data['close_hook']."\n\n";
    	}elseif(strpos($text,$hook_data['close_hook']."\n")!==false){
    		$_close_hook=$hook_data['close_hook']."\n";
    	}elseif(strpos($text,$hook_data['close_hook'])!==false){
    		$_close_hook=$hook_data['close_hook'];
    	}
    	if(!empty($_close_hook)){
    		$text = str_replace($_close_hook,"",$text);        	
        	$close_hook_removed=true;     
    	}
    }
    if (!empty($hook_data['open_hook'])&&$open_hook_removed&&!empty($hook_data['close_hook'])&&$close_hook_removed){
    	$save_files=true; 
    }elseif(!empty($hook_data['open_hook'])&&$open_hook_removed&&empty($hook_data['close_hook'])){
    	$save_files=true; 
    }elseif(!empty($hook_data['close_hook'])&&$close_hook_removed&&empty($hook_data['open_hook'])){
    	$save_files=true; 
    }

    if($save_files)
    	file_put_contents($path, $text, LOCK_EX); 

    //TODO: Epistrofei akrivos pou emfanistike to provlima: den eiparxei arxeio, den egine aferesei open i close hook
    if($save_files){
    	db_query("UPDATE ?:i_custom_hooks SET installed=?s WHERE custom_hook_id=?i","N",$hook_id);
		fn_clear_cache();
    	return("S");
    }else{
    	return("F");
    }
}

