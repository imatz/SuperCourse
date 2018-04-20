{if !$smarty.request.extra}
<script type="text/javascript">
//<![CDATA[
(function(_, $) {
    _.tr('text_items_added', '{__("text_items_added")|escape:"javascript"}');

    $.ceEvent('on', 'ce.formpost_slides_form', function(frm, elm) {

        var slides = {};

        if ($('input.cm-item:checked', frm).length > 0) {
            $('input.cm-item:checked', frm).each( function() {
                var id = $(this).val();
                slides[id] = $('#slide_' + id).text();
            });

            {literal}
            $.cePicker('add_js_item', frm.data('caResultId'), slides, 'b', {
                '{slide_id}': '%id',
                '{slide}': '%item'
            });
            {/literal}

            $.ceNotification('show', {
                type: 'N', 
                title: _.tr('notice'), 
                message: _.tr('text_items_added'), 
                message_state: 'I'
            });
        }

        return false;
    });

}(Tygh, Tygh.$));
//]]>
</script>
{/if}
</head>
<form action="{$smarty.request.extra|fn_url}" data-ca-result-id="{$smarty.request.data_id}" method="post" name="slides_form">
{if $slides}
<table width="100%" class="table table-middle">
<thead>
<tr>
    <th>
        {include file="common/check_items.tpl"}</th>
    <th>{__("slide")}</th>
</tr>
</thead>
{foreach from=$slides item=slide}
<tr>
    <td>
        <input type="checkbox" name="{$smarty.request.checkbox_name|default:"slides_ids"}[]" value="{$slide.slide_id}" class="cm-item" /></td>
    <td id="slide_{$slide.slide_id}" width="100%">{$slide.slide}</td>
</tr>
{/foreach}
</table>
{else}
    <p class="no-items">{__("no_data")}</p>
{/if}

{if $slides}
<div class="buttons-container">
    {include file="buttons/add_close.tpl" but_text=__("add_slides") but_close_text=__("add_slides_and_close") is_js=$smarty.request.extra|fn_is_empty}
</div>
{/if}

</form>
