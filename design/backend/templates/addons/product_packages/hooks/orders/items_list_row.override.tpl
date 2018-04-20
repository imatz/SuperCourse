 {if !$oi.extra.parent && !$oi.extra.package_info}
    <tr>
	<td>
	    {if !$oi.deleted_product}<a href="{"products.update?product_id=`$oi.product_id`"|fn_url}">{/if}{$oi.product nofilter}{if !$oi.deleted_product}</a>{/if}
	    <div class="products-hint">
	    {hook name="orders:product_info"}
		{if $oi.product_code}<p>{__("sku")}:{$oi.product_code}</p>{/if}
	    {/hook}
	    </div>
	    {if $oi.product_options}<div class="options-info">{include file="common/options_info.tpl" product_options=$oi.product_options}</div>{/if}
	</td>
	<td class="nowrap">
	    {if $oi.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$oi.original_price}{/if}</td>
	<td class="center">
	    &nbsp;{$oi.amount}<br />
	    {if !"ULTIMATE:FREE"|fn_allowed_for && $use_shipments && $oi.shipped_amount > 0}
		&nbsp;<span class="muted"><small>({$oi.shipped_amount}&nbsp;{__("shipped")})</small></span>
	    {/if}
	</td>
	{if $order_info.use_discount}
	<td class="nowrap">
	    {if $oi.extra.discount|floatval}{include file="common/price.tpl" value=$oi.extra.discount}{else}-{/if}</td>
	{/if}
	{if $order_info.taxes && $settings.General.tax_calculation != "subtotal"}
	<td class="nowrap">
	    {if $oi.tax_value|floatval}{include file="common/price.tpl" value=$oi.tax_value}{else}-{/if}</td>
	{/if}
	<td class="right">&nbsp;<span>{if $oi.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$oi.display_subtotal}{/if}</span></td>
    </tr>
 {else}
 <span class="hidden">aa</span>
 {/if}
 