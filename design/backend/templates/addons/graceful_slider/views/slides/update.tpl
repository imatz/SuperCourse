<style>
#slide_text_section .sp-container {
	top: 30px !important;
}
#slide_text_section .sp-palette-only .sp-picker-container {
	display: block;
}
.input_opacity-size {
	width: 42px;
}
</style>



{if $slide}
	{assign var="id" value=$slide.slide_id}
{else}
	{assign var="id" value=0}
{/if}

{assign var="allow_save" value=$slide|fn_allow_save_object:"slides"}

{** slides section **}

{assign var="b_type" value=$slide.type|default:"G"}

{capture name="mainbox"}

<form action="{""|fn_url}" method="post" class="form-horizontal form-edit  {if !$allow_save} cm-hide-inputs{/if}" name="slides_form" enctype="multipart/form-data">
<input type="hidden" class="cm-no-hide-input" name="fake" value="1" />
<input type="hidden" class="cm-no-hide-input" name="slide_id" value="{$id}" />

{capture name="tabsbox"}
<div id="content_general">
	<div class="control-group">
		<label for="elm_slide_name" class="control-label cm-required">{__("name")}</label>
		<div class="controls">
		<input type="text" name="slide_data[slide]" id="elm_slide_name" value="{$slide.slide}" size="25" class="input-large" /></div>
	</div>

	{if "ULTIMATE"|fn_allowed_for}
		{include file="views/companies/components/company_field.tpl"
			name="slide_data[company_id]"
			id="slide_data_company_id"
			selected=$slide.company_id
		}
	{/if}

	<div class="control-group">
		<label for="elm_slide_position" class="control-label">{__("position_short")}</label>
		<div class="controls">
			<input type="text" name="slide_data[position]" id="elm_slide_position" value="{$slide.position|default:"0"}" size="3"/>
		</div>
	</div>

	<div class="control-group">
		<label for="elm_slide_type" class="control-label cm-required">{__("type")}</label>
		<div class="controls">
		<select name="slide_data[type]" id="elm_slide_type" onchange="elm_slide_type_change()">
			<option {if $slide.type == "G"}selected="selected"{/if} value="G">{__("graphic_slide")}</option>
			<option {if $slide.type == "V"}selected="selected"{/if} value="V">{__("video_slide")}</option>
		</select>
		</div>
	</div>
	
	<script>
		function elm_slide_type_change() {
			var slideType = document.getElementById('elm_slide_type').value;
			switch (slideType) {
				case "G":
					Tygh.$('#slide_graphic_label_bg').hide();
					Tygh.$('#slide_graphic_label_img').show();
					Tygh.$('#slide_video_section').hide();
					Tygh.$('#slide_url').show();
					Tygh.$('#slide_target').show();
					Tygh.$('#slide_text_section').hide();
					break;
				case "V":
					Tygh.$('#slide_graphic_label_img').hide();
					Tygh.$('#slide_graphic_label_bg').show();
					Tygh.$('#slide_video_section').show();
					Tygh.$('#slide_url').hide();
					Tygh.$('#slide_target').hide();
					Tygh.$('#slide_text_section').hide();
					break;
				case "T":
					Tygh.$('#slide_graphic_label_img').hide();
					Tygh.$('#slide_graphic_label_bg').show();
					Tygh.$('#slide_video_section').hide();
					Tygh.$('#slide_url').show();
					Tygh.$('#slide_target').show();
					Tygh.$('#slide_text_section').show();
					break;;
				default:
			}
		}
	</script>

	{* Image ********************************** *}
		<div class="control-group" id="slide_graphic">

			<label id="slide_graphic_label_bg" class="control-label {if $slide.type == "G"}hidden{/if}">{__("bg_slide")}</label>
			<label id="slide_graphic_label_img" class="control-label {if $slide.type != "G"}hidden{/if}">{__("image")}</label>
			<div class="controls">
				{include file="common/attach_images.tpl" image_name="slides_main" image_object_type="grslide" image_pair=$slide.main_pair image_object_id=$id no_detailed=true hide_titles=true}
			</div>
		</div>
	{* ********************************** *}
	
	{* Video ********************************** *}
	<div id="slide_video_section" class="{if  $b_type == "G" or $b_type == "T"}hidden{/if}">
		<div class="control-group" id="slide_video">
			<label class="control-label" for="elm_slide_video">{__("video_code")}:</label>
			<div class="controls">
				<textarea id="elm_slide_video" name="slide_data[video_code]" cols="35" rows="8" class="input-large">{$slide.video_code}</textarea>
			</div>
		</div>
		{if $slide.video_code}
			<div class="control-group" id="slide_video_preview">
				<label class="control-label" for="elm_slide_video_preview">{__("video_preview")}:</label>
				<div class="controls">
					{$slide.video_code nofilter}
				</div>
			</div>
		{/if}
	</div>
	{* ********************************** *}

	{* Text ********************************** *}
	<div id="slide_text_section"  class="{if $b_type != "T"}hidden{/if}">
		<div class="control-group id="slide_text_title">
			<label class="control-label" for="elm_text_title">{__("title")}:</label>
			<div class="controls">
				<input type="text" name="slide_data[title]" id="elm_text_title" value="{$slide.title}" size="25" class="input-large" />
			</div>
		</div>
		<div class="control-group id="slide_text_description">
			<label class="control-label" for="elm_text_description">{__("description")}:</label>
			<div class="controls">
				<textarea id="elm_text_description" name="slide_data[description]" cols="35" rows="3" class="input-large">{$slide.description}</textarea>
			</div>
		</div>
		
		<div class="control-group id="slide_text_hover_effects">
			<label for="elm_slide_hover_effects" class="control-label">{__("hover_effects")}</label>
			<div class="controls">
			<select name="slide_data[settings][hover_effects]" id="elm_slide_hover_effects" onchange="elm_slide_hover_effects()">
				<option {if $slide.settings.hover_effects == "effect_lily"}selected="selected"{/if} value="effect_lily">{__("effect_lily")}</option>
				<option {if $slide.settings.hover_effects == "effect_sadie"}selected="selected"{/if} value="effect_sadie">{__("effect_sadie")}</option>
				<option {if $slide.settings.hover_effects == "effect_honey"}selected="selected"{/if} value="effect_honey">{__("effect_honey")}</option>
				<option {if $slide.settings.hover_effects == "effect_layla"}selected="selected"{/if} value="effect_layla">{__("effect_layla")}</option>
				<option {if $slide.settings.hover_effects == "effect_oscar"}selected="selected"{/if} value="effect_oscar">{__("effect_oscar")}</option>
				<option {if $slide.settings.hover_effects == "effect_marley"}selected="selected"{/if} value="effect_marley">{__("effect_marley")}</option>
				<option {if $slide.settings.hover_effects == "effect_ruby"}selected="selected"{/if} value="effect_ruby">{__("effect_ruby")}</option>
				<option {if $slide.settings.hover_effects == "effect_roxy"}selected="selected"{/if} value="effect_roxy">{__("effect_roxy")}</option>
				<option {if $slide.settings.hover_effects == "effect_bubba"}selected="selected"{/if} value="effect_bubba">{__("effect_bubba")}</option>
				<option {if $slide.settings.hover_effects == "effect_romeo"}selected="selected"{/if} value="effect_romeo">{__("effect_romeo")}</option>
				<option {if $slide.settings.hover_effects == "effect_dexter"}selected="selected"{/if} value="effect_dexter">{__("effect_dexter")}</option>
				<option {if $slide.settings.hover_effects == "effect_sarah"}selected="selected"{/if} value="effect_sarah">{__("effect_sarah")}</option>
				<option {if $slide.settings.hover_effects == "effect_chico"}selected="selected"{/if} value="effect_chico">{__("effect_chico")}</option>
				<option {if $slide.settings.hover_effects == "effect_milo"}selected="selected"{/if} value="effect_milo">{__("effect_milo")}</option>
			</select>
			</div>
		</div>
		
		<div class="control-group" id="slide_text_no_hover_bg">
			<label for="elm_text_slide_bg_color" class="control-label">{__("text_slide_bg_color")}</label>
			<div class="controls">
				{include file="common/colorpicker.tpl" cp_name="slide_data[settings][slide_bg_color]" cp_id="elm_text_slide_bg_color" cp_value=$slide.settings.slide_bg_color|default:"#ffffff" cp_class="cm-te-value-changer piker-new-position"}
			</div>
		</div>
		
		<div class="control-group" id="slide_text_no_hover_text">
			<label for="elm_text_slide_text_color" class="control-label">{__("text_slide_text_color")}</label>
			<div class="controls">
				{include file="common/colorpicker.tpl" cp_name="slide_data[settings][slide_text_color]" cp_id="elm_text_slide_text_color" cp_value=$slide.settings.slide_text_color|default:"#ffffff" cp_class="cm-te-value-changer piker-new-position"}
			</div>
		</div>
		
		<div class="control-group" id="slide_text_no_hover_opacity">
			<label for="elm_text_slide_bg_image_opacity" class="control-label">{__("text_slide_bg_image_opacity")}</label>
			<div class="controls">
				<input type="text" name="slide_data[settings][slide_bg_image_opacity]" id="elm_text_slide_bg_image_opacity" value="{$slide.settings.slide_bg_image_opacity|default:"80"}" size="3" class="input_opacity-size"/>
			</div>
		</div>
		
		{* // Hover settings // *}
		
		<div class="control-group" id="slide_text_hover_bg">
			<label for="elm_text_slide_bg_color_hover" class="control-label">{__("text_slide_bg_color_hover")}</label>
			<div class="controls">
				{include file="common/colorpicker.tpl" cp_name="slide_data[settings][slide_bg_color_hover]" cp_id="elm_text_slide_bg_color_hover" cp_value=$slide.settings.slide_bg_color_hover|default:"#ffffff" cp_class="cm-te-value-changer piker-new-position"}
			</div>
		</div>
		
		<div class="control-group" id="slide_text_hover_text">
			<label for="elm_text_slide_text_color_hover" class="control-label">{__("text_slide_text_color_hover")}</label>
			<div class="controls">
				{include file="common/colorpicker.tpl" cp_name="slide_data[settings][slide_text_color_hover]" cp_id="elm_text_slide_text_color_hover" cp_value=$slide.settings.slide_text_color_hover|default:"#000000" cp_class="cm-te-value-changer piker-new-position"}
			</div>
		</div>
		
		<div class="control-group" id="slide_text_hover_opacity">
			<label for="elm_text_slide_bg_color_opacity_hover" class="control-label">{__("text_slide_bg_color_opacity_hover")}</label>
			<div class="controls">
				<input type="text" name="slide_data[settings][slide_bg_color_opacity_hover]" id="elm_text_slide_bg_color_opacity_hover" value="{$slide.settings.slide_bg_color_opacity_hover|default:"90"}" size="3" class="input_opacity-size"/>
			</div>
		</div>
		
	</div>
    {* ********************************** *}
	
	<div class="control-group {if $b_type == "T"}hidden{/if}" id="slide_target">
		<label class="control-label" for="elm_slide_target">{__("open_in_new_window")}</label>
		<div class="controls">
		<input type="hidden" name="slide_data[target]" value="T" />
		<input type="checkbox" name="slide_data[target]" id="elm_slide_target" value="B" {if $slide.target == "B"}checked="checked"{/if} />
		</div>
	</div>

	<div class="control-group {if $b_type == "V"}hidden{/if}" id="slide_url">
		<label class="control-label" for="elm_slide_url">{__("url")}:</label>
		<div class="controls">
			<input type="text" name="slide_data[url]" id="elm_slide_url" value="{$slide.url}" size="25" class="input-large" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="elm_slide_timestamp_{$id}">{__("creation_date")}</label>
		<div class="controls">
		{include file="common/calendar.tpl" date_id="elm_slide_timestamp_`$id`" date_name="slide_data[timestamp]" date_val=$slide.timestamp|default:$smarty.const.TIME start_year=$settings.Company.company_start_year}
		</div>
	</div>

	{include file="views/localizations/components/select.tpl" data_name="slide_data[localization]" data_from=$slide.localization}

	{include file="common/select_status.tpl" input_name="slide_data[status]" id="elm_slide_status" obj_id=$id obj=$slide hidden=true}
</div>
{/capture}
{include file="common/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

{capture name="buttons"}
	{if !$id}
		{include file="buttons/save_cancel.tpl" but_role="submit-link" but_target_form="slides_form" but_name="dispatch[slides.update]"}
	{else}
		{if "ULTIMATE"|fn_allowed_for && !$allow_save}
			{assign var="hide_first_button" value=true}
			{assign var="hide_second_button" value=true}
		{/if}
		{include file="buttons/save_cancel.tpl" but_name="dispatch[slides.update]" but_role="submit-link" but_target_form="slides_form" hide_first_button=$hide_first_button hide_second_button=$hide_second_button save=$id}
	{/if}
{/capture}
	
</form>

{/capture}

{if !$id}
	{assign var="title" value=__("slides.new_slide")}
{else}
	{assign var="title" value="{__("slides.editing_slide")}: `$slide.slide`"}
{/if}
{include file="common/mainbox.tpl" title=$title content=$smarty.capture.mainbox buttons=$smarty.capture.buttons select_languages=true}

{** slide section **}
