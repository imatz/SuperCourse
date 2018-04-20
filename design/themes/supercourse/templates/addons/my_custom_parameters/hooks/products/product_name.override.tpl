{if $show_name}
<strong>{$product.product nofilter}</strong>
{elseif $show_trunc_name}
<strong>{$product.product|truncate:44:"...":true nofilter}</strong>
{/if}