{assign var="obj_prefix" value="`$block.block_id`000"}

{assign var="delim_width" value="50"}
{math equation="delim_w + image_w" assign="item_width" image_w=$block.properties.thumbnail_width delim_w = $delim_width}
{assign var="item_qty" value=$block.properties.item_quantity}

{if $block.properties.outside_navigation == "Y"}
    <div class="owl-theme ty-owl-controls">
        <div class="owl-controls clickable owl-controls-outside" id="owl_outside_nav_{$block.block_id}">
            <div class="owl-buttons">
                <div id="owl_prev_{$obj_prefix}" class="owl-prev"><i class="ty-icon-left-open-thin"></i></div>
                <div id="owl_next_{$obj_prefix}" class="owl-next"><i class="ty-icon-right-open-thin"></i></div>
            </div>
        </div>
    </div>
{/if}

<div id="scroll_list_{$block.block_id}" class="owl-carousel">
    {assign var="image_h" value=$block.properties.thumbnail_width}
    {assign var="text_h" value="65"}

    {math equation="item_qty + image_h + text_h" assign="item_height" image_h=$image_h text_h=$text_h item_qty=$item_qty}

    {foreach from=$brands item="brand" name="for_brands"}
            {assign var="obj_id" value="scr_`$block.block_id`000`$brand.variant_id`"}
            {include file="common/image.tpl" assign="object_img" class="ty-grayscale" image_width=$block.properties.thumbnail_width image_height=$block.properties.thumbnail_width images=$brand.image_pair no_ids=true lazy_load=true}
            <div class="ty-center">
                <a href="{"product_features.view?variant_id=`$brand.variant_id`"|fn_url}">{$object_img nofilter}</a>
            </div>
    {/foreach}
</div>
{include file="common/scroller_init.tpl" items=$brands prev_selector="#owl_prev_`$obj_prefix`" next_selector="#owl_next_`$obj_prefix`"}