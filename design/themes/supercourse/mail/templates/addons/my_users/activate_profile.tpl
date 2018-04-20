{include file="common/letter_header.tpl"}

{__("hello")},<br><br>
{assign var="user_login" value=$user_data.email}
{assign var="tel" value=$user_data.tel}
{assign var="afm" value=$user_data.afm}
{assign var="code" value=$user_data.code}
{assign var="phone" value=$user_data.phone}
{__("text_new_user_activation", ["[user_login]" => $user_login, "[tel]" => $tel, "[afm]" => $afm, "[email]" => $user_login, "[code]" => $code, "[phone]" => $phone])}

{include file="common/letter_footer.tpl" user_type='A'}