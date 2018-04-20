{** slides section **}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" name="slides_form" class=" cm-hide-inputs" enctype="multipart/form-data">
<input type="hidden" name="fake" value="1" />

{if $slides}
<table class="table table-middle">
<thead>
<tr>
    <th width="1%" class="left">
        {include file="common/check_items.tpl" class="cm-no-hide-input"}</th>
    <th>{__("slide")}</th>
    <th>{__("type")}</th>
    <th width="6%">&nbsp;</th>
    <th width="10%" class="right">{__("status")}</th>
</tr>
</thead>
{foreach from=$slides item=slide}
<tr class="cm-row-status-{$slide.status|lower}">
    {assign var="allow_save" value=$slide|fn_allow_save_object:"slides"}

    {if $allow_save}
        {assign var="no_hide_input" value="cm-no-hide-input"}
    {else}
        {assign var="no_hide_input" value=""}
    {/if}

    <td class="left">
        <input type="checkbox" name="slide_ids[]" value="{$slide.slide_id}" class="cm-item {$no_hide_input}" /></td>
    <td class="{$no_hide_input}">
        <a class="row-status" href="{"slides.update?slide_id=`$slide.slide_id`"|fn_url}">{$slide.slide}</a>
        {include file="views/companies/components/company_name.tpl" object=$slide}
    </td>
    <td class="nowrap row-status {$no_hide_input}">{if $slide.type == "G"}{__("graphic_slide")}{elseif $slide.type == "V"}{__("video_slide")}{else}{__("text_slide")}{/if}</td>
    <td>
        {capture name="tools_list"}
            <li>{btn type="list" text=__("edit") href="slides.update?slide_id=`$slide.slide_id`"}</li>
        {if $allow_save}
            <li>{btn type="list" class="cm-confirm" text=__("delete") href="slides.delete?slide_id=`$slide.slide_id`"}</li>
        {/if}
        {/capture}
        <div class="hidden-tools">
            {dropdown content=$smarty.capture.tools_list}
        </div>
    </td>
    <td class="right">
        {include file="common/select_popup.tpl" id=$slide.slide_id status=$slide.status hidden=true object_id_name="slide_id" table="slides" popup_additional_class="`$no_hide_input` dropleft"}
    </td>
</tr>
{/foreach}
</table>
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}

{capture name="buttons"}
    {capture name="tools_list"}
        {if $addons.statistics.status == "A"}
            <li>{btn type="list" text=__("slides_statistics") href="statistics.slides"}</li>
             {if $slides}
                <li class="divider"></li>
            {/if}
        {/if}
        {if $slides}
            <li>{btn type="delete_selected" dispatch="dispatch[slides.m_delete]" form="slides_form"}</li>
        {/if}
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
{/capture}
{capture name="adv_buttons"}
    {include file="common/tools.tpl" tool_href="slides.add" prefix="top" hide_tools="true" title=__("add_slide") icon="icon-plus"}
{/capture}

</form>

{/capture}
{include file="common/mainbox.tpl" title=__("slides") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons select_languages=true}

{** ad section **}