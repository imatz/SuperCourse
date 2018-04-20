{capture name="mainbox"}
<form action="{""|fn_url}" method="post" name="popups_form" class="cm-hide-inputs">
<input type="hidden" name="fake" value="1" />

{include file="common/pagination.tpl" save_current_page=true div_id=$smarty.request.content_id}
{assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

{assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
{assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
{assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

{if $popups}
<table width="100%" class="table table-middle">
<thead>
<tr>    
    <th width="1%">       
        {include file="common/check_items.tpl" class="cm-no-hide-input"}
    </th>
    <th width="5%" class="shift-left"><a class="cm-ajax" href="{"`$c_url`&sort_by=priority&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("priority")}</a></th>
    <th width="15%" class="shift-left"><a class="cm-ajax" href="{"`$c_url`&sort_by=name&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("name")}</a></th>
    <th width="10%">{__("width")}</th>
    <th width="10%">{__("height")}</th>
    <th width="5%">{__("auto_size")}</th>
    <th width="5%">{__("delay")}</th>
    <th width="5%">{__("ttl")}</th>
    <th width="5%"><a class="cm-ajax" href="{"`$c_url`&sort_by=stop_other&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("stop_other")}</a></th>
    <th width="5%"><a class="cm-ajax" href="{"`$c_url`&sort_by=not_closable&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("not_closable")}</a></th>
    <th width="5%">&nbsp;</th>
    <th class="right" width="10%"><a class="cm-ajax" href="{"`$c_url`&sort_by=not_closable&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}</a></th>
</tr>
</thead>
{foreach from=$popups item=n}
<tbody>
<tr class="cm-row-status-{$n.status|lower}" valign="top" >
    {assign var="allow_save" value=$n|fn_allow_save_object:"popups"}
    {if $allow_save}
        {assign var="no_hide_input" value="cm-no-hide-input"}
        {assign var="display" value=""}
    {else}
        {assign var="no_hide_input" value=""}
        {assign var="display" value="text"}
    {/if}
    <td class="left {$no_hide_input}">
        <input type="checkbox" name="popup_ids[]" value="{$n.popup_id}" class="cm-item" /></td>
    <td class="{$no_hide_input}">
	    <input type="text" name="popup_data[{$n.popup_id}][priority]" size="10" value="{$n.priority}" class="input-micro" />
    </td>
	<td class="{$no_hide_input}">
		{*<input type="text" name="popup_data[{$n.popup_id}][name]" size="50" value="{$n.name}" class="input-small" />*}
		<a href="{"power_popup.update?popup_id=`$n.popup_id`"|fn_url}">{$n.name nofilter}</a>
        {include file="views/companies/components/company_name.tpl" object=$n}
    </td>
	<td class="{$no_hide_input}">
        <input type="text" name="popup_data[{$n.popup_id}][width]" size="10" value="{$n.width}" class="input-small" />
    </td>
    <td class="{$no_hide_input}">
        <input type="text" name="popup_data[{$n.popup_id}][height]" size="10" value="{$n.height}" class="input-small" />
    </td>
	<td class="{$no_hide_input}">
		<input type="hidden" name="popup_data[{$n.popup_id}][auto_size]" value="N" />
        <input type="checkbox" name="popup_data[{$n.popup_id}][auto_size]" {if $n.auto_size=="Y"}checked="checked"{/if} value="Y"/>
    </td>
	<td class="{$no_hide_input}">
        <input type="text" name="popup_data[{$n.popup_id}][delay]" size="10" value="{$n.delay}" class="input-micro" />
    </td>
	<td class="{$no_hide_input}">
		<input type="text" name="popup_data[{$n.popup_id}][ttl]" size="10" value="{$n.ttl}" class="input-micro" />
    </td>
	<td class="{$no_hide_input}">
        <input type="hidden" name="popup_data[{$n.popup_id}][stop_other]" value="N" />
        <input type="checkbox" name="popup_data[{$n.popup_id}][stop_other]" {if $n.stop_other=="Y"}checked="checked"{/if} value="Y"/>
    </td>
   	<td class="{$no_hide_input}">
        <input type="hidden" name="popup_data[{$n.popup_id}][not_closable]" value="N" />
        <input type="checkbox" name="popup_data[{$n.popup_id}][not_closable]" {if $n.not_closable=="Y"}checked="checked"{/if} value="Y"/>
    </td>
    <td class="center nowrap">
        {capture name="tools_list"}
            {if $allow_save}
                <li>{btn type="list" text=__("edit") href="power_popup.update?popup_id=`$n.popup_id`"}</li>
                <li>{btn type="list" class="cm-confirm" text=__("delete") href="power_popup.delete?popup_id=`$n.popup_id`"}</li>
            {/if}
        {/capture}
        <div class="hidden-tools right">
            {dropdown content=$smarty.capture.tools_list}
        </div>
    </td>
    <td class="right nowrap">
        {include file="common/select_popup.tpl" id=$n.popup_id status=$n.status hidden="" object_id_name="popup_id" table="cp_popups" popup_additional_class="`$no_hide_input`" display=$display}
    </td>
</tr>
{/foreach}
</tbody>
</table>
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}

{include file="common/pagination.tpl" div_id=$smarty.request.content_id}

{capture name="adv_buttons"}
    {include file="common/tools.tpl" tool_href="power_popup.add" prefix="top" title=__("add_popup") hide_tools=true}
{/capture}

{capture name="buttons"}
    {capture name="tools_list"}
        {if $popups}
            <li>{btn type="delete_selected" dispatch="dispatch[power_popup.m_delete]" form="popups_form"}</li>
        {/if}
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
	{if $popups}
        {include file="buttons/save.tpl" but_name="dispatch[power_popup.m_update]" but_role="submit-link" but_target_form="popups_form"}
    {/if}
{/capture}
</form>

{/capture}
{include file="common/mainbox.tpl" title=__("popups") content=$smarty.capture.mainbox adv_buttons=$smarty.capture.adv_buttons select_languages=true buttons=$smarty.capture.buttons content_id="manage_popups"}
