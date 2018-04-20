 {if $product && $product.product_id|fn_check_package == "Y" && $product.package_form == "Y" && $runtime.mode == "view"}
    {assign var="obj_id" value=$product.product_id}
    {include file="common/product_data.tpl" product=$product separate_buttons=$separate_buttons|default:true but_role="big" but_text=__("add_to_cart")}
        <div class="ty-product-block__img-wrapper">
            {hook name="products:image_wrap"}
                {if !$no_images}
                    <div class="ty-product-block__img cm-reload-{$product.product_id}" id="product_images_{$product.product_id}_update">

                        {assign var="discount_label" value="discount_label_`$obj_prefix``$obj_id`"}
                        {$smarty.capture.$discount_label nofilter}

                        {include file="views/products/components/product_images.tpl" product=$product show_detailed_link="Y" image_width=$settings.Thumbnails.product_details_thumbnail_width image_height=$settings.Thumbnails.product_details_thumbnail_height}
                    <!--product_images_{$product.product_id}_update--></div>
                {/if}
            {/hook}
        </div>
        {if $settings.Thumbnails.product_details_thumbnail_height}
        {assign var="height" value=$settings.Thumbnails.product_details_thumbnail_height+60}
        {else}
        {assign var="height" value=$settings.Thumbnails.product_details_thumbnail_width+60}
        {/if}
        <div class="ty-product-block__left">
                {hook name="products:main_info_title"}
                    {if !$hide_title}
                        <h1 class="ty-product-block-title" {live_edit name="product:product:{$product.product_id}"}>{$product.product nofilter}</h1>
                    {/if}

                    {hook name="products:brand"}
                        <div class="brand">
                            {include file="views/products/components/product_features_short_list.tpl" features=$product.header_features}
                        </div>
                    {/hook}
                {/hook}
        </div>
            

            
           
    {/if}