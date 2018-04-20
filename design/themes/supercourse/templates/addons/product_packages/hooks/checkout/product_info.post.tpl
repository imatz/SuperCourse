{if $product.product_id|fn_check_package == "Y"}
    <p><a data-ca-target-id="package_info_{$key}" class="cm-dialog-opener cm-dialog-auto-size">{__("package_info")}</a></p>
    <div id="package_info_{$key}" class="hidden" title="{__("package_info")}">
	<table class="ty-cart-content ty-table">
	<thead>
	    <tr>
		<th class="ty-cart-content__title">{__("product")}</th>
		<th class="ty-cart-content__title">{__("options")}</th>
		<th class="ty-cart-content__title">{__("quantity")}</th>
		{if $product.price_rule == "S"}
		<th class="ty-cart-content__title">{__("price")}</th>
		<th class="ty-cart-content__title">{__("subtotal")}</th>
		{/if}
	    </tr>
        </thead>
	    {foreach from=$cart.products item="pp" key="pp_key"}
		{if $pp.extra.package_info.p_id == $key}
		    <tr>
			<td>
			   <a href="{"products.view&product_id=`$pp.product_id`"|fn_url}">{$pp.product}</a>
			</td>
			<td>
			      {if $pp.product_options}
				  {include file="common/options_info.tpl" product_options=$cart_products.$pp_key.product_options}
			      {/if}
			      
			</td>
			<td class="center">{$pp.amount}</td>
			{if $product.price_rule == "S"}
			<td>{include file="common/price.tpl" value=$pp.extra.package_info.f_price class="none"}</td>
			
			<td>
			    {include file="common/price.tpl" value=$pp.extra.package_info.f_price*$pp.amount class="none"}
			</td>
			{/if}
		    </tr>
		{/if}
	    {/foreach}
            {assign var="no_product_items" value=$product.product_id|fn_get_no_products}
            {foreach from=$no_product_items item="pp" key="pp_key"}
                    <tr>
                        <td>
                           {$pp.name}
                        </td>
                        <td>
                            &nbsp;
                        </td>
                        <td class="center">{$pp.amount}</td>
                    </tr>
            {/foreach}
	</table>
    </div>
{/if}