{capture name="section"}

<form action="{""|fn_url}" name="advanced_search_form" method="get" class="{$form_meta}">
<input type="hidden" name="search_performed" value="Y" />

{if $put_request_vars}
    {array_to_fields data=$smarty.request skip=["callback"]}
{/if}

{$search_extra nofilter}


<div class="ty-control-group">
    <label class="ty-control-group__title">{__("status")}</label>
    <input class="radio" type="radio" name="status" value="" {if empty($search.status)}checked="checked"{/if}/><span class="radio">{__("all")}</span>
    <input class="radio" type="radio" name="status" value="A" {if $search.status == "A"}checked="checked"{/if}/><span class="radio">{__("active")}</span>
    <input class="radio" type="radio" name="status" value="D" {if $search.status == "D"}checked="checked"{/if}/><span class="radio">{__("disabled")}</span>
</div>

<div class="ty-control-group">
    <label class="ty-control-group__title">{__("type")}</label>
    <input class="radio" type="radio" name="creation" value="" {if  empty($search.creation)}checked="checked"{/if}/><span class="radio">{__("all")}</span>
    <input class="radio" type="radio" name="creation" value="Q" {if $search.creation == "Q"}checked="checked"{/if}/><span class="radio">{__("quick_package")}</span>
    <input class="radio" type="radio" name="creation" value="S" {if $search.creation == "S"}checked="checked"{/if}/><span class="radio">{__("standard_package")}</span>
</div>


<div class="ty-search-form__buttons-container buttons-container">
    {include file="buttons/search.tpl" but_name="dispatch[`$dispatch`]"}&nbsp;&nbsp;{__("or")}<a class="ty-btn ty-btn__secondary cm-reset-link">{__("reset")}</a>
</div>

</form>

{/capture}
{include file="addons/my_product_packages/common/open_section.tpl" section_title=__("filters") section_content=$smarty.capture.section class="ty-search-form"}
