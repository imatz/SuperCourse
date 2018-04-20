<div class="ty-checkout-complete__order-success">
    <p>{__("text_order_placed_successfully", ["[order_id]" => $order_info.order_id])}</p>
</div>


    <div class="ty-checkout-complete__login-info ty-checkout-complete_width_full">
        {hook name="checkout:payment_instruction"}
            {if $order_info.payment_method.instructions}
                <h4 class="ty-subheader">{__("payment_instructions")}</h4>
                <div class="ty-wysiwyg-content">
                    <br>
                    {$order_info.payment_method.instructions nofilter}
                </div>
            {/if}
        {/hook}
    </div>


    {* place any code you wish to display on this page right after the order has been placed *}
    {hook name="checkout:order_confirmation"}
    {/hook}

    <div class="ty-checkout-complete__buttons buttons-container {if !$order_info || !$settings.Checkout.allow_create_account_after_order == "Y" || $auth.user_id} ty-mt-s{/if}">
        {hook name="checkout:complete_button"}
            <div class="ty-checkout-complete__buttons-right">
                {include file="buttons/continue_shopping.tpl" but_role="text" but_meta="ty-checkout-complete__button-vmid" but_href=$continue_url|fn_url}
            </div>
        {/hook}
    </div>

{capture name="mainbox_title"}{__("order")}{/capture}