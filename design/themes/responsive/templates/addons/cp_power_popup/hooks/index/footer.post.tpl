{if $popup}
	{capture name="main_popup"}
		<input type="hidden" name="popup_id" value="{$popup.popup_id}">
		<div {if $popup.auto_size != "Y"}style="width: {$popup.width}px; height: {$popup.height}px;"{/if}>
			{if $popup.type == "T"}
				{if $popup.content_type == "P"}
					{assign var="page" value=$popup.page_content}
					<div class="ty-wysiwyg-content cp-popup-content">
						{hook name="pages:page_content"}
							<div {live_edit name="page:description:{$page.page_id}"}>{$page.description nofilter}</div>
						{/hook}
					</div>

					{capture name="mainbox_title"}<span {live_edit name="page:page:{$popup.page_content.page_id}"}>{$page.page}</span>{/capture}
						
					{hook name="pages:page_extra"}
					{/hook}
				{else}
					<div class="ty-wysiwyg-content cp-popup-content">
						{$popup.content nofilter}
					</div>
				{/if}
				<div class="buttons-container">
					<div class="ty-float-left">
						{include file="buttons/button.tpl" but_meta="ty-btn__secondary" but_text=__("power_yes_button") but_onclick="submitLicense({$popup.popup_id});"}
					</div>
					{if $popup.not_closable != 'Y'}
                        <div class="ty-float-right">
                            {include file="buttons/button.tpl" but_meta="ty-btn__secondary" but_text=__("power_no_button") but_onclick="cancelLicense();"}
                        </div>
                    {/if}
				</div>
			{elseif $popup.type == "A"}
				{if $popup.content_type == "P"}
					{assign var="page" value=$popup.page_content}
					<div class="ty-wysiwyg-content cp-popup-content {if $addons.cp_power_popup.use_calendar != "Y"}cp-calendar-block{/if}">
						{hook name="pages:page_content"}
							<div {live_edit name="page:description:{$page.page_id}"}>{$page.description nofilter}</div>
						{/hook}
					</div>

					{capture name="mainbox_title"}<span {live_edit name="page:page:{$popup.page_content.page_id}"}>{$page.page}</span>{/capture}
						
					{hook name="pages:page_extra"}
					{/hook}
				{else}
					<div class="ty-wysiwyg-content cp-popup-content  {if $addons.cp_power_popup.use_calendar != "Y"}cp-calendar-block{/if}">
						{$popup.content nofilter}
					</div>
				{/if}
				<div class="buttons-container">
                    <div class="ty-control-group">
                        <div class="ty-float-left">
                            <label class="ty-control-group__title" for="birthday">{__("enter_your_birthday")}</label>
                            {if $addons.cp_power_popup.use_calendar == "Y"}
                                {include file="common/calendar.tpl" date_id="birthday" date_name="user_data[birthday]" date_val=$user_data.birthday start_year="1902" end_year="0"}
                            {else}
                                <div class="calendar-item">
                                    <div>{__('month')|capitalize}:</div>
                                    <select name="v_month" id="v_month">
                                        {for $i=1 to 12}
                                            <option value="{$i}">{$i}</option>
                                        {/for}
                                    </select>
                                </div>
                                <div class="calendar-item">
                                    <div>{__('day')|capitalize}:</div>
                                    <select name="v_day" id="v_day">
                                        {for $i=1 to 31}
                                            <option value="{$i}">{$i}</option>
                                        {/for}
                                    </select>
                                </div>
                                <div class="calendar-item">
                                    <div>{__('year')|capitalize}:</div>
                                    <select name="v_year" id="v_year">
                                        {for $i=2014 to 1900 step -1}
                                            <option value="{$i}">{$i}</option>
                                        {/for}
                                    </select>
                                </div>
                                <div class="age-verification-failed">
                                    <span>{__("age_verification_failed")}</span>
                                </div>
                            {/if}
                        </div>
                        <div class="ty-float-right {if $addons.cp_power_popup.use_calendar == "Y"}popup-age-button{else}popup-age-button-calendar{/if}">
                            {include file="buttons/button.tpl" but_meta="ty-btn__secondary"  but_text=__("power_verify") but_onclick="verifyAge({$popup.popup_id});"}
                        </div>
                    </div>
				</div>
			{elseif $popup.type == "R"}
				{if $popup.content_type == "P"}
					{assign var="page" value=$popup.page_content}
					<div class="ty-wysiwyg-content cp-popup-content">
						{hook name="pages:page_content"}
							<div {live_edit name="page:description:{$page.page_id}"}>{$page.description nofilter}</div>
						{/hook}
					</div>

					{capture name="mainbox_title"}<span {live_edit name="page:page:{$popup.page_content.page_id}"}>{$page.page}</span>{/capture}
						
					{hook name="pages:page_extra"}
					{/hook}
				{elseif $popup.content_type == "R"}
                    {if $popup.content}
                        <div class="ty-wysiwyg-content cp-popup-content">
                            {$popup.content nofilter}
                        </div>
                    {/if}
                    {include file="views/products/quick_view.tpl" product=$popup.product show_sku=true show_rating=true show_old_price=true show_price=true show_list_discount=true show_clean_price=true details_page=true show_discount_label=true show_product_amount=true show_product_options=true hide_form=$smarty.capture.val_hide_form min_qty=true show_edp=true show_add_to_cart=true show_list_buttons=true but_role="action" capture_buttons=$smarty.capture.val_capture_buttons capture_options_vs_qty=$smarty.capture.val_capture_options_vs_qty  show_add_to_cart=true show_list_buttons=true but_role="action" block_width=true no_ajax=$smarty.capture.val_no_ajax show_product_tabs=true}
				{else}
					<div class="ty-wysiwyg-content cp-popup-content">
						{$popup.content nofilter}
					</div>
				{/if}
				{if $popup.not_closable != 'Y'}
                    <div class="buttons-container">
                        <div class="ty-float-right">
                            {include file="buttons/button.tpl" but_meta="ty-btn__secondary" but_text=__("close") but_onclick="cancelLicense();"}
                        </div>
                    </div>
                {/if}
			{/if}
		</div>
	{/capture}
	{include file="common/popupbox.tpl" act="general" id="power_popup" content=$smarty.capture.main_popup text="{if $popup.content_type == "P"}{$page.page}{else}{$popup.name}{/if}" wysiwyg=true link_text="power_popup" link_meta="text-button hidden"}
	
	<script>
		var nonClosable = false;
		{if $popup.not_closable == 'Y'}
			nonClosable = true;
		{/if}
        var delay = {$popup.delay} * 1000;
		{literal}
			function open_power_popup()
			{
				var _e = $('#opener_power_popup');
				var params = $.ceDialog('get_params', _e);
				
				if(nonClosable) params['nonClosable'] = true;
				
				$('#' + _e.data('caTargetId')).ceDialog('open', params);
				if(nonClosable) $('.ui-widget-header.ui-dialog-titlebar button').hide();
				return false;
			}
			setTimeout(open_power_popup, delay);
		{/literal}
	</script>
{/if}
