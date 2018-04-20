<?php

if (!defined('BOOTSTRAP')) { die('Access denied'); }

use Tygh\Registry;
use Tygh\Settings;

/** 
* addon 
*/
function fn_my_users_install()
{
	// ftiaje ta pedia profil poy xreiazomaste
	$profile_ids=array();
	
	//thlefvna
	$phone_data=array(
		'description'=>'Τηλέφωνο επικοινωνίας',
		'section'=>'BS',
		'field_name'=>'phones',
		'profile_show'=>'N',
		'profile_required'=>'N',
		'checkout_show'=>'N',
		'checkout_required'=>'N',
		'field_type'=>'P',
		'position'=>108,
	);
	

	$profile_ids['b_phones']=fn_update_profile_field($phone_data,0,'el');
	$profile_ids['s_phones']=db_get_field("SELECT matching_id FROM ?:profile_fields WHERE field_id=?i",$profile_ids['b_phones']);
	
	// energopoihse thn emfanish se profil kai checkout tvn shiiping
	
	$phone_data['field_name']='s_phones';
	$phone_data['section']='S';
	$phone_data['profile_show']='Y';
	$phone_data['profile_required']='Y';
	$phone_data['checkout_show']='Y';
	$phone_data['checkout_required']='Y';
	unset($phone_data['field_type']);
	
	fn_update_profile_field($phone_data,$profile_ids['s_phones'],'el');
	
	
	// parathrhseis gia courrier
	$par_data=array(
		'description'=>'Παρατηρήσεις Παράδοσης',
		'section'=>'BS',
		'profile_show'=>'N',
		'profile_required'=>'N',
		'checkout_show'=>'N',
		'checkout_required'=>'N',
		'field_type'=>'T',
		'position'=>109,
	);
	
	$profile_ids['b_delivery_notes']=fn_update_profile_field($par_data,0,'el');
	$profile_ids['s_delivery_notes']=db_get_field("SELECT matching_id FROM ?:profile_fields WHERE field_id=?i",$profile_ids['b_delivery_notes']);
	
	// energopoihse thn emfanish se profil kai checkout tvn shiiping
	
	$par_data['section']='S';
	$par_data['profile_show']='Y';
	$par_data['profile_required']='N';
	$par_data['checkout_show']='Y';
	$par_data['checkout_required']='N';
	unset($par_data['field_type']);
	
	fn_update_profile_field($par_data,$profile_ids['s_delivery_notes'],'el');
	
	/*
	* GENERAL SECTION
	*/
	
	//tim_lian
	
	$tl_data=array(
		'description'=>'Τιμολόγιο - Λιανική',
		'section'=>'C',
		'profile_show'=>'Y',
		'profile_required'=>'N',
		'checkout_show'=>'N',
		'checkout_required'=>'N',
		'field_type'=>'I',
		'position'=>36,
	);
	
	
	$profile_ids['tim_lian']=fn_update_profile_field($tl_data,0,'el');
	
	//afm
	
	$afm_data=array(
		'description'=>'ΑΦΜ',
		'section'=>'C',
		'profile_show'=>'Y',
		'profile_required'=>'Y',
		'checkout_show'=>'Y',
		'checkout_required'=>'Y',
		'field_type'=>'I',
		'position'=>34,
	);
	
	
	$profile_ids['afm']=fn_update_profile_field($afm_data,0,'el');
	
	//epaggelma
	
	$prof_data=array(
		'description'=>'Επάγγελμα',
		'section'=>'C',
		'profile_show'=>'Y',
		'profile_required'=>'N',
		'checkout_show'=>'Y',
		'checkout_required'=>'N',
		'field_type'=>'I',
		'position'=>35,
	);
	
	
	$profile_ids['profession']=fn_update_profile_field($prof_data,0,'el');
	
	// typos pelath
	
	$acc_data=array(
		'description'=>'Κατηγορία Πελάτη',
		'section'=>'C',
		'profile_show'=>'Y',
		'profile_required'=>'N',
		'checkout_show'=>'N',
		'checkout_required'=>'N',
		'field_type'=>'I',
		'position'=>37,
	);
	
	
	$profile_ids['account_type']=fn_update_profile_field($acc_data,0,'el');
	
	$vat_data=array(
		'description'=>'Κατηγορία ΦΠΑ',
		'section'=>'C',
		'profile_show'=>'Y',
		'profile_required'=>'N',
		'checkout_show'=>'N',
		'checkout_required'=>'N',
		'field_type'=>'I',
		'position'=>38,
	);
	
	$profile_ids['vat_class']=fn_update_profile_field($vat_data,0,'el');
	
	$cmp_data=array(
		'description'=>'CMP',
		'section'=>'C',
		'profile_show'=>'Y',
		'profile_required'=>'N',
		'checkout_show'=>'N',
		'checkout_required'=>'N',
		'field_type'=>'I',
		'position'=>39,
	);
	
	
	$profile_ids['cmp']=fn_update_profile_field($cmp_data,0,'el');

	
	// balta sta settings toy addon
	if (!$section = Settings::instance()->getSectionByName('my_users', Settings::ADDON_SECTION)) {
		$section = Settings::instance()->updateSection(array(
			'parent_id' =>      0,
			'edition_type' =>   'ROOT,ULT:VENDOR',
			'name' =>           'my_users',
			'type' =>           'ADDON'
		));
	}
	

	foreach ($profile_ids as $option_name => $option_value) {
		if (!$setting_id = Settings::instance()->getId($option_name, 'my_users')) {
			$setting_id = Settings::instance()->update(array(
				'name' =>           $option_name,
				'section_id' =>     $section['section_id'],
				'edition_type' =>	'ROOT,ULT:VENDOR',
				'section_tab_id' => 0,
				'type' =>           'A',
				'position' =>       0,
				'is_global' =>      'N',
				'handler' =>        ''
			));
		}

		Settings::instance()->updateValueById($setting_id, $option_value, Registry::get('runtime.company_id'));
	}				
}


function fn_my_users_get_setting_field($field_name,$default_value='')
{
	$settings=Registry::get('addons.my_users');
	return (!empty($settings[$field_name]))?$settings[$field_name]:$default_value;
}

// end of addon settings

/*
* Hooks
*/
function fn_my_users_get_user_info($user_id, $get_profile, $profile_id, &$user_data)
{
	// an pote xreiastei na baloyme polla thl sta stauera stoixeia epikoinvnias
	//$user_data['phones']=db_get_array("SELECT phone_id, phone FROM ?:user_profile_phones upp WHERE user_id=?i AND section='C' ORDER BY phone_id",$user_id);
	if($get_profile){
    $user_data['b_phones']=fn_my_users_get_user_profile_phones('B',$user_id,$profile_id);
    $user_data['s_phones']=fn_my_users_get_user_profile_phones('S',$user_id,$profile_id);
	}
}

function fn_my_users_update_user_profile_post($user_id, $user_data, $action)
{
	
	if (!empty($user_data['phones'])){
		fn_my_users_update_user_profile_phones ($user_data['phones'],'C',$user_data['user_id'],null);
	}
	
	if (!empty($user_data['b_phones'])){
		fn_my_users_update_user_profile_phones ($user_data['b_phones'],'B',$user_data['user_id'],$user_data['profile_id']);
	}
	
	if (!empty($user_data['s_phones'])){
		fn_my_users_update_user_profile_phones ($user_data['s_phones'],'S',$user_data['user_id'],$user_data['profile_id']);
	}
}

function fn_my_users_fill_auth(&$auth, $user_data, $area, $original_auth)
{
	if ($user_data) { 
		$account_type_id=fn_my_users_get_setting_field('account_type');
		$vat_class_id=fn_my_users_get_setting_field('vat_class');
		$tim_lian=fn_my_users_get_setting_field('tim_lian');
		$auth['account_type']=fn_my_users_get_user_profile_data_value($user_data['user_id'],$account_type_id);
		$auth['vat_class']=fn_my_users_get_user_profile_data_value($user_data['user_id'],$vat_class_id);
		$auth['tim_lian']=fn_my_users_get_user_profile_data_value($user_data['user_id'],$tim_lian);
	}
}

// end of Hooks
function fn_my_users_get_account_types()
{
	return array(
		'R'=>'R',
		'B'=>'B',
		'S'=>'S'
	);
}

function fn_my_users_get_account_type($auth)
{
	return (empty($auth['account_type']))?'R':$auth['account_type'];
}


function fn_my_users_convert_phone_array($data)
{
	$ret=array();
	foreach($data as $d) {
		$ret[$d['phone_id']]=$d['phone'];
	}
	return $ret;
}


function fn_my_users_get_user_profile_data($user_id)
{
	$db_data = db_get_array("SELECT field_id, value FROM ?:profile_fields_data 
	WHERE object_type='U' AND object_id=?i", $user_id);
	$ret=array();
	foreach ($db_data as $db) {
		$ret[$db['field_id']]=$db['value'];
	}
	return $ret;
}

function fn_my_users_get_user_profile_data_value($user_id,$field_id)
{
	return db_get_field("SELECT value FROM ?:profile_fields_data 
	WHERE object_type='U' AND object_id=?i AND field_id=?i", $user_id,$field_id);
}

function fn_my_users_get_user_profile_phones ($section,$user_id,$profile_id)
{
	$where_data=array('user_id'=>$user_id,'section'=>$section);
	if(!empty($profile_id)){
		$where_data['profile_id']=$profile_id;
	}

	$phones = db_get_array("SELECT * FROM ?:user_profile_phones WHERE ?w", $where_data);
  return fn_my_users_convert_phone_array($phones);
}

function fn_my_users_delete_user_profile_phones ($section,$user_id,$profile_id)
{
	$where_data=array('user_id'=>$user_id,'section'=>$section);
	if(!empty($profile_id)){
		$where_data['profile_id']=$profile_id;
	}

	db_query("DELETE FROM ?:user_profile_phones WHERE ?w", $where_data);
}

function fn_my_users_update_user_profile_phones ($data,$section,$user_id,$profile_id)
{
	fn_my_users_delete_user_profile_phones ($section,$user_id,$profile_id);
	
	$insert_data=array();
	foreach($data as $id=>$phone) {
		if (!empty($phone)){
			$insert_data[]=array('user_id'=>$user_id, 'profile_id'=>$profile_id, 'section'=>$section, 'phone_id'=>$id, 'phone'=>$phone);
		}
	}
	if (!empty($insert_data)) return db_query("INSERT INTO ?:user_profile_phones ?m", $insert_data);
	
}

function fn_my_users_get_unregistered_user($afm,$tel)
{
	$afm_id=fn_my_users_get_setting_field('afm');
	
	return db_get_row("
	SELECT u.*
	FROM ?:profile_fields_data INNER JOIN ?:users u ON object_id=u.user_id
	WHERE object_type='U' AND field_id=?i AND value=?i AND EXISTS(
		SELECT upp.user_id FROM ?:user_profile_phones upp WHERE upp.user_id = object_id AND phone=?i 
	)",$afm_id,$afm,$tel);
}

function fn_my_users_is_user_exists_post($user_id, $user_data, &$is_exist)
{
	$is_exist = false;
}

/** CART SEPARATION AMONG PROFILES **/

// dioruvse to delete
function fn_my_users_pre_save_cart ($cart, $user_id, $type, &$condition) 
{
  $profile_id = 0;
  if (!empty($_SESSION['auth']['profile_id']))
    $profile_id = $_SESSION['auth']['profile_id'];
  
  $condition.=db_quote(' AND profile_id=?i',$profile_id);
}

// bale profile_id
function fn_my_users_save_cart ($cart, $user_id, $type) 
{
  $profile_id = 0;
  if (!empty($_SESSION['auth']['profile_id']))
    $profile_id = $_SESSION['auth']['profile_id'];
  
  db_query('UPDATE ?:user_session_products SET profile_id=?i WHERE ?w', $profile_id, array('timestamp'=>TIME, 'user_id'=>$user_id));
  
}

function fn_my_users_pre_extract_cart ($cart, &$condition, $item_types) 
{
  $profile_id = 0;
  if (!empty($_SESSION['auth']['profile_id']))
    $profile_id = $_SESSION['auth']['profile_id'];
  
  $condition.=db_quote(' AND profile_id=?i',$profile_id);
  
}