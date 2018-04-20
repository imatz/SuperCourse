{if !$cart.products.$key.extra.parent}
	<li class="ty-order-products__item">
	   {if "Y"==$product.package}<em>{__("my_package")}:</em> {$product.product nofilter}, [{$product.product_code}] 
	   {else}{$product.product nofilter}{/if}
		{if !$product.exclude_from_calculate}
			{include file="buttons/button.tpl" but_href="checkout.delete?cart_id=`$key`&redirect_mode=`$runtime.mode`" but_meta="ty-order-products__item-delete delete" but_target_id="cart_status*" but_role="delete" but_name="delete_cart_item"}
		{/if}
		<div class="ty-order-products__price">
			{$product.amount}&nbsp;x&nbsp;{include file="common/price.tpl" value=$product.display_price}
		</div>
		{if "Y"==$product.package}{include file="addons/my_product_packages/components/package_products.tpl" package_products=$product.package_products}{/if}
		{include file="common/options_info.tpl" product_options=$product.product_options no_block=true}
		{hook name="block_checkout:product_extra"}{/hook}
	</li>
{/if}
   