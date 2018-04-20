{*

	Author Ioannis Matziaris [imatz] - imatzgr@gmail.com - February 2014

	my custom hooks - manage
*}

{capture name="mainbox"}
	<form action="{""|fn_url}" method="post" name="manage_custom_hooks_form">
        {include file="common/pagination.tpl" save_current_page=true save_current_url=true div_id=$smarty.request.content_id}

        {assign var="c_url" value=$config.current_url|fn_query_remove:"sort_by":"sort_order"}

        {assign var="rev" value=$smarty.request.content_id|default:"pagination_contents"}
        {assign var="c_icon" value="<i class=\"exicon-`$search.sort_order_rev`\"></i>"}
        {assign var="c_dummy" value="<i class=\"exicon-dummy\"></i>"}

        {if $custom_hooks}
            <table width="100%" class="table table-middle">
                <thead>
                <tr>
                    <th width="5%" class="left">
                        {include file="common/check_items.tpl" check_statuses=''|fn_get_default_status_filters:true}
                    </th>
                    <th width="80%"><a class="cm-ajax" href="{"`$c_url`&sort_by=file&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("file")}{if $search.sort_by == "file"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                    <th width="5%">&nbsp;</th>
                    <th width="10%" class="right"><a class="cm-ajax" href="{"`$c_url`&sort_by=installed&sort_order=`$search.sort_order_rev`"|fn_url}" data-ca-target-id={$rev}>{__("status")}{if $search.sort_by == "installed"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}</a></th>
                </tr>
                </thead>
                 
        	{foreach from=$custom_hooks item=custom_hook}
                {assign var="fake_status" value="D"}
                {if $custom_hook.installed=="S"}
                    {assign var="fake_status" value="A"}
                {/if}
                <tr class="cm-row-status-{$fake_status|lower}">
                    <td class="left">
                        <input type="checkbox" name="hook_ids[]" value="{$custom_hook.custom_hook_id}" class="checkbox cm-item cm-item-status-{$product.status|lower}" />
                    </td>
                    <td>
                        <a class="row-status" href="{"i_custom_hooks.update?hook_id=`$custom_hook.custom_hook_id`"|fn_url}">{$custom_hook.file nofilter}</a>                        
                    </td>
                    <td class="nowrap">
                        <div class="hidden-tools">
                            {capture name="tools_list"}
                                <li>{btn type="list" text=__("edit") href="i_custom_hooks.update?hook_id=`$custom_hook.custom_hook_id`"}</li>
                                {if $custom_hook.installed=="S"}
                                    <li>{btn type="list" text=__("delete") class="cm-confirm" data=["data-ca-confirm-text" => "{__("custom_hook_deletion_side_effects")}"] href="i_custom_hooks.delete?hook_id=`$custom_hook.custom_hook_id`&uninstalled=Y"}</li> 
                                {else}
                                    <li>{btn type="list" text=__("delete") class="cm-confirm" href="i_custom_hooks.delete?hook_id=`$custom_hook.custom_hook_id`&uninstalled=N"}</li> 
                                {/if}
                                <li class="divider"></li>
                                <li>{btn type="list" text=__("install") href="i_custom_hooks.install?hook_id=`$custom_hook.custom_hook_id`"}</li>
                                <li>{btn type="list" text=__("uninstall") href="i_custom_hooks.uninstall?hook_id=`$custom_hook.custom_hook_id`"}</li>
                                <li class="divider"></li>
                                <li>{btn type="list" text=__("check_installation") href="i_custom_hooks.check_install?hook_id=`$custom_hook.custom_hook_id`"}</li>
                             {/capture}
                            {dropdown content=$smarty.capture.tools_list}
                        </div>
                    </td>
                    <td class="right nowrap">
                        {include file="addons/i_custom_hooks/views/i_custom_hooks/components/installed_status.tpl" installed=$custom_hook.installed}
                    </td>
        		</tr>
        	{/foreach}
            </table>  
        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}
        {capture name="buttons"}
            {capture name="tools_items"}
                {if $custom_hooks}
                    <li>{btn type="delete_selected" dispatch="dispatch[i_custom_hooks.m_delete]" form="manage_custom_hooks_form"}</li>
                    <li class="divider"></li>
                    <li>{btn type="list" text=__("install_selected") class="cm-confirm" data=["data-ca-confirm-text" => "{__("custom_hooks_installations_warning")}"] dispatch="dispatch[i_custom_hooks.m_install]" form="manage_custom_hooks_form"}</li>
                    <li>{btn type="list" text=__("uninstall_selected") class="cm-confirm" data=["data-ca-confirm-text" => "{__("custom_hooks_uninstallations_warning")}"] dispatch="dispatch[i_custom_hooks.m_uninstall]" form="manage_custom_hooks_form"}</li>
                    <li class="divider"></li>
                    <li>{btn type="list" text=__("check_installation_selected") dispatch="dispatch[i_custom_hooks.m_check_installation]" form="manage_custom_hooks_form"}</li>
                {/if}
            {/capture}
            {dropdown content=$smarty.capture.tools_items}
        {/capture}

        <div class="clearfix">
            {include file="common/pagination.tpl" div_id=$smarty.request.content_id}
        </div>
    </form>
{/capture}

{capture name="adv_buttons"}
    {btn type="add" title=__("add_custom_hook") href="i_custom_hooks.add"}
{/capture}

{capture name="sidebar"}
{/capture}

{include file="common/mainbox.tpl" title=__("custom_hooks") content=$smarty.capture.mainbox sidebar=$smarty.capture.sidebar adv_buttons=$smarty.capture.adv_buttons select_languages=false buttons=$smarty.capture.buttons content_id="manage_custom_hooks"}