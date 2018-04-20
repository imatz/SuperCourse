        <div class="ty-compact-list__item">
            <div class="ty-compact-list__content">
                <div class="ty-compact-list__image">
                  
                    {include file="common/image.tpl" image_width=$image_width image_height=$image_height images=$product.main_pair obj_id=$obj_id_prefix}
                   
                    {assign var="discount_label" value="discount_label_`$obj_prefix``$obj_id`"}
                    {$smarty.capture.$discount_label nofilter}
                </div>
                
                <div class="ty-compact-list__title">
                    {assign var="name" value="name_$obj_id"}{$smarty.capture.$name nofilter}

                    {$sku = "sku_`$obj_id`"}
                    {$smarty.capture.$sku nofilter}

                </div>

                <div class="ty-compact-list__controls">
                    <div class="ty-compact-list__price">
                        {assign var="old_price" value="old_price_`$obj_id`"}
                        {assign var="price" value="price_`$obj_id`"}

                        {if $smarty.capture.$old_price|trim}
                            {$smarty.capture.$old_price nofilter}
                        {/if}
                        {$smarty.capture.$price nofilter}
                    </div>

                    {if !$smarty.capture.capt_options_vs_qty}
                        {assign var="product_options" value="product_options_`$obj_id`"}
                        {$smarty.capture.$product_options nofilter}

                        {assign var="qty" value="qty_`$obj_id`"}
                        {$smarty.capture.$qty nofilter}
                    {/if}

                    {if $show_add_to_cart}
                        {assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
                        {$smarty.capture.$add_to_cart nofilter}
                    {/if}
                </div>
            </div>
        </div>