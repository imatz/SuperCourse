{if $runtime.controller == 'addons' && $runtime.mode == 'manage'}
<script type="text/javascript">
(function(_, $) {
    $(document).ready(function(){
		if (typeof(ecl_applied) == 'undefined') {
        $('[id^="addon_ecl_"] .exicon-box-blue').css('background-image', 'url(https://ecom-labs.com/images/ecl_logo.png)').css('background-position', '0px').css('width', '31px').css('height', '33px').css('margin-top', '0px');
		var ecl_applied = true;
		}
	});
}(Tygh, Tygh.$));
</script>
{/if}