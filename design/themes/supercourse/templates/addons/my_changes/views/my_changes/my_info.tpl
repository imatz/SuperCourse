<div style="margin-left: 20px;">
<h3>Σύνδεση</h>
<form name="custom_login_form" action="{""|fn_url}" method="post">
	<div style="width: 60%;">
    	<div class="ty-control-group" style="float: left;" "width: 30%;">
        	<label for="login_{$id}" class="ty-login__filed-label ty-control-group__label cm-required cm-trim cm-email">{__("email")}</label>
        	<input type="text" "hidden" id="login_{$id}" name="user_login" size="30" value="{$config.demo_username}" class="ty-login__input cm-focus"/>
    	</div>
        <div style="float: right;" "width: 30%;">
        	<h4>Δεν είσαστε Εγγεγραμένο Μέλος;</h4>
            <a href="http://supercourse-eshop.gr/profiles-add/">Ενεργοποίηση / Δημιουργία Λογαριασμού</a>
        </div>
	</div>
    <div style="clear:both"></div>
    {hook name="index:login_buttons"}
        <div class="buttons-container clearfix">
            <div class="ty-float-left" style="float: left;" >
                {include file="buttons/continue.tpl" but_name="dispatch[my_changes.login2]" but_role="submit"}
            </div>
        </div>
    	{/hook}
</form>
</div>