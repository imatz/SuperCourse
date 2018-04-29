{if $checkout_account_exists}
	{*koce ola ta alla*}
	{assign var="popup" value=null}
	{capture name="main_popup"}
		<input type="hidden" name="popup_id" value="{$popup.popup_id}">
		<div class="my_checkout_login_or_continue">
			<div id="my_checkout_login_or_continue_text">
				{$checkout_account_exists}
			</div>
			<a href="{"my_changes.my_info"|fn_url}" class="ty-btn ty-btn__secondary ty-float-left" rel="nofollow">{__("login")}</a>
			{include file="buttons/button.tpl" but_text=__("continue_as_retail_customer") but_meta="ty-btn__secondary ty-float-right cm-dialog-closer" }
		</div>
	{/capture}
	{include file="common/popupbox.tpl" act="general" id="checkout_login_or_continue_popup" content=$smarty.capture.main_popup text="{__('checkout_login_or_continue')}" link_text="checkout_login_or_continue_popup" link_meta="text-button hidden"}
	
	<script>

		{literal}
			function open_checkout_login_or_continue_popup()
			{
				var _e = $('#opener_checkout_login_or_continue_popup');
				var params = $.ceDialog('get_params', _e);
				params['nonClosable'] = true;
				$('#' + _e.data('caTargetId')).ceDialog('open', params);
				$('.ui-widget-header.ui-dialog-titlebar button').hide();
				return false;
			}
			setTimeout(open_checkout_login_or_continue_popup, 0);
		{/literal}
	</script>
{/if}