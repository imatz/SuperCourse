<div class="sidebar-field" id="elm_phone_field">
    <label for="elm_phone">{__("phone")}</label>
    <div class="break">
        <input type="text" name="phone" id="elm_phone" value="{$search.phone}" />
    </div>
</div>
<script type="text/javascript">
//<![CDATA[
(function(_, $) {
    $(document).ready(function(){
		$('#simple_search').append($('#elm_phone_field'));
    });
}(Tygh, Tygh.$));
//]]>
</script>
