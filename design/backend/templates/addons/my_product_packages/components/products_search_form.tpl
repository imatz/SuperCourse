
<div class="sidebar-row">
<h6>{__("search")}</h6>
{literal}
<script>
$( "#pp" ).autocomplete({
      source: "{/literal}{"packages.suggest"|fn_url}{literal}",
      minLength: 4
    }); 
</script>    
{/literal}

<form action="{""|fn_url}{$_page_part}" name="{$product_search_form_prefix}search_form" method="get" class="cm-disable-empty {$form_meta}">
<input type="hidden" name="type" value="{$search_type|default:"simple"}" autofocus="autofocus" />
{if $smarty.request.redirect_url}
    <input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
{/if}
{if $selected_section != ""}
    <input type="hidden" id="selected_section" name="selected_section" value="{$selected_section}" />
{/if}
<input type="hidden" name="pcode_from_q" value="Y" />

{if $put_request_vars}
    {array_to_fields data=$smarty.request skip=["callback"]}
{/if}

{$extra nofilter}

    <div class="sidebar-field">
        <label>{__("find_results_with_packages")}</label>
        <input type="text" name="q" size="20" value="{$search.q}" />
    </div>
    
    <div class="sidebar-field">
        <label>{__("find_results_with_products")}</label>
        <input type="text" name="pp" id="pp" size="20" value="{$search.pp}" />
    </div>

    <div class="sidebar-field">
        <label>{__("find_results_with_customer")}</label>
        <input type="text" name="user_login" size="20" value="{$search.user_login}" />
    </div>
   
<div class="sidebar-field in-popup">
{include file="buttons/search.tpl" but_name="dispatch[`$dispatch`]"}
</form>

</div><hr>
