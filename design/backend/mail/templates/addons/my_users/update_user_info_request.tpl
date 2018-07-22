{include file="common/letter_header.tpl"}
{assign var="when" value=$smarty.now|date_format:"`$settings.Appearance.date_format`"}
<b>{__("customer_code")}: $user_data.user_login </b>
<br><br>

{__("initial_values")}
<br><br>

<table>
<tr><td>email:</td><td>{$user_data.fmail}</td></tr>
{foreach from=$profile_fields item=field}
{if $field.field_name}
    {assign var="data_id" value=$field.field_name}
    {assign var="value" value=$user_data.$data_id}
{else}
    {assign var="data_id" value=$field.field_id}
    {assign var="value" value=$user_data.fields.$data_id}
{/if}
<tr><td>{$field.description}:</td><td>{$value}</td></tr>
{/foreach}
</table>
<br><br>

{__("requested_values")}
<br><br>

<table>
<tr><td>email:</td><td>{$request_data.email}</td></tr>
<tr><td>password:</td><td>{$request_data.password1}</td></tr>
{foreach from=$profile_fields item=field}
{if $field.field_name}
    {assign var="data_id" value=$field.field_name}
    {assign var="value" value=$request_data.$data_id}
{else}
    {assign var="data_id" value=$field.field_id}
    {assign var="value" value=$request_data.fields.$data_id}
{/if}
<tr><td>{$field.description}:</td><td>{$value}</td></tr>
{/foreach}
</table>
<br><br>

{__("supercourse_users_department")}
{include file="common/letter_footer.tpl" user_type='C'}