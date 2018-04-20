{** block-description:text_links **}

<{if $block.properties.item_number == "Y"}ol{else}ul{/if} class="text-link-list">

{foreach from=$items item="product"}
{assign var="obj_id" value="`$block.block_id`000`$product.product_id`"}
{if $product}
    <li class="text-link-list__item">
        {$product.product nofilter}
    </li>
{/if}
{/foreach}

</{if $block.properties.item_number == "Y"}ol{else}ul{/if}>
