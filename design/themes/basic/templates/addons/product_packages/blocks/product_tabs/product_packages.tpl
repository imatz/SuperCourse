{** block-description:package_info **}
{if $product && $product.product_id|fn_check_package == "Y" && $product.package_form == "Y"}
    {assign var="obj_id" value=$product.product_id}
    {include file="common/product_data.tpl" product=$product separate_buttons=$separate_buttons|default:true but_role="big" but_text=__("add_to_cart")  show_sku=true show_rating=true show_old_price=true show_price=true show_list_discount=true show_clean_price=true details_page=true show_discount_label=true show_product_amount=true show_product_options=true hide_form=$smarty.capture.val_hide_form min_qty=true show_edp=true show_add_to_cart=true show_list_buttons=true but_role="action" capture_buttons=$smarty.capture.val_capture_buttons capture_options_vs_qty=$smarty.capture.val_capture_options_vs_qty separate_buttons=$smarty.capture.val_separate_buttons show_add_to_cart=true show_list_buttons=true but_role="action" block_width=true no_ajax=$smarty.capture.val_no_ajax}
        <div class="product-info">
            {assign var="form_open" value="form_open_`$obj_id`"}
            {$smarty.capture.$form_open nofilter}

            {assign var="old_price" value="old_price_`$obj_id`"}
            {assign var="price" value="price_`$obj_id`"}
            {assign var="clean_price" value="clean_price_`$obj_id`"}
            {assign var="list_discount" value="list_discount_`$obj_id`"}
            {assign var="discount_label" value="discount_label_`$obj_id`"}

            {hook name="products:promo_text"}
            <div class="product-note">
                {$product.promo_text nofilter}
            </div>
            {/hook}

            <div class="{if $smarty.capture.$old_price|trim || $smarty.capture.$clean_price|trim || $smarty.capture.$list_discount|trim}prices-container {/if}price-wrap clearfix product-detail-price">
            {if $smarty.capture.$old_price|trim || $smarty.capture.$clean_price|trim || $smarty.capture.$list_discount|trim}
                <div class="float-left product-prices">
                    {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}&nbsp;{/if}
            {/if}

            {if !$smarty.capture.$old_price|trim || $details_page}<p class="actual-price">{/if}
                    {$smarty.capture.$price nofilter}
            {if !$smarty.capture.$old_price|trim || $details_page}</p>{/if}

            {if $smarty.capture.$old_price|trim || $smarty.capture.$clean_price|trim || $smarty.capture.$list_discount|trim}
                    {$smarty.capture.$clean_price nofilter}
                    {$smarty.capture.$list_discount nofilter}
                </div>
            {/if}

            </div>

            {if $capture_options_vs_qty}{capture name="product_options"}{$smarty.capture.product_options nofilter}{/if}
            <div class="options-wrapper indented">
                {assign var="product_options" value="product_options_`$obj_id`"}
                {$smarty.capture.$product_options nofilter}
            </div>
            {if $capture_options_vs_qty}{/capture}{/if}

            <div class="advanced-options-wrapper indented">
                {if $capture_options_vs_qty}{capture name="product_options"}{$smarty.capture.product_options nofilter}{/if}
                {assign var="advanced_options" value="advanced_options_`$obj_id`"}
                {$smarty.capture.$advanced_options nofilter}
                {if $capture_options_vs_qty}{/capture}{/if}
            </div>

            <div class="sku-options-wrapper indented">
                {assign var="sku" value="sku_`$obj_id`"}
                {$smarty.capture.$sku nofilter}
            </div>

            {if $capture_options_vs_qty}{capture name="product_options"}{$smarty.capture.product_options nofilter}{/if}
            <div class="product-fields-wrapper indented">
                <div class="product-fields-group">
                    {assign var="product_amount" value="product_amount_`$obj_id`"}
                    {$smarty.capture.$product_amount nofilter}

                    {assign var="qty" value="qty_`$obj_id`"}
                    {$smarty.capture.$qty nofilter}

                    {assign var="min_qty" value="min_qty_`$obj_id`"}
                    {$smarty.capture.$min_qty nofilter}
                </div>
            </div>
            {if $capture_options_vs_qty}{/capture}{/if}

            {assign var="product_edp" value="product_edp_`$obj_id`"}
            {$smarty.capture.$product_edp nofilter}

            {if $show_descr}
            {assign var="prod_descr" value="prod_descr_`$obj_id`"}
            <h2 class="description-title">{__("description")}</h2>
            <p class="product-description">{$smarty.capture.$prod_descr nofilter}</p>
            {/if}

            {if $capture_buttons}{capture name="buttons"}{/if}
                <div class="buttons-container">

                    {if $show_details_button}
                        {include file="buttons/button.tpl" but_href="products.view?product_id=`$product.product_id`" but_text=__("view_details") but_role="submit"}
                    {/if}

                    {assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
                    {$smarty.capture.$add_to_cart nofilter}

                    {assign var="list_buttons" value="list_buttons_`$obj_id`"}
                    {$smarty.capture.$list_buttons nofilter}

                </div>
            {if $capture_buttons}{/capture}{/if}

            {assign var="form_close" value="form_close_`$obj_id`"}
            {$smarty.capture.$form_close nofilter}

            {if $show_product_tabs}
            {include file="views/tabs/components/product_popup_tabs.tpl"}
            {$smarty.capture.popupsbox_content nofilter}
            {/if}
    </div>
{/if}