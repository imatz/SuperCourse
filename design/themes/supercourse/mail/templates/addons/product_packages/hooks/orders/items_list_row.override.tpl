{if !$oi.extra.parent && !$oi.extra.package_info}
<tr>
    <td style="padding: 5px 10px; background-color: #ffffff; font-size: 12px; font-family: Arial;">
        <div style="font-size: 24px; margin: 6px 0 10px">{$oi.product|default:__("deleted_product") nofilter}</div>
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
{else}
<span style="display: none;">aa</span>
{/if}

{if $oi.extra.package_hash}
	{assign var="colspan" value="4"}
	{if $order_info.use_discount}
	    {assign var="colspan" value=$colspan+1}
	{/if}
	{if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
	    {assign var="colspan" value=$colspan+1}
	{/if}
	<tr>
            <td colspan="{$colspan}" style="padding: 0 0 1px">
                <table width="100%" cellspacing="1" cellpadding="0">
                <tr>
                    <th style="background-color: #f0f0f0; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">
                        {__("products_in_package")}
                    </th>
                    <th style="background-color: #f0f0f0; padding: 6px 10px; white-space: nowrap; font-size: 12px; font-family: Arial;">
                        {__("qty")}
                    </th>
                </tr>
                {foreach from=$order_info.products item="oip"}
                    {if $oi.item_id == $oip.extra.package_info.p_id}
                        <tr>
                            <td style="color: #666;padding: 5px 10px; background-color: #ffffff; font-size: 12px; font-family: Arial;">
                                {$oip.product|default:__("deleted_product") nofilter}
                                {if $oip.product_code}<p style="color: #666; margin: 2px 0px 3px 0px;">{__("sku")}: {$oip.product_code}</p>{/if}
                                {if $oip.product_options}<br/>{include file="common/options_info.tpl" product_options=$oip.product_options}{/if}
                                {if $settings.Suppliers.enable_suppliers == "Y" && $oi.company_id && $settings.Suppliers.display_supplier == "Y"}
                                    <p style="margin: 2px 0px 3px 0px;">{__("supplier")}: {$oi.company_id|fn_get_company_name}</p>
                                {/if}
                            </td>
                            <td style="color: #666;padding: 5px 10px; background-color: #ffffff; font-size: 12px; font-family: Arial;">
                                {$oip.amount}
                            </td>
                        </tr>
                    {/if}
                {/foreach}
                {foreach from=$oi.extra.no_items_products item="oip"}
                    <tr>
                        <td style="color: #666;padding: 5px 10px; background-color: #ffffff; font-size: 12px; font-family: Arial;">
                            {$oip.name nofilter}
                        </td>
                        <td style="color: #666;padding: 5px 10px; background-color: #ffffff; font-size: 12px; font-family: Arial;">
                            {$oip.amount}
                        </td>
                    </tr>
                {/foreach}
            </table>
        </td>
      </tr>
{/if}