
        <div class="ty-thumbnail-list__item">
            {assign var="form_open" value="form_open_`$obj_id`"}
            {$smarty.capture.$form_open nofilter}
			{include file="common/image.tpl" image_width="70" image_height="70" images=$product.main_pair obj_id=$obj_id_prefix no_ids=true class="ty-thumbnail-list__img"}
            <div class="ty-thumbnail-list__name">{if $block.properties.item_number == "Y"}{$smarty.foreach.products.iteration}.&nbsp;{/if}
            {assign var="name" value="name_$obj_id"}{$smarty.capture.$name nofilter}</div>

            {assign var="old_price" value="old_price_`$obj_id`"}
            {if $smarty.capture.$old_price|trim}{$smarty.capture.$old_price nofilter}&nbsp;{/if}
            
            {assign var="price" value="price_`$obj_id`"}
            {$smarty.capture.$price nofilter}

            {if $show_add_to_cart}
                <div class="ty-thumbnail-list__butons">
                    {assign var="add_to_cart" value="add_to_cart_`$obj_id`"}
                    {$smarty.capture.$add_to_cart nofilter}
                </div>
            {/if}

            {assign var="form_close" value="form_close_`$obj_id`"}
            {$smarty.capture.$form_close nofilter}
        </div>
