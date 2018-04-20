{if !$product.extra.parent && !$product.extra.package_info}
<tr class="ty-valign-top">
    <td>
        {if $product.is_accessible}<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">{/if}
            {$product.product nofilter}
        {if $product.is_accessible}</a>{/if}

        {if $product.extra.is_edp == "Y"}
            <div class="ty-right"><a href="{"orders.order_downloads?order_id=`$order_info.order_id`"|fn_url}">[{__("download")}]</a></div>
        {/if}
        {if $product.product_code}
            <div class="ty-orders-detail__table-code">{__("sku")}:&nbsp;{$product.product_code}</div>
        {/if}
        {hook name="orders:product_info"}
            {if $product.product_options}{include file="common/options_info.tpl" product_options=$product.product_options}{/if}
        {/hook}
    </td>
    <td class="ty-right">
        {if $product.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$product.original_price}{/if}
    </td>
    <td class="ty-center">&nbsp;{$product.amount}</td>
    {if $order_info.use_discount}
        <td class="ty-right">
            {if $product.extra.discount|floatval}{include file="common/price.tpl" value=$product.extra.discount}{else}-{/if}
        </td>
    {/if}
    {if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
        <td class="ty-center">
            {if $product.tax_value|floatval}{include file="common/price.tpl" value=$product.tax_value}{else}-{/if}
        </td>
    {/if}
    <td class="ty-right">
            &nbsp;{if $product.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$product.display_subtotal}{/if}
        </td>
</tr>
{else}
<span class="hidden"></span>
{/if}