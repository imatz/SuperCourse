{$fmail = $smarty.session.fmail}
{assign var="rec_mail" value="$fmail"}

<div class="ty-recover-password">
	<form name="recoverfrm" action="{""|fn_url}" method="post">
	    <div class="ty-control-group">
	        <label class="ty-login__filed-label ty-control-group__label cm-trim cm-required" for="login_id">{__("email")}</label>
	        <input type="text" id="login_id" name="user_email" size="30" value="{$rec_mail}" class="ty-login__input cm-focus" readonly/>
	    </div>
	    <div class="buttons-container login-recovery">
	        {include file="buttons/reset_password.tpl" but_name="dispatch[auth.recover_password]"}
	    </div>
	</form>
</div>
{capture name="mainbox_title"}{__("recover_password")}{/capture}