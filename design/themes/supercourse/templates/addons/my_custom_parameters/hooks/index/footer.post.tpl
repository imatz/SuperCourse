{if $login_prompt}
	{*koce ola ta alla*}
	{assign var="popup" value=null}
	{capture name="main_popup"}
		<input type="hidden" name="popup_id" value="{$popup.popup_id}">
		<div class="my_login_prompt">
				<div class="my_login_prompt_buttons ty-float-left">
					{*<a class="ty-btn ty-btn__secondary login_prompt_show_form" rel="nofollow">{__("school")}</a>
					<a class="ty-btn ty-btn__secondary login_prompt_show_form" rel="nofollow">{__("bookshop")}</a>*}
					<a href="{"my_changes.my_info"|fn_url}" class="ty-btn ty-btn__secondary" rel="nofollow">{__("school")}</a>
					<a href="{"my_changes.my_info"|fn_url}" class="ty-btn ty-btn__secondary" rel="nofollow">{__("bookshop")}</a>
					{if 'Y'==$retail_enabled}<a href="{"profiles.retail"|fn_url}" class="ty-btn ty-btn__secondary" rel="nofollow">{__("retail_customer")}</a>{/if}	
					<a href="{"profiles.register"|fn_url}" rel="nofollow" class="ty-btn ty-btn__primary">{__("register")}</a>					
				</div>
				<div class="my_login_prompt_board ty-float-right">
					{include file="blocks/static_templates/logo.tpl"}
					<div id="login_prompt_text">
						<p>{__('login_prompt_text')}</p>
					</div>	 
					<div class="ty-login-popup hidden" id="my_login_prompt_form_container">
						{include file="views/auth/login_form.tpl" style="popup" id="my_login_form_prompt"}
					</div>
					
				</div>
			
		</div>
	{/capture}
	{include file="common/popupbox.tpl" act="general" id="login_prompt_popup" content=$smarty.capture.main_popup text="{__('select_customer_type')}" link_text="login_prompt_popup" link_meta="text-button hidden"}
	
	<script>

		{literal}
			function open_login_prompt_popup()
			{
				var _e = $('#opener_login_prompt_popup');
				var params = $.ceDialog('get_params', _e);
				params['nonClosable'] = true;
				$('#' + _e.data('caTargetId')).ceDialog('open', params);
				$('.ui-widget-header.ui-dialog-titlebar button').hide();
				return false;
			}
			setTimeout(open_login_prompt_popup, 0);
			$(document).ready(function(){
				var selected_account_type=true;
				$('.login_prompt_show_form').one('click',function(){
					if(!selected_account_type) $('#login_prompt_text, #my_login_prompt_form_container').toggle();
					selected_account_type=true;
				});
			});
		{/literal}
	</script>
{/if}