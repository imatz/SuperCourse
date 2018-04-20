{*
{include file="common/letter_header.tpl"}


{$fmail = $smarty.session.email}
{assign var="rec_mail" value="$fmail"}
{assign var="afm" value=$addons.my_users.afm}
{assign var="type" value=$addons.my_users.account_type}
{assign var="now_time" value=$smarty.now|date_format:"%T"}
{assign var="date" value=$smarty.now|date_format:"%d/%m/%y"}
{assign var="company" value=$company_data.company_name}
{assign var="subject" value=__("ng_new_user_profile")}



{__("text_profile_activated", ["[link]" => $config.http_location, "[type]" => $user_data.fields.$type, "[afm]" => $user_data.fields.$afm, "[firstname]" => $user_data.firstname, "[lastname]" => $user_data.lastname, "[password]" => $password, "[fmail]" => $rec_mail, "[tel]" => ","|implode:$user_data.s_phones, "[date]" => $date, "[now_time]" => $now_time, "[company]" => $company, "[subject]" => $subject])}
<br><br>
{__("supercourse_users_department")}

{include file="common/letter_footer.tpl"}
*}

<table style="width: 100%; height: 100%;">
<tr>
<table bgcolor="#67b9ce" cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 27px 10px 10px 10px;">
            <tr>
                <td width="70%" align="left" style="padding-bottom: 3px;" valign="middle"><img src="images\logos\1\SuperCourse-Logo_wasw-m8.png" width="200" height="68" border="0" alt="Supercourse ELT Publishing" />
                </td>
                <td width="30%" style="text-align: right;  font: 16px Arial; color: white; text-transform: normal;  margin: 10px 20px 10px 0;">
                	Στοιχεία λογαριασμού e-Shop.
                </td>
            </tr>
        </table>
        <table bgcolor="#e4e4e4" cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 0 10px 10px 10px;">
            <tr valign="top">
                {hook name="orders:invoice_company_info"}
                <td style="width: 50%; padding: 2px 0px 0px 2px; font-size: 12px; font-family: Arial;">
                <p>
                    <strong>{$company_data.company_name}</strong> //
                    {$company_data.company_address},
                    {$company_data.company_city}{if $company_data.company_city && ($company_data.company_state_descr || $company_data.company_zipcode)},{/if} {$company_data.company_state_descr} {$company_data.company_zipcode}, 
                    {$company_data.company_country_descr}
               </p>
               
               <table cellpadding="0" cellspacing="0" border="0">
                    {if $company_data.company_phone}
                    <tr>
                    <img style="vertical-align:middle;" src="images\mail_icons\Tel_icon.png" alt="Phone" width="42" height="42">
                    {$company_data.company_phone}
                    {/if}
                    {if $company_data.company_fax}
                    <img style="vertical-align:middle;" src="images\mail_icons\Fax_icon.png" alt="Fax" width="42" height="42">
                    {$company_data.company_fax}
                    {/if}
                    {if $company_data.company_website}
                    <img style="vertical-align:middle;" src="images\mail_icons\Web_icon.png" alt="Website" width="42" height="42">
                    {$company_data.company_website}
                    {/if}
                    {if $company_data.company_orders_department}
                    <img style="vertical-align:middle;" src="images\mail_icons\Email_icon.png" alt="Email" width="42" height="42">
                    <a href="mailto:{$company_data.company_orders_department}">{$company_data.company_orders_department|replace:",":"<br>"|replace:" ":""}</a>
                    </tr>
                    {/if}
                    </table>
                </td>
                {/hook}
            </tr>
            </table>
                
<table bgcolor="#fff" cellpadding="10" cellspacing="0" border="0" width="100%" class="main-table" style="height: 100%; background-color: #fff; font-size: 12px; font-family: Arial;">
  <tr>
    <td colspan="2">
<b>Επιβεβαίωση ενεργοποίησης & στοιχεία λογαριασμού e-Shop.</b>
<br><br>
<b><u>Στοιχεία εταιρίας:</u></b><br>
Όνομα: {$user_data.firstname}<br>
Α.Φ.Μ.: {$user_data.fields.$afm}<br>
<br>
Σας ενημερώνουμε ότι ο λογαριασμός σας δημιουργήθηκε / ενεργοποιήθηκε με επιτυχία!
<br><br>
<b><u>Στοιχεία εισόδου του λογαριασμού σας:</u></b><br>
Ηλεκτρονική διεύθυνση: <a href="{$config.http_location}">Εκδόσεις SuperCourse - Ηλεκτρονικό Κατάστημα</a><br>
Username: {$rec_mail}<br>
Password: {$password}<br>
<br>
Καλές αγορές
<br><br>
Η ομάδα εξυπηρέτησης πελατών της Super Course ELT Publishing</td>
<br><br>
  </tr>
  <hr>
  <tr>
    <td colspan="2">
<b>Confirmation & Account information e-Shop</b>
<br><br>
Name: {$user_data.firstname}<br>
Vat No: {$user_data.fields.$afm}<br>
<br>
We would like to inform you that your online application HAS BEEN created and activated successfully.
<br><br>
<b><u>Data entry account:</u></b><br>
Websitre address: <a href="{$config.http_location}">Εκδόσεις SuperCourse - Ηλεκτρονικό Κατάστημα</a><br>
Username: {$rec_mail}<br>
Password: {$password}<br>
<br><br>
The Super Course customer service team 
</td>
  </tr>
  </table>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 0px 0px 5px 0px;">
	<tr valign="top">
		<td style="padding-top: 14px; padding-left: 10px;">
			<hr size="8" style="color: #1f5070; background-color: #1f5070;">
		</td>
	</tr>
</table>
            