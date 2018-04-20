{include file="common/letter_header.tpl"}

{assign var="afm" value=$addons.my_users.afm}
{assign var="type" value=$addons.my_users.account_type}


{__("text_profile_activated", ["[link]" => $config.http_location, "[type]" => $user_data.fields.$type, "[afm]" => $user_data.fields.$afm, "[lastname]" => $user_data.lastname, "[password]" => $password, "[email]" => $user_data.email, "[phone]" => $user_data.phone])}
<br><br>
{__("supercourse_users_department")}

{include file="common/letter_footer.tpl"}