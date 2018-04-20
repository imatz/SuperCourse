
        <div class="ty-scroller-list__item">
            {assign var="obj_id" value="scr_`$block.block_id`000`$product.product_id`"}
            <div class="ty-scroller-list__img-block">
                {include file="common/image.tpl" assign="object_img" images=$product.main_pair image_width=$block.properties.thumbnail_width image_height=$block.properties.thumbnail_width no_ids=true lazy_load=true}
				{$object_img nofilter}
                {if $block.properties.enable_quick_view == "Y"}
                    {include file="views/products/components/quick_view_link.tpl" quick_nav_ids=$quick_nav_ids}
                {/if}
            </div>
            <div class="ty-scroller-list__description">
                {strip}
                    {include file="blocks/list_templates/simple_list.tpl" product=$product show_trunc_name=true show_price=true show_add_to_cart=$_show_add_to_cart but_role="action" hide_price=$_hide_price hide_qty=true show_discount_label=true}
                {/strip}
            </div>
        </div>
