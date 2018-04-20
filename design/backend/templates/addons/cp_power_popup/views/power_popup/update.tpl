{if $popup_data}
    {assign var="id" value=$popup_data.popup_id}
{else}
    {assign var="id" value=0}
{/if}

{assign var="allow_save" value=$popup_data|fn_allow_save_object:"popups"}
{$show_save_btn = $allow_save scope = root}

{capture name="mainbox"}

	<form action="{""|fn_url}" method="post" name="popups_update_form" class="form-horizontal form-edit {if !$allow_save} cm-hide-inputs{/if}">
	<input type="hidden" class="cm-no-hide-input" name="fake" value="1" />
	<input type="hidden" class="cm-no-hide-input" name="popup_id" value="{$id}" />

	<fieldset>
		{include file="common/subheader.tpl" title=__("general") target="#general"}
		<div id="general" class="collapse in">
			<div class="control-group">
				<label for="elm_news_name" class="control-label cm-required">{__("name")}</label>
				<div class="controls">
					<input type="text" name="popup_data[name]" id="elm_news_name" value="{$popup_data.name}" size="40" class="input-large" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="elm_popup_priority">{__("priority")}</label>
				<div class="controls">
					<input type="text" name="popup_data[priority]" id="elm_popup_priority" size="10" value="{$popup_data.priority}" class="input-micro" />
				</div>
			</div>			
			{if "MULTIVENDOR"|fn_allowed_for}
				{assign var="zero_company_id_name_lang_var" value="none"}
			{/if}
			{include file="views/companies/components/company_field.tpl"
				name="popup_data[company_id]"
				id="elm_popups_company_id"
				selected=$popup_data.company_id
				disable_company_picker=!$allow_save
			}
			<div class="control-group">
				<label class="control-label" for="elm_popup_content_type">{__("content_type")}</label>
				<div class="controls">
					<select id="elm_popup_content_type" name="popup_data[content_type]" onchange="fn_check_content_type(this);">
						<option value="T" {if !$popup_data.content_type || $popup_data.content_type == "T"}selected="selected"{/if}>{__("text")}</option>
						<option value="P" {if $popup_data.content_type == "P"}selected="selected"{/if}>{__("pages")}</option>
						<option value="R" {if $popup_data.content_type == "R"}selected="selected"{/if}>{__("product")}</option>
					</select>
				</div>
			</div>
            <div class="control-group {if $popup_data.content_type != "R"}hidden{/if}" id="product_type">
                <label class="control-label" for="elm_popup_products">{__("select_product")}</label>
                <div class="controls">
                    {assign var="product" value=$popup_data.product_id|fn_get_product_name}
                    {include file="pickers/products/picker.tpl" input_name="popup_data[product_id]" product=$product but_text=__("add_product") item_ids=$popup_data.product_id type="single"}
                </div>
            </div>
			<div class="control-group {if $popup_data.content_type != "P"}hidden{/if}" id="page_type">
				<label class="control-label" for="elm_popup_pages">{__("select_page")}</label>
				<div class="controls custom-contoller">
					{include file="pickers/pages/picker.tpl" item_ids=$popup_data.page_id but_text=__("add_page") input_name="popup_data[page_id]" picker_for="elm_popup_pages"}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="elm_popup_section">{__("section")}</label>
				<div class="controls">
					<select id="elm_popup_content_type" name="popup_data[section]" onchange="fn_check_popup_section(this);">
						<option value="A" {if !$popup_data.section || $popup_data.section == "A"}selected="selected"{/if}>{__("all")}</option>
						<option value="N" {if $popup_data.section == "N"}selected="selected"{/if}>{__("none")}</option>
						<option value="P" {if $popup_data.section == "P"}selected="selected"{/if}>{__("products")}</option>
						<option value="C" {if $popup_data.section == "C"}selected="selected"{/if}>{__("categories")}</option>
						<option value="W" {if $popup_data.section == "W"}selected="selected"{/if}>{__("pages")}</option>
						<option value="S" {if $popup_data.section == "S"}selected="selected"{/if}>{__("products_search")}</option>
						<option value="H" {if $popup_data.section == "H"}selected="selected"{/if}>{__("homepage")}</option>
						<option value="B" {if $popup_data.section == "B"}selected="selected"{/if}>{__("cart_page")}</option>
						<option value="Z" {if $popup_data.section == "Z"}selected="selected"{/if}>{__("checkout_page")}</option>
						<option value="O" {if $popup_data.section == "O"}selected="selected"{/if}>{__("order_complete")}</option>
						<option value="U" {if $popup_data.section == "U"}selected="selected"{/if}>{__("custom_location")}</option>
					</select>
					<input type="text" name="popup_data[dispatch]" id="elm_popup_custom_dispatch" size="50" value="{$popup_data.dispatch}" class="input-medium {if $popup_data.section != 'U'}hidden{/if}" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="elm_popup_type">{__("popup_type")}</label>
				<div class="controls">
					<select id="elm_popup_type" name="popup_data[type]" onchange="fn_check_popup_type(this);">
                        <option value="R" {if !$popup_data.type || $popup_data.type == "R"}selected="selected"{/if}>{__("regular_popup")}</option>
                        <option value="T" {if $popup_data.type == "T"}selected="selected"{/if} {if $popup_data.content_type == "R"}disabled="disabled"{/if}>{__("terms_and_conditions")}</option>
                        <option value="A" {if $popup_data.type == "A"}selected="selected"{/if} {if $popup_data.content_type == "R"}disabled="disabled"{/if}>{__("age_verification")}</option>
    				</select>
				</div>
			</div>
			<div class="age_verification control-group {if $popup_data.type != "A"}hidden{/if}">
				<label class="control-label" for="elm_popup_age_verification">{__("age_limit")}</label>
				<div class="controls">
					<input type="text" name="popup_data[age_limit]" id="elm_popup_age_verification" size="10" value="{$popup_data.age_limit}" class="input-small" />
				</div>
			</div>
            <div class="age_verification control-group {if $popup_data.type != "A"}hidden{/if}">
                <label class="control-label" for="elm_popup_age_verification_redirect">{__("cp_redirect_url")}</label>
                <div class="controls">
                    <input type="text" name="popup_data[redirect_url]" id="elm_popup_age_verification_redirect" size="600" value="{$popup_data.redirect_url}" class="input-large" />
                </div>
            </div>
			<div class="control-group">
				<label class="control-label" for="elm_popup_content">{__("content")}</label>
				<div class="controls">
					<textarea id="elm_popup_content" name="popup_data[content]" cols="35" rows="8" class="cm-wysiwyg input-large">{$popup_data.content}</textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="elm_popup_delay">{__("delay")}</label>
				<div class="controls">
					<input type="text" name="popup_data[delay]" id="elm_popup_delay" size="10" value="{$popup_data.delay}" class="input-small" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="elm_popup_ttl">{__("ttl")}</label>
				<div class="controls">
					<input type="text" name="popup_data[ttl]" id="elm_popup_ttl" size="10" value="{$popup_data.ttl}" class="input-small" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="elm_popups_auto_size">{__("auto_size")}</label>
				<div class="controls">
					<input type="hidden" name="popup_data[auto_size]" value="N" />
					<input type="checkbox" onchange="fn_check_popup_width(this);" name="popup_data[auto_size]" id="elm_popups_auto_size" value="Y" {if $popup_data.auto_size == "Y" || !$popup_data.auto_size}checked="checked"{/if} />
				</div>
			</div>
			<div class="control-group popup-dimensions {if $popup_data.auto_size == 'Y' || !$popup_data.auto_size} hidden {/if}">
				<label class="control-label" for="elm_popup_width">{__("width")}</label>
				<div class="controls">
					<input type="text" name="popup_data[width]" id="elm_popup_width" size="10" value="{$popup_data.width}" class="input-small" />
				</div>
			</div>
			<div class="control-group popup-dimensions {if $popup_data.auto_size == 'Y' || !$popup_data.auto_size} hidden {/if}">
				<label class="control-label" for="elm_popup_height">{__("height")}</label>
				<div class="controls">
					<input type="text" name="popup_data[height]" id="elm_popup_width" size="10" value="{$popup_data.height}" class="input-small" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="elm_popups_stop_other">{__("stop_other")}</label>
				<div class="controls">
				<input type="hidden" name="popup_data[stop_other]" value="N" />
				<input type="checkbox" name="popup_data[stop_other]" id="elm_popups_stop_other" value="Y" {if $popup_data.stop_other == "Y"}checked="checked"{/if} />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="elm_popups_not_closable">{__("not_closable")}</label>
				<div class="controls">
				<input type="hidden" name="popup_data[not_closable]" value="N" />
				<input type="checkbox" name="popup_data[not_closable]" id="elm_popups_not_closable" value="Y" {if $popup_data.not_closable == "Y"}checked="checked"{/if} />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="elm_use_avail_period">{__("use_avail_period")}</label>
				<div class="controls">
					<input type="checkbox" name="avail_period" id="elm_use_avail_period" {if $popup_data.from_date || $popup_data.to_date}checked="checked"{/if} value="Y" onclick="fn_activate_calendar(this);"/>
				</div>
			</div>
			
			{capture name="calendar_disable"}{if !$popup_data.from_date && !$popup_data.to_date}disabled="disabled"{/if}{/capture}
			<div class="control-group">
				<label class="control-label" for="elm_date_holder_from">{__("avail_from")}</label>
				<div class="controls">
				<input type="hidden" name="popup_data[from_date]" value="0" />
				{include file="common/calendar.tpl" date_id="elm_date_holder_from" date_name="popup_data[from_date]" date_val=$popup_data.from_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="elm_date_holder_to">{__("avail_till")}</label>
				<div class="controls">
				<input type="hidden" name="popup_data[to_date]" value="0" />
				{include file="common/calendar.tpl" date_id="elm_date_holder_to" date_name="popup_data[to_date]" date_val=$popup_data.to_date|default:$smarty.const.TIME start_year=$settings.Company.company_start_year extra=$smarty.capture.calendar_disable}
				</div>
			</div>
			
            {if !"ULTIMATE:FREE"|fn_allowed_for}
                <div class="control-group">
                    <label class="control-label">{__("usergroups")}:</label>
                    <div class="controls">
                        {include file="common/select_usergroups.tpl" id="ug_id" name="popup_data[usergroup_ids]" usergroups="C"|fn_get_usergroups:$smarty.const.DESCR_SL usergroup_ids=$popup_data.usergroup_ids input_extra="" list_mode=false}
                    </div>
                </div>
            {/if}			
			
			<script language="javascript">
			function fn_activate_calendar(el)
			{
				var $ = Tygh.$;
				var jelm = $(el);
				var checked = jelm.prop('checked');

				$('#elm_date_holder_from,#elm_date_holder_to').prop('disabled', !checked);
			}
			fn_activate_calendar(Tygh.$('#elm_use_avail_period'));
			</script>
			
			{include file="views/localizations/components/select.tpl" data_from=$popup_data.localization data_name="popup_data[localization]"}
			{include file="common/select_status.tpl" input_name="popup_data[status]" id="elm_popups_status" obj_id=$popup_data.popup_id obj=$popup_data}
		</div>
		
		{include file="common/subheader.tpl" title=__("assign_to") target="#assign_to"}
		<div id="assign_to" class="collapse in">
			{include file="pickers/categories/picker.tpl" multiple=true input_name="popup_data[categories]" item_ids=$popup_data.categories data_id="category_ids" use_keys="N" but_meta="pull-right"}
			{include file="pickers/products/picker.tpl" input_name="popup_data[products]" item_ids=$popup_data.products type="links" placement="right"}
		</div>
	</fieldset>
	{capture name="buttons"}
		{if !$id}
			{include file="buttons/save_cancel.tpl" but_name="dispatch[power_popup.update]" but_role="submit-link" but_target_form="popups_update_form"}
		{else}
			{if !$show_save_btn}
				{assign var="hide_first_button" value=true}
				{assign var="hide_second_button" value=true}
			{/if}
			{include file="buttons/save_cancel.tpl" but_name="dispatch[power_popup.update]" hide_first_button=$hide_first_button hide_second_button=$hide_second_button but_role="submit-link" but_target_form="popups_update_form" save=$id}
		{/if}
	{/capture}

	</form>
{/capture}

{if $id}
    {assign var="title" value="{__("editing_popup")}: `$popup_data.name`"}
{else}
    {assign var="title" value=__("new_popup")}
{/if}
{literal}
<script type="text/javascript">
	function fn_check_content_type(elm) {
		if ($(elm).val() == 'P') {
			$('#page_type').show();
			$('#page_type').removeClass('hidden');
		} else {
            $('#page_type').hide();
            $('#page_type').addClass('hidden');
            
		}
		
		if ($(elm).val() == 'R') {
			$('#product_type').show();
			$('#product_type').removeClass('hidden');
			$("#elm_popup_type option[value='T']").attr("disabled", "disabled");
            $("#elm_popup_type option[value='A']").attr("disabled", "disabled");
		}else {
            $('#product_type').hide();
            $('#product_type').addClass('hidden');
            $("#elm_popup_type option[value='T']").removeAttr("disabled");
            $("#elm_popup_type option[value='A']").removeAttr("disabled");
        }
	}
	function fn_check_popup_type(elm) {
		if ($(elm).val() == 'A') {
			$('.age_verification').show();
			$('.age_verification').removeClass('hidden');
		} else {
			$('.age_verification').hide();
			$('.age_verification').addClass('hidden');
		}
	}
	function fn_check_popup_width(elm) {
		if ($(elm).is(':checked') == false) {
			$('.popup-dimensions').show();
			$('.popup-dimensions').removeClass('hidden');
		} else {
			$('.popup-dimensions').hide();
			$('.popup-dimensions').addClass('hidden');
		}
	}
	function fn_check_popup_section(elm) {	
		if ($(elm).val() == 'U') {
			$('#elm_popup_custom_dispatch').show();
			$('#elm_popup_custom_dispatch').removeClass('hidden');
		} else {
			$('#elm_popup_custom_dispatch').hide();
			$('#elm_popup_custom_dispatch').addClass('hidden');
		}
	}
</script>
{/literal}
{include file="common/mainbox.tpl" title=$title content=$smarty.capture.mainbox select_languages=true buttons=$smarty.capture.buttons}