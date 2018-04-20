{styles use_scheme=true reflect_less=$reflect_less}
{hook name="index:styles"}

	{style src="addons/cp_graceful_theme/true-styles.less"}
	{style src="addons/cp_graceful_theme/tygh/responsive.less"}

	{if $rtl == "Y"}
		{style src="addons/cp_graceful_theme/tygh/rtl.less"}
	{/if}
	
	{* Translation mode *}
	{if $runtime.customization_mode.live_editor || $runtime.customization_mode.design}
		{style src="tygh/design_mode.less"}
	{/if}

	{* Theme editor mode *}
	{if $runtime.customization_mode.theme_editor}
		{style src="tygh/theme_editor.less"}
	{/if}
{/hook}
{/styles}
