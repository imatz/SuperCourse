{capture name="mainbox"}
{literal}
 <script>
    $(function(){ 
      $('.module').click(function(){
        $(this).parent().nextAll().find('.module').prop('checked',$(this).is(':checked'));
      }); 
    });
 </script>
{/literal}
<form action="{""|fn_url}" method="post" name="clear_sync_form" id="clear_sync_form">
<input type="hidden" name="redirect_url" value="{"erp_sync.clear"|fn_url}" />
<ul class="unstyled">
  <li>{include file="common/check_items.tpl" check_statuses=''|fn_get_default_status_filters:true}{__("all_clearing_modules")}<li>
{foreach from=$items key=category item=modules}
  <li><h4>{__($category)}</h4><ul class="unstyled"> 
  {foreach from=$modules item=module}
    <li><input type="checkbox" name="modules[]" value="{$module}" class="module checkbox cm-item cm-item-status-a" /> {__("`$module`_clearing_module")} <em>{__("clear_`$module`_desc")}</em></li>
  {/foreach}
  </ul></li>
  <li class="divider">&nbsp;</li>
{/foreach}
</ul>
{capture name="buttons"}
  {include file="buttons/button.tpl" but_text=__("clear") but_name="dispatch[erp_sync.clear]" but_role="submit-button" but_meta=$but_meta but_onclick=$but_onclick allow_href=true but_target_form="clear_sync_form"}
{/capture}
</form>
{/capture}


{include file="common/mainbox.tpl" title=__("erp_sync_clear") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons content_id="erp_sync_clear"}