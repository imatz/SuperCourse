{include file="common/letter_header.tpl"}

{__("dear")} {if $user_data.firstname}{$user_data.firstname}{else}{$user_data.user_type|fn_get_user_type_description|lower}{/if},<br><br>

{__("register_profile_notification_header")}<br><br>

<table cellpadding="1" cellspacing="1" border="0" width="100%">
<tr>
	<td colspan="2" class="form-title">{__("user_account_info")}<hr size="1" noshade></td>
</tr>
<tr>
	<td class="form-field-caption" nowrap>Username:&nbsp;</td>
	<td>{$user_data.email}</td>
</tr>
<tr>
	<td class="form-field-caption" nowrap>Password:&nbsp;</td>
	<td>{$req_password}</td>
</tr>
</table>


{include file="common/letter_footer.tpl"}