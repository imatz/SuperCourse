{assign var ="s_delivery_notes_id" value="s_delivery_notes"|fn_my_users_get_setting_field}
{if "checkout"==$runtime.mode && "checkout"==$runtime.controller && $auth.user_id && $field.required == "Y"}
	{assign var ="tooltip_title" value=__('contact_shop_to_change') }
	{*assign var ="readonly" value="disabled=\"disabled\" title=\"$tooltip_title\"" }
	{assign var ="tip" value=" cm-tooltip"*}
	{assign var ="readonly" value="disabled=\"disabled\"" }
	{assign var ="tip" value=""}
{else}
	{assign var ="readonly" value=""}
	{assign var ="tip" value=""}
{/if}
<div class="ty-control-group ty-profile-field__item ty-{$field.class}">
    {if $pref_field_name != $field.description || $field.required == "Y"}
        <label for="{$id_prefix}elm_{$field.field_id}" class="ty-control-group__title cm-profile-field {if $field.required == "Y"}cm-required{/if}{if $field.field_type == "P"} cm-phone{/if}{if $field.field_type == "Z"} cm-zipcode{/if}{if $field.field_type == "E"} cm-email{/if} {if $field.field_type == "Z"}{if $section == "S"}cm-location-shipping{else}cm-location-billing{/if}{/if}">{$field.description}</label>
    {/if}

    {if $field.field_type == "A"}  {* State selectbox *}
        {$_country = $settings.General.default_country}
        {if ! $auth.user_id}
		{$_state = $value|default:$settings.General.default_state}
			<select {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} id="{$id_prefix}elm_{$field.field_id}" class="ty-profile-field__select-state cm-state {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if} {if !$skip_field}{$_class}{/if}" name="{$data_name}[{$data_id}]" {if !$skip_field}{$disabled_param nofilter}{/if}>
				<option value="">- {__("select_state")} -</option>
				{if $states && $states.$_country}
					{foreach from=$states.$_country item=state}
						<option {if $_state == $state.code}selected="selected"{/if} value="{$state.code}">{$state.state}</option>
					{/foreach}
				{/if}
			</select>
            <input {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} type="text" id="elm_{$field.field_id}_d" name="{$data_name}[{$data_id}]" size="32" maxlength="64" value="{$_state}" disabled="disabled" class="cm-state {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if} ty-input-text hidden {if $_class}disabled{/if}"/>
		{else}
			{assign var ="nomos" value="{__(contact_shop)}"}
			{$_state = $value}
			{if !empty($_state) && $states && $states.$_country}
				{foreach from=$states.$_country item=state}
					{if $_state == $state.code}{assign var ="nomos" value=$state.state}{/if}
				{/foreach}
			{/if}
			 <input {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} type="text" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" size="32" value="{$nomos}" class="ty-input-text {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}{$tip}" {if !$skip_field}{$disabled_param nofilter}{/if} {$readonly nofilter}/>
		{/if}
    {elseif $field.field_type == "O"}  {* Countries selectbox *}
        {assign var="_country" value=$value|default:$settings.General.default_country}
        <select {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} id="{$id_prefix}elm_{$field.field_id}" class="ty-profile-field__select-country cm-country {if $section == "S"}cm-location-shipping{else}cm-location-billing{/if} {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}" name="{$data_name}[{$data_id}]" {if !$skip_field}{$disabled_param nofilter}{/if}>
            {hook name="profiles:country_selectbox_items"}
            <option value="GR">{$countries.GR}</option>
           {* <option value="">- {__("select_country")} -</option>
            {foreach from=$countries item="country" key="code"}
            <option {if $_country == $code}selected="selected"{/if} value="{$code}">{$country}</option>
            {/foreach}
           *} {/hook}
        </select>
    
    {elseif $field.field_type == "C"}  {* Checkbox *}
        <input type="hidden" name="{$data_name}[{$data_id}]" value="N" {if !$skip_field}{$disabled_param nofilter}{/if} />
        <input type="checkbox" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" value="Y" {if $value == "Y"}checked="checked"{/if} class="checkbox {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}" {if !$skip_field}{$disabled_param nofilter}{/if} />

    {elseif $field.field_type == "T"}  {* Textarea *}
		{if $s_delivery_notes_id==$field.field_id}
		{literal}
		<script type="text/javascript">
			$(document).ready(function(){
				$("#{/literal}{$id_prefix}delivery_notes_selector_{$field.field_id}{literal}").change(function(){
					var hide = true;
					var selected_value = $(this).val();
					if("-" == selected_value) {
						hide = false;
						selected_value='';
					} 
					$("#{/literal}{$id_prefix}elm_{$field.field_id}{literal}").val(selected_value).toggleClass('hidden',hide);
				});
			});
		</script>
		{/literal}
		{assign var ="found" value=false}
		<select id="{$id_prefix}delivery_notes_selector_{$field.field_id}" class="ty-profile-field__select" >
            <option value="">--</option>            
            <option {if $value == 'ΑΠΟΓΕΥΜΑ'}selected="selected"{assign var ="found" value=true}{/if} value="ΑΠΟΓΕΥΜΑ">ΑΠΟΓΕΥΜΑ</option>
            <option {if $value == 'ΜΕΣΗΜΕΡΙ'}selected="selected"{assign var ="found" value=true}{/if} value="ΜΕΣΗΜΕΡΙ">ΜΕΣΗΜΕΡΙ</option>
            <option {if $value == 'ΠΡΩΙ'}selected="selected"{assign var ="found" value=true}{/if} value="ΠΡΩΙ">ΠΡΩΙ</option>
            <option {if $value == 'ΠΑΡΑΛΑΒΗ ΑΠΟ ACS'}selected="selected"{assign var ="found" value=true}{/if} value="ΠΑΡΑΛΑΒΗ ΑΠΟ ACS">ΠΑΡΑΛΑΒΗ ΑΠΟ ACS</option>
            <option {if $value == 'Ο COURIER ΘΑ ΠΑΡΕΙ ΔΕΜΑ'}selected="selected"{assign var ="found" value=true}{/if} value="Ο COURIER ΘΑ ΠΑΡΕΙ ΔΕΜΑ">Ο COURIER ΘΑ ΠΑΡΕΙ ΔΕΜΑ</option>
            <option {if $value == 'ΠΕΡΙΕΧΕΙ ΣΥΝΑΛΛΑΓΜΑΤΙΚΗ'}selected="selected"{assign var ="found" value=true}{/if} value="ΠΕΡΙΕΧΕΙ ΣΥΝΑΛΛΑΓΜΑΤΙΚΗ">ΠΕΡΙΕΧΕΙ ΣΥΝΑΛΛΑΓΜΑΤΙΚΗ</option>
            <option {if $value == 'ΝΑ ΠΡΟΗΓΗΘΕΙ ΤΗΛΕΦΩΝΟ'}selected="selected"{assign var ="found" value=true}{/if} value="ΝΑ ΠΡΟΗΓΗΘΕΙ ΤΗΛΕΦΩΝΟ">ΝΑ ΠΡΟΗΓΗΘΕΙ ΤΗΛΕΦΩΝΟ</option>
            <option {if !empty($value) && ! $found}selected="selected"{/if} value="-">ΑΛΛΟ</option>
        </select>
		</div>
		<div class="ty-control-group ty-profile-field__item ty-{$field.class}">
		{/if}
        <textarea {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} class="ty-input-textarea {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}{if $s_delivery_notes_id==$field.field_id && (empty($value) || $found)} hidden{/if}" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" cols="32" rows="3" {if !$skip_field}{$disabled_param nofilter}{/if}>{$value}</textarea>
    
    {elseif $field.field_type == "D"}  {* Date *}
        {if !$skip_field}
            {include file="common/calendar.tpl" date_id="`$id_prefix`elm_`$field.field_id`" date_name="`$data_name`[`$data_id`]" date_val=$value start_year="1902" end_year="0" extra=$disabled_param}
        {else}
            {include file="common/calendar.tpl" date_id="`$id_prefix`elm_`$field.field_id`" date_name="`$data_name`[`$data_id`]" date_val=$value start_year="1902" end_year="0"}
        {/if}

    {elseif $field.field_type == "S"}  {* Selectbox *}
        <select {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} id="{$id_prefix}elm_{$field.field_id}" class="ty-profile-field__select {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}" name="{$data_name}[{$data_id}]" {if !$skip_field}{$disabled_param nofilter}{/if}>
            {if $field.required != "Y"}
            <option value="">--</option>
            {/if}
            {foreach from=$field.values key=k item=v}
            <option {if $value == $k}selected="selected"{/if} value="{$k}">{$v}</option>
            {/foreach}
        </select>
    
    {elseif $field.field_type == "R"}  {* Radiogroup *}
        <div id="{$id_prefix}elm_{$field.field_id}">
            {foreach from=$field.values key=k item=v name="rfe"}
            <input class="radio {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if} {$id_prefix}elm_{$field.field_id}" type="radio" id="{$id_prefix}elm_{$field.field_id}_{$k}" name="{$data_name}[{$data_id}]" value="{$k}" {if (!$value && $smarty.foreach.rfe.first) || $value == $k}checked="checked"{/if} {if !$skip_field}{$disabled_param nofilter}{/if} /><span class="radio">{$v}</span>
            {/foreach}
        </div>

    {elseif $field.field_type == "N"}  {* Address type *}
        <input class="radio {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if} {$id_prefix}elm_{$field.field_id}" type="radio" id="{$id_prefix}elm_{$field.field_id}_residential" name="{$data_name}[{$data_id}]" value="residential" {if !$value || $value == "residential"}checked="checked"{/if} {if !$skip_field}{$disabled_param nofilter}{/if} /><span class="radio">{__("address_residential")}</span>
        <input class="radio {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if} {$id_prefix}elm_{$field.field_id}" type="radio" id="{$id_prefix}elm_{$field.field_id}_commercial" name="{$data_name}[{$data_id}]" value="commercial" {if $value == "commercial"}checked="checked"{/if} {if !$skip_field}{$disabled_param nofilter}{/if} /><span class="radio">{__("address_commercial")}</span>
		
	{elseif $field.field_type == "P" && ( "s_phones" == $data_id || "b_phones" == $data_id || "phones" == $data_id )}  {* an exei array me thlefvna kane iteration *}
		{foreach from=$value key=phone_id item=phone}
			<input {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} type="text" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}][{$phone_id}]" size="32" value="{$phone}" class="ty-input-text {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}{$tip}" {if !$skip_field}{$disabled_param nofilter}{/if}  {$readonly nofilter}/>
			
		{foreachelse}
			<input {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} type="text" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}][]" size="32" value="" class="ty-input-text {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}{$tip}" {if !$skip_field}{$disabled_param nofilter}{/if}  {$readonly nofilter}/>
		{/foreach}	
			
    {else}  {* Simple input *}
        <input {if $field.autocomplete_type}x-autocompletetype="{$field.autocomplete_type}"{/if} type="text" id="{$id_prefix}elm_{$field.field_id}" name="{$data_name}[{$data_id}]" size="32" value="{$value}" class="ty-input-text {if !$skip_field}{$_class}{else}cm-skip-avail-switch{/if}{$tip}" {if !$skip_field}{$disabled_param nofilter}{/if} {$readonly nofilter}/>
		
    {/if}

    {assign var="pref_field_name" value=$field.description}
</div>
