<div class="ty-package_products">
<em>{__("package_products")}:</em>
{foreach from=$package_products item=package_product}
<br>
	
		{if $package_product.product_alt}
			{$package_product.product_alt}
		{else}
			{$package_product.product}
		{/if}
    {if $package_product.status == "D"}
    <br>
    	<b>There is a product that has been disabled.</b>
    {/if}
{foreachelse}
		{__("no_items_found")}
{/foreach}

</div>