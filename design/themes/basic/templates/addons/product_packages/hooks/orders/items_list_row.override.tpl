{if !$product.extra.parent && !$product.extra.package_info}
{cycle values=",class=\"table-row\"" name="class_cycle" assign="_class"}
<tr {$_class} style="vertical-align: top;">
    <td>{if $product.is_accessible}<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{/if}{$product.product nofilter}{if $product.is_accessible}</a>{/if}
        {if $product.extra.is_edp == "Y"}
        <div class="right"><a href="{"orders.order_downloads?order_id=`$order_info.order_id`"|fn_url}"><strong>[{__("download")}]</strong></a></div>
        {/if}
        {if $product.product_code}
        <p class="code">{__("sku")}:&nbsp;{$product.product_code}</p>
        {/if}
        {hook name="orders:product_info"}
        {if $product.product_options}{include file="common/options_info.tpl" product_options=$product.product_options inline_option=true}{/if}
        {/hook}
    </td>
    <td class="right nowrap">
        {if $product.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$product.original_price}{/if}</td>
    <td class="center">&nbsp;{$product.amount}</td>
    {if $order_info.use_discount}
        <td class="right nowrap">
            {if $product.extra.discount|floatval}{include file="common/price.tpl" value=$product.extra.discount}{else}-{/if}
        </td>
    {/if}
    {if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
        <td class="center nowrap">
            {if $product.tax_value|floatval}{include file="common/price.tpl" value=$product.tax_value}{else}-{/if}
        </td>
    {/if}
    <td class="right">
         &nbsp;<strong>{if $product.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$product.display_subtotal}{/if}</strong></td>
</tr>
{else}
<span class="hidden"></span>
{/if}