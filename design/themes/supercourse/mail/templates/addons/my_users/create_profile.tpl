{include file="common/letter_header.tpl"}
{assign var="when" value=$smarty.now|date_format:"`$settings.Appearance.date_format`"}



<table style="width: 100%; height: 100%;">
<tr>
<table bgcolor="#67b9ce" cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 27px 10px 10px 10px;">
            <tr>
                <td width="70%" align="left" style="padding-bottom: 3px;" valign="middle"><img src="images/logos/1/SuperCourse-Logo_wasw-m8.png" width="200" height="68" border="0" alt="{$logos.mail.image.alt}" /></td>
                <td width="30%" style="text-align: right;  font: 22px Arial; color: white; text-transform: normal;  margin: 10px 20px 10px 0;">
                	{if $doc_id_text}{$doc_id_text} <br />{/if}{__("new_user_activation_request")}
                </td>
            </tr>
            </table>
            <table bgcolor="#e4e4e4" cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 0 10px 10px 10px;">
            <tr valign="top">
                {hook name="orders:invoice_company_info"}
                <td style="width: 50%; padding: 2px 0px 0px 2px; font-size: 12px; font-family: Arial;">
                <p>
                    <strong>{$company_data.company_name}</strong> //
                    {$company_data.company_address}
                    {$company_data.company_city}{if $company_data.company_city && ($company_data.company_state_descr || $company_data.company_zipcode)},{/if} {$company_data.company_state_descr} {$company_data.company_zipcode} 
                    {$company_data.company_country_descr}
               </p>
               
               <table cellpadding="0" cellspacing="0" border="0">
                    {if $company_data.company_phone}
                    <tr>
                    <img style="vertical-align:middle;" src="images/mail_icons/Tel_icon.png" alt="Phone" width="42" height="42">
                    {$company_data.company_phone}
                    {/if}
                    {if $company_data.company_fax}
                    <img style="vertical-align:middle;" src="images/mail_icons/Fax_icon.png" alt="Fax" width="42" height="42">
                    {$company_data.company_fax}
                    {/if}
                    {if $company_data.company_website}
                   <img style="vertical-align:middle;" src="images/mail_icons/Web_icon.png" alt="Website" width="42" height="42">
                    {$company_data.company_website}
                    {/if}
                    {if $company_data.company_orders_department}
                    <img style="vertical-align:middle;" src="images/mail_icons/Email_icon.png" alt="Email" width="42" height="42">
                    <a href="mailto:{$company_data.company_orders_department}">{$company_data.company_orders_department|replace:",":"<br>"|replace:" ":""}</a>
                    </tr>
                    {/if}
                    </table>
                </td>
                {/hook}
            </tr>
            </table>
</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 0px 0px 5px 0px;">
	<tr valign="top">
		<td style="padding-top: 14px; padding-left: 10px;">
        {__("text_new_user_activation_request", ["[firstname]" => $user_data.firstname, "[lastname]" => $user_data.lastname, "[when]" => $when, "[phone]" => $settings.Company.company_phone])}
		<br><br>
		{__("supercourse_users_department")}
		{include file="common/letter_footer.tpl" user_type='C'}
        </td>
    </tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 0px 0px 5px 0px;">
                <tr valign="top">
                	<td style="padding-top: 14px; padding-left: 10px;">
                        <hr size="4" style="color: #1f5070; background-color: #1f5070;">
                	</td>
                </tr>
            </table>
            
            
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
            {if $order_info.notes}
            <td valign="top" width="50%">
                <label style="font-size: 12px; font-weight:bold; font-family: Arial; padding-left: 10px; text-transform: uppercase;">{__("customer_notes")}:</label>
                <label style="overflow-x: auto; clear: both; width: 510px; height: 100%; padding-bottom: 20px; overflow-y: hidden; font-size: 12px; font-family: Arial;">{$order_info.notes|nl2br nofilter}</label>
            </td>
            {/if}
            <td width="50%" style="text-align: right;  font: 14px Arial; color: black; text-transform: normal;  margin: 10px 20px 10px 0;">Σας ευχαριστούμε για την <br> προτίμησή σας στα βιβλία μας</td>
            </table>
            
            
            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 0px 0px 5px 0px;">
                <tr valign="top">
                	<td style="padding-top: 14px; padding-left: 10px;">
                        <hr size="8" style="color: #1f5070; background-color: #1f5070;">
                	</td>
                </tr>
            </table>
            