{if $slide_id == "0"}
    {assign var="slide" value=$default_name}
{else}
    {assign var="slide" value=$slide_id|fn_get_slide_name|default:"`$ldelim`slide`$rdelim`"}
{/if}

<tr {if !$clone}id="{$holder}_{$slide_id}" {/if}class="cm-js-item{if $clone} cm-clone hidden{/if}">
    {if $position_field}
        <td>
            <input type="text" name="{$input_name}[{$slide_id}]" value="{math equation="a*b" a=$position b=10}" size="3" class="input-text-short" {if $clone}disabled="disabled"{/if} />
        </td>
    {/if}
    <td><a href="{"slides.update?slide_id=`$slide_id`"|fn_url}">{$slide}</a></td>
    <td>
        {capture name="tools_list"}
            {if !$hide_delete_button && !$view_only}
                <li><a onclick="Tygh.$.cePicker('delete_js_item', '{$holder}', '{$slide_id}', 'b'); return false;">{__("delete")}</a></li>
            {/if}
        {/capture}
        <div class="hidden-tools">
            {dropdown content=$smarty.capture.tools_list}
        </div>
    </td>
</tr>