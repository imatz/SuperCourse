{if $auth.tim_lian && $auth.tim_lian=="TL" || $auth.tim_lian=="LT"}
{include file="common/subheader.tpl" title=__("tim_lian")}
 <div class="ty-profile-field__switch ty-tim_lian-switch clearfix">
	<div class="ty-profile-field__switch-actions">
		<input class="radio" type="radio" name="tim_lian" value="T" id="tim_lian_invoice" {if "T"==$cart.tim_lian}checked="checked"{/if} /><label for="tim_lian_invoice">{__("invoice")}</label>
		<input class="radio" type="radio" name="tim_lian" value="L" id="tim_lian_receipt" {if "L"==$cart.tim_lian}checked="checked"{/if} /><label for="tim_lian_receipt">{__("receipt")}</label>
	</div>
</div>
{/if}

{include file="common/subheader.tpl" title=__("customer_notes")}
<ul class="ty-order-products__list order-product-list">
   
	{foreach from=$cart_products key="key" item="product" name="cart_products"}
		
			{if !$cart.products.$key.extra.parent}
				<li class="ty-order-products__item">
					<span class="ty-product-notes__a">{if "Y"==$product.package}<em>{__("my_package")}:</em> {$product.product nofilter}, [{$product.product_code}] 
					{else}{$product.product nofilter}{/if}</span>
					<div class="ty-order-products__price">
						{$product.amount}&nbsp;x&nbsp;{include file="common/price.tpl" value=$product.display_price}
					</div>
					<textarea class="ty-product-notes__text" name="product_notes[{$key}]" cols="30" rows="2">{$cart.products.{$key}.extra.notes}</textarea>
				</li>
			{/if}
		
	{/foreach}
 </ul>
 