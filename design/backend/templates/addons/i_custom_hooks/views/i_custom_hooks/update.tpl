{*

	Author: Ioannis Matziaris [imatz]
	Email: imatzgr@gnail.com
	Date: October 2014
	Details:
	Copyrights: All copyrights are reserved

*}
{if $hook_data.custom_hook_id}
    {assign var="id" value=$hook_data.custom_hook_id}
{else}
    {assign var="id" value=0}
{/if}

{capture name="mainbox"}
	<form action="{""|fn_url}" method="post" name="hooks_update_form" class="form-horizontal form-edit {if ""|fn_check_form_permissions} cm-hide-inputs{/if}" enctype="multipart/form-data">
	<input type="hidden" name="fake" value="1" />
	<input type="hidden" name="hook_id" value="{$id}" />
	<input type="hidden" name="selected_section" value="{$smarty.request.selected_section}" />

	{capture name="tabsbox"}

		<div id="content_detailed">
			<div class="control-group">
                <label for="elm_file_name" class="control-label cm-required">{__("file")}</label>
                <div class="controls">
                    <input class="input-large" type="text" name="hook_data[file]" id="elm_file_name" size="55" value="{$hook_data.file nofilter}" />
                    <p>{__("theme_vars_in_file_name_notification")}</p>
                </div>

            </div>

            <div class="control-group">
                <label class="control-label" for="elm_comments">{__("comments")}:</label>
                <div class="controls">
                    <textarea id="elm_comments" name="hook_data[comments]" cols="55" rows="2" class="input-large">{$hook_data.comments}</textarea>
                </div>
            </div>
            
			<div class="control-group">
                <label for="elm_installed" class="control-label">{__("installed")}</label>
                <div class="controls">
                	{include file="addons/i_custom_hooks/views/i_custom_hooks/components/installed_status.tpl" installed=$hook_data.installed}                    
                </div>
            </div>

            {include file="common/subheader.tpl" title=__("open_position") target="#acc_open"}
            <div id="acc_open" class="collapsed in">

                <div class="control-group">
                    <label class="control-label cm-required" for="elm_open_hook_position">{__("open_hook_position")}:</label>
                    <div class="controls">
                        <textarea name="hook_data[open_hook_position]" id="elm_open_hook_position" cols="55" rows="2" class="input-large">{$hook_data.open_hook_position nofilter}</textarea>
                    </div>
                </div>

                <div class="control-group">
			        <label class="control-label cm-required" for="elm_open_occurrence">{__("open_occurrence")}:</label>
			        <div class="controls">
			            <input type="text" name="hook_data[open_occurrence]" id="elm_open_occurrence" size="10" value="{$hook_data.open_occurrence|default:'1'}" class="input-text-short" />
			        </div>
			    </div>

			    <div class="control-group">
		            <label class="control-label" for="elm_open_hook_order">{__("open_hook_order")}:</label>
		            <div class="controls">
		            <select id="elm_open_hook_order" name="hook_data[open_hook_order]">
	                    <option {if $hook_data.open_hook_order == "A" || !$id}selected="selected"{/if} value="A">{__("above")}</option>
	                    <option {if $hook_data.open_hook_order == "B"}selected="selected"{/if} value="B">{__("below")}</option>
		            </select>
		            </div>
		        </div>

	        	<div class="control-group">
                    <label class="control-label cm-required" for="elm_open_hook">{__("open_hook")}:</label>
                    <div class="controls">
                        <textarea name="hook_data[open_hook]" id="elm_open_hook" cols="55" rows="2" class="input-large">{$hook_data.open_hook nofilter}</textarea>
                    </div>
                </div>
            </div>		            

            {include file="common/subheader.tpl" title=__("close_position") target="#acc_close"}
            <div id="acc_close" class="collapsed in">

            	<div class="control-group">
                    <label class="control-label" for="elm_close_hook_position">{__("close_hook_position")}:</label>
                    <div class="controls">
                        <textarea name="hook_data[close_hook_position]" id="elm_close_hook_position" cols="55" rows="2" class="input-large">{$hook_data.close_hook_position nofilter}</textarea>
                    </div>
                </div>

                <div class="control-group">
			        <label class="control-label" for="elm_close_occurrence">{__("close_occurrence")}:</label>
			        <div class="controls">
			            <input type="text" name="hook_data[close_occurrence]" id="elm_close_occurrence" size="10" value="{$hook_data.close_occurrence|default:'1'}" class="input-text-short" />
			        </div>
			    </div>

			    <div class="control-group">
		            <label class="control-label" for="elm_open_hook_order">{__("close_hook_order")}:</label>
		            <div class="controls">
		            <select id="elm_open_hook_order" name="hook_data[close_hook_order]">
	                    <option {if $hook_data.close_hook_order == "A"}selected="selected"{/if} value="A">{__("above")}</option>
	                    <option {if $hook_data.close_hook_order == "B" || !$id}selected="selected"{/if} value="B">{__("below")}</option>
		            </select>
		            </div>
		        </div>

		        <div class="control-group">
                    <label class="control-label" for="elm_close_hook">{__("close_hook")}:</label>
                    <div class="controls">
                        <textarea name="hook_data[close_hook]" id="elm_close_hook" cols="55" rows="2" class="input-large">{$hook_data.close_hook nofilter}</textarea>
                    </div>
                </div>
            </div>        		
		</div>
	{/capture}
	{include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox group_name=$runtime.controller active_tab=$smarty.request.selected_section track=true}
	</form>
{/capture}

{capture name="buttons"}
    {if $id}
        {include file="common/view_tools.tpl" url="i_custom_hooks.update?hook_id="}

        {capture name="tools_list"}
            <li>{btn type="list" href="i_custom_hooks.install?hook_id=$id&caller=update" text=__("install")}</li>
            <li>{btn type="list" href="i_custom_hooks.uninstall?hook_id=$id&caller=update" text=__("uninstall")}</li>
            <li class="divider"></li>
            <li>{btn type="list" text=__("check_installation") href="i_custom_hooks.check_install?hook_id=`$id`&caller=update"}</li>
            <li class="divider"></li>
            <li>{btn type="list" class="cm-confirm" text=__("delete_custom_hook") data=["data-ca-confirm-text" => "{__("custom_hook_deletion_side_effects")}"] href="i_custom_hooks.delete?hook_id=`$id`&caller=update"}</li>
        {/capture}
        {dropdown content=$smarty.capture.tools_list}
    {/if}
    {include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="hooks_update_form" but_name="dispatch[i_custom_hooks.update]" save=$id}
{/capture}

{if !$id}
    {include file="common/mainbox.tpl" title=__("new_custom_hook") sidebar=$smarty.capture.sidebar sidebar_position="left" content=$smarty.capture.mainbox buttons=$smarty.capture.buttons}
{else}
    {include file="common/mainbox.tpl" sidebar=$smarty.capture.sidebar sidebar_position="left" title="{__("editing_custom_hook")}: `$hook_data.file`" content=$smarty.capture.mainbox select_languages=true buttons=$smarty.capture.buttons adv_buttons=$smarty.capture.adv_buttons select_languages=false}
{/if}