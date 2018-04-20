{*Don't show in profiles*}
{if 'profiles'!=$runtime.controller}
<form action="{""|fn_url}" method="post" name="add_package_to_cart_form" enctype="multipart/form-data" class="cm-ajax cm-ajax-full-render cm-ajax-status-middle">
<input type="hidden" name="result_ids" value="cart_status*,wish_list*,checkout*,account_info*">
<input type="hidden" name="redirect_url" value="index.php">
<input type="text" class="ty-input-text-medium cm-hint" placeholder="{__("enter_package_code")}" name="package_code">
{include file="buttons/add_to_cart.tpl" but_id="button_cart_package" but_name="dispatch[checkout.add.package]" but_role="action"}
</form>
<form class="search_form" action="{""|fn_url}" name="search_form" method="get">
        <input type="hidden" name="subcats" value="Y" />
        <input type="hidden" name="status" value="A" />
        <input type="hidden" name="pshort" value="Y" />
        <input type="hidden" name="pfull" value="Y" />
        <input type="hidden" name="pname" value="Y" />
        <input type="hidden" name="pkeywords" value="Y" />
        <input type="hidden" name="search_performed" value="Y" />

        {hook name="search:additional_fields"}{/hook}

        {strip}
            {if $settings.General.search_objects}
                {assign var="search_title" value=__("search")}
            {else}
                {assign var="search_title" value=__("search_products")}
            {/if}
            <input type="text" class="ty-input-text-medium cm-hint" name="q" value="{$search.q}" id="search_input{if $smarty.capture.search_input_id}_{$smarty.capture.search_input_id}{/if}" title="{$search_title}" placeholder="{$search_title}" />            {include file="buttons/button.tpl" but_text=__("search") but_name="dispatch[products.search]" but_role="submit" but_meta="ty-btn__secondary"}
            {if $settings.General.search_objects}
                {include file="buttons/magnifier.tpl" but_name="search.results" alt=__("search")}
            {else}
                {include file="buttons/magnifier.tpl" but_name="products.search" alt=__("search")}
            {/if}
        {/strip}

        {capture name="search_input_id"}
            {math equation="x + y" x=$smarty.capture.search_input_id|default:1 y=1 assign="search_input_id"}
            {$search_input_id}
        {/capture}
</form>
{/if}