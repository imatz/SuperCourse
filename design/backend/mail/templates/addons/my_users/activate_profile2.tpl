{include file="common/letter_header.tpl"}
{*
{__("hello")},<br><br>
*}

{__("text_new_user_activation", ["[firstname]" => $user_data.firstname, "[lastname]" => $user_data.lastname, "[password]" => $user_data.password1, "[email]" => $user_data.email, "[phone]" => $user_data.phone])}
{*
{include file="common/letter_footer.tpl" user_type='A'}
*}


