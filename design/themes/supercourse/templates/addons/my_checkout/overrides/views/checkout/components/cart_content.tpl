{assign var="result_ids" value="cart_items,checkout_totals,sc-cart-buttons-top,checkout_steps,cart_status*,checkout_cart"}

<form name="checkout_form" class="cm-check-changes cm-ajax cm-ajax-full-render" action="{""|fn_url}" method="post" id="checkout_form" enctype="multipart/form-data">
<input type="hidden" name="redirect_mode" value="cart" />
<input type="hidden" name="result_ids" value="{$result_ids}" />
<script>
	{literal}
	$(document).ready(function(){
		$("body").on("click", ".cm-decrease, .cm-increase", function(){
			var inp = $('input', $(this).closest('.cm-value-changer'));
			var step = 1;
			var min_qty = 0;
			if (inp.attr('data-ca-step')) {
				step = parseInt(inp.attr('data-ca-step'));
			}
			if(inp.data('caMinQty')) {
				min_qty = parseInt(inp.data('caMinQty'));
			}
			var new_val = parseInt(inp.val()) + (($(this).is('a.cm-increase') || $(this).parents('a.cm-increase').length) ? step : -step);

			inp.val(new_val > min_qty ? new_val : min_qty);
			inp.keypress();
			$(this).addClass("cm-submit");
		});
	});
	{/literal}	
</script>


<h1 class="ty-mainbox-title">{__("cart_contents")}</h1>

<div id="sc-cart-buttons-top" class="buttons-container ty-cart-content__top-buttons clearfix  cm-reload">
    <div class="ty-float-left ty-cart-content__left-buttons ">
        {include file="buttons/continue_shopping.tpl" but_href=$continue_url|fn_url }
    {if $cart_products}  
      {include file="buttons/clear_cart.tpl" but_href="checkout.clear" but_role="text" but_meta="cm-confirm ty-cart-content__clear-button"}
    </div>
    <div class="ty-float-right ty-cart-content__right-buttons">
        {include file="buttons/update_cart.tpl" but_id="button_cart" but_name="dispatch[checkout.update]"}
        {if $payment_methods}
            {assign var="m_name" value="checkout"}
            {assign var="link_href" value="checkout.checkout"}
            {include file="buttons/proceed_to_checkout.tpl" but_href=$link_href but_meta=""}
        {/if}
    {/if}
    </div>
</div>

{include file="views/checkout/components/cart_items.tpl" disable_ids="button_cart"}

</form>

{include file="views/checkout/components/checkout_totals.tpl" location="cart"}

<div id="sc-cart-buttons-bottom" class="buttons-container ty-cart-content__bottom-buttons clearfix  cm-reload">
    <div class="ty-float-left ty-cart-content__left-buttons">
        {include file="buttons/continue_shopping.tpl" but_href=$continue_url|fn_url}
    {if $cart_products}  
    </div>
    <div class="ty-float-right ty-cart-content__right-buttons">
        {include file="buttons/update_cart.tpl" but_external_click_id="button_cart" but_meta="cm-external-click"}
        {if $payment_methods}
            {assign var="m_name" value="checkout"}
            {assign var="link_href" value="checkout.checkout"}
            {include file="buttons/proceed_to_checkout.tpl" but_href=$link_href}
    
        {/if}
    {/if}
    </div>
    
</div>
{if $checkout_add_buttons}
    <div class="ty-cart-content__payment-methods payment-methods" id="payment-methods">
        <span class="ty-cart-content__payment-methods-title payment-metgods-or">{__("or_use")}</span>
        <table class="ty-cart-content__payment-methods-block">
            <tr>
                {foreach from=$checkout_add_buttons item="checkout_add_button"}
                    <td class="ty-cart-content__payment-methods-item">{$checkout_add_button nofilter}</td>
                {/foreach}
            </tr>
    </table>
    <!--payment-methods--></div>
{/if}
