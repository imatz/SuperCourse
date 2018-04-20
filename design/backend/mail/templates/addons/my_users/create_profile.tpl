{include file="common/letter_header.tpl"}
{assign var="when" value=$smarty.now|date_format:"`$settings.Appearance.date_format`"}
{__("text_new_user_activation_request", ["[firstname]" => $user_data.firstname, "[lastname]" => $user_data.lastname, "[when]" => $when, "[phone]" => $settings.Company.company_phone])}
<br><br>
{__("supercourse_users_department")}
{include file="common/letter_footer.tpl" user_type='C'}