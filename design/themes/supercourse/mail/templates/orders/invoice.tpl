<table style="width: 100%; height: 100%;">
<tr>
<table bgcolor="#67b9ce" cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 27px 10px 10px 10px;">
            <tr>
                <td width="70%" align="left" style="padding-bottom: 3px;" valign="middle"><img src="{$logos.mail.image.image_path}" width="200" height="68" border="0" alt="{$logos.mail.image.alt}" /></td>
                <td width="30%" style="text-align: right;  font: 22px Arial; color: white; text-transform: normal;  margin: 10px 20px 10px 0;">
                	{if $doc_id_text}{$doc_id_text} <br />{/if}{__("order")}&nbsp;#{$order_info.order_id}
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
                    {$company_data.company_city},&nbsp;{__("nomos_imathias")}, {$company_data.company_zipcode},
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
                    http://supercourse.gr
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
                        <strong>{if $doc_id_text}{$doc_id_text} <br />{/if}{__("order")}&nbsp;#{$order_info.order_id}</strong>
                        <hr width="auto" style="border: 1px dotted #e4e4e4" size="2">
                </td>
                </tr>
                </table>
                              
                {hook name="orders:invoice_order_status_info"}
                <table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 0px 0px 5px 0px;">
            	<tr valign="top">
                <td width="33%" style="padding-left: 10px; font-size: 12px; font-family: Arial;">
                    <label style="text-transform: uppercase; font-weight:bold;">{__("status")}:</label>
                    {__("kataxwrithike")}
                    <br>
                    <label style="text-transform: uppercase; font-weight:bold;">{__("date")}:</label>
                    {$order_info.timestamp|date_format:"`$settings.Appearance.date_format`, `$settings.Appearance.time_format`"}
                </td>
                {if $order_info.shipping}
                <td width="33%" style="font-size: 12px; font-family: Arial;">
                    <label style="text-transform: uppercase; font-weight:bold;">{__("payment_method")}:</label>
                    {$payment_method.payment|default:" - "}
                    <br>
                    <label style="text-transform: uppercase; font-weight:bold;">{__("shipping_method")}:</label>
                    {foreach from=$order_info.shipping item="shipping" name="f_shipp"}
                                {$shipping.shipping}{if !$smarty.foreach.f_shipp.last}, {/if}
                                {if $shipments[$shipping.group_key].tracking_number}{assign var="tracking_number_exists" value="Y"}{/if}
                    {/foreach}
                    {/if}
                </td>
            </tr>
            {/hook}
            </table>
            
            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 0px 0px 5px 0px;">
                <tr valign="top">
                	<td style="padding-top: 14px; padding-left: 10px;">
                        <hr width="auto" style="border: 1px dotted #e4e4e4" size="2">
                	</td>
                </tr>
            </table>
                
            {hook name="orders:invoice_customer_info"}
            {if !$profile_fields}
            {assign var="profile_fields" value='I'|fn_get_profile_fields}
            {/if}
            {if $profile_fields}
            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 0px 0px 5px 0px;">
            <tr valign="top">
                {if $profile_fields.C}
                {assign var="profields_c" value=$profile_fields.C|fn_fields_from_multi_level:"field_name":"field_id"}
                <td width="33%" style="padding-left: 10px; font-size: 12px; font-family: Arial;">
                    <strong>{__("customer")}:</strong>
                    <p style="margin: 2px 0px 3px 0px;">{if $profields_c.firstname}{$order_info.firstname}&nbsp;{/if}{if $profields_c.lastname}{$order_info.lastname}{/if}</p>
                    {if $profields_c.email}<p style="margin: 2px 0px 3px 0px;"><a href="mailto:{$order_info.email|escape:url}">{$order_info.email}</a></p>{/if}
                    {if $profields_c.phone}<p style="margin: 2px 0px 3px 0px;"><span style="text-transform: uppercase;">{__("phone")}:</span>&nbsp;{$order_info.phone}</p>{/if}
                    {if $profields_c.fax && $order_info.fax}<p style="margin: 2px 0px 3px 0px;"><span style="text-transform: uppercase;">{__("fax")}:</span>&nbsp;{$order_info.fax}</p>{/if}
                    {if $profields_c.company && $order_info.company}<p style="margin: 2px 0px 3px 0px;"><span style="text-transform: uppercase;">{__("company")}:</span>&nbsp;{$order_info.company}</p>{/if}
                    {if $profields_c.url && $order_info.url}<p style="margin: 2px 0px 3px 0px;"><span style="text-transform: uppercase;">{__("url")}:</span>&nbsp;{$order_info.url}</p>{/if}
                    {include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.C}
                </td>
                {/if}
                {if $profile_fields.B}
                {assign var="profields_b" value=$profile_fields.B|fn_fields_from_multi_level:"field_name":"field_id"}
                <td width="34%" style="font-size: 12px; font-family: Arial; {if $profile_fields.S}padding-right: 10px;{/if} {if $profile_fields.C}padding-left: 10px;{/if}">
                    <h3 style="font: bold 17px Tahoma; padding: 0px 0px 3px 1px; margin: 0px;">{__("bill_to")}:</h3>
                    {if $order_info.b_firstname && $profields_b.b_firstname || $order_info.b_lastname && $profields_b.b_lastname}
                    <p style="margin: 2px 0px 3px 0px;">
                        {if $profields_b.b_firstname}{$order_info.b_firstname} {/if}{if $profields_b.b_lastname}{$order_info.b_lastname}{/if}
                    </p>
                    {/if}
                    {if $order_info.b_address && $profields_b.b_address || $order_info.b_address_2 && $profields_b.b_address_2}
                    <p style="margin: 2px 0px 3px 0px;">
                        {if $profields_b.b_address}{$order_info.b_address} {/if}{if $profields_b.b_address_2}<br />{$order_info.b_address_2}{/if}
                    </p>
                    {/if}
                    {if $order_info.b_city && $profields_b.b_city || $order_info.b_state_descr && $profields_b.b_state || $order_info.b_zipcode && $profields_b.b_zipcode}
                    <p style="margin: 2px 0px 3px 0px;">
                        {if $profields_b.b_city}{$order_info.b_city}{if $profields_b.b_state},{/if} {/if}{if $profields_b.b_state}{$order_info.b_state_descr} {/if}{if $profields_b.b_zipcode}{$order_info.b_zipcode}{/if}
                    </p>
                    {/if}
                    {if $order_info.b_country_descr && $profields_b.b_country}
                    <p style="margin: 2px 0px 3px 0px;">
                        {$order_info.b_country_descr}
                    </p>
                    {/if}
                    {if $order_info.b_phone && $profields_b.b_phone}
                    <p style="margin: 2px 0px 3px 0px;">
                        {if $profields_b.b_phone}{$order_info.b_phone} {/if}
                    </p>
                    {/if}
                    {include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.B}
                </td>
                {/if}
                {if $profile_fields.S}
                {assign var="profields_s" value=$profile_fields.S|fn_fields_from_multi_level:"field_name":"field_id"}
                <td width="33%" style="font-size: 12px; font-family: Arial;">
                    <strong>{__("ship_to")}:</strong>
                    
                    {if $order_info.s_firstname && $profields_s.s_firstname || $order_info.s_lastname && $profields_s.s_lastname}
                    <p style="margin: 2px 0px 3px 0px;">
                        {if $profields_s.s_firstname}{$order_info.s_firstname} {/if}{if $profields_s.s_lastname}{$order_info.s_lastname}{/if}
                    </p>
                    {/if}
                    <p style="margin: 2px 0px 3px 0px;">
                    {if $order_info.s_address && $profields_s.s_address || $order_info.s_address_2 && $profields_s.s_address_2}
                        {if $profields_s.s_address}{$order_info.s_address} {/if}{if $profields_s.s_address_2}<br />{$order_info.s_address_2}{/if}
                    {/if}
                    {if $order_info.s_city && $profields_s.s_city || $order_info.s_state_descr && $profields_s.s_state || $order_info.s_zipcode && $profields_s.s_zipcode}
                        {if $profields_s.s_city}{$order_info.s_city}{if $profields_s.s_state},{/if} {/if}{if $profields_s.s_state}{$order_info.s_state_descr} {/if}{if $profields_s.s_zipcode}{$order_info.s_zipcode}{/if}
                    {/if}
                    {if $order_info.s_country_descr && $profields_s.s_country}
                        {$order_info.s_country_descr}
                    {/if}
                    {if $order_info.s_phone && $profields_s.s_phone}
                        {if $profields_s.s_phone}{$order_info.s_phone} {/if}
                    {/if}
                    </p>
                    {include file="profiles/profiles_extra_fields.tpl" fields=$profile_fields.S}
                </td>
                {/if}
            </tr>
            </table>
            {/if}
            {/hook}
            {* Customer info *}
            
            {* Ordered products *}
            <table width="100%" cellpadding="0" cellspacing="1" style="border-collapse:collapse; background-color: #dddddd;">
            <tr style="padding-left: 10px;">
                <th width="70%" style="background-color: #67b9ce; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{__("product")}</th>
                <th style="background-color: #67b9ce; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{__("quantity")}</th>
                <th style="background-color: #67b9ce; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{__("unit_price")}</th>
                {if $order_info.use_discount}
                    <th style="background-color: #67b9ce; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{__("discount")}</th>
                {/if}
                {if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
                    <th style="background-color: #67b9ce; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{__("tax")}</th>
                {/if}
                <th style="background-color: #67b9ce; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">{__("subtotal")}</th>
            </tr>
            {foreach from=$order_info.products item="oi"}
            {hook name="orders:items_list_row"}
                {if !$oi.extra.parent}
                <tr>
                    <td style="padding: 5px 10px; background-color: #ffffff; font-size: 12px; font-family: Arial;">
                        {$oi.product|default:__("deleted_product") nofilter}
                        {hook name="orders:product_info"}
                        {if $oi.product_code}<p style="margin: 2px 0px 3px 0px;">{__("sku")}: {$oi.product_code}</p>{/if}
                        {/hook}
                        {if $oi.product_options}<br/>{include file="common/options_info.tpl" product_options=$oi.product_options}{/if}
                    </td>
                    <td style="padding: 5px 10px; background-color: #ffffff; text-align: center; font-size: 12px; font-family: Arial;">{$oi.amount}</td>
                    <td style="padding: 5px 10px; background-color: #ffffff; text-align: right; font-size: 12px; font-family: Arial;">{if $oi.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$oi.original_price}{/if}</td>
                    {if $order_info.use_discount}
                    <td style="padding: 5px 10px; background-color: #ffffff; text-align: right; font-size: 12px; font-family: Arial;">{if $oi.extra.discount|floatval}{include file="common/price.tpl" value=$oi.extra.discount}{else}&nbsp;-&nbsp;{/if}</td>
                    {/if}
                    {if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
                        <td style="padding: 5px 10px; background-color: #ffffff; text-align: right; font-size: 12px; font-family: Arial;">{if $oi.tax_value}{include file="common/price.tpl" value=$oi.tax_value}{else}&nbsp;-&nbsp;{/if}</td>
                    {/if}
        
                    <td style="padding: 5px 10px; background-color: #ffffff; text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{if $oi.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$oi.display_subtotal}{/if}</b>&nbsp;</td>
                </tr>
                {/if}
            {/hook}
            {/foreach}
            {hook name="orders:extra_list"}
            {/hook}
            </table>
        
            {hook name="orders:ordered_products"}
            {/hook}
            {* /Ordered products *}
        
            {* Order totals *}
            <table cellpadding="0" cellspacing="0" border="0" width="100%">
            <tr>
                <td align="right">
                <table border="0" style="padding: 3px 0px 12px 0px;">
                <tr>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{__("synoliko_kostos_eidwn")}:</b>&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common/price.tpl" value=$order_info.display_subtotal}</td>
                </tr>
                {*
                {if $order_info.discount|floatval}
                <tr>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{__("including_discount")}:</b>&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">
                        {include file="common/price.tpl" value=$order_info.discount}</td>
                </tr>
                {/if}
				
            
                {if $order_info.subtotal_discount|floatval}
                <tr>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{__("order_discount")}:</td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">
                        {include file="common/price.tpl" value=$order_info.subtotal_discount}</td>
                </tr>
                {/if}

                {if $order_info.coupons}
                {foreach from=$order_info.coupons item="coupon" key="key"}
                <tr>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{__("coupon")}:</b>&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{$key}</td>
                </tr>
                {/foreach}
                {/if}
                {if $order_info.taxes}
                <tr>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{__("taxes")}:</b>&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">&nbsp;</td>
                </tr>
                {foreach from=$order_info.taxes item=tax_data}
                <tr>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{$tax_data.description}&nbsp;{include file="common/modifier.tpl" mod_value=$tax_data.rate_value mod_type=$tax_data.rate_type}{if $tax_data.price_includes_tax == "Y" && ($settings.Appearance.cart_prices_w_taxes != "Y" || $settings.General.tax_calculation == "subtotal")}&nbsp;{__("included")}{/if}{if $tax_data.regnumber}&nbsp;({$tax_data.regnumber}){/if}:&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common/price.tpl" value=$tax_data.tax_subtotal}</td>
                </tr>
                {/foreach}
                {/if}
                {if $order_info.tax_exempt == 'Y'}
                <tr>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{__("tax_exempt")}</b></td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">&nbsp;</td>
                </tr>
                {/if}

                {if $order_info.payment_surcharge|floatval && !$take_surcharge_from_vendor}
                <tr>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{$order_info.payment_method.surcharge_title|default:__("payment_surcharge")}:&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{include file="common/price.tpl" value=$order_info.payment_surcharge}</b></td>
                </tr>
                {/if}
            	*}
            
                {if $order_info.shipping}
                <tr>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;"><b>{__("shipping_cost")}:</b>&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font-size: 12px; font-family: Arial;">{include file="common/price.tpl" value=$order_info.display_shipping_cost}</td>
                </tr>
                {/if}
                {hook name="orders:totals"}
                {/hook}
                
                <tr>
                    <td colspan="2"><hr style="border: 0px solid #d5d5d5; border-top-width: 1px;" /></td>
                </tr>
                <tr>
                    <td style="text-align: right; white-space: nowrap; font: 15px Tahoma; text-align: right;">{__("teliko_poso")}:&nbsp;</td>
                    <td style="text-align: right; white-space: nowrap; font: 15px Tahoma; text-align: right;"><strong style="font: bold 17px Tahoma;">{include file="common/price.tpl" value=$order_info.total}</strong></td>       
                </tr>
                </table>
                </td>
            </tr>
            </table>
        
            {* /Order totals *}
            
            
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
            <td width="50%" style="text-align: right;  font: 14px Arial; color: black; text-transform: normal;  margin: 10px 20px 10px 0;">{__("thanks_message")}</td>
            </table>
            
        
        {hook name="orders:invoice"}
        {/hook}
            
            
            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 0px 0px 5px 0px;">
                <tr valign="top">
                	<td style="padding-top: 14px; padding-left: 10px;">
                        <hr size="8" style="color: #1f5070; background-color: #1f5070;">
                	</td>
                </tr>
            </table>

{__("extra_invoice_info")}