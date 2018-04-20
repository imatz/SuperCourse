<div style="margin-left: 20px;">
<h3>Κωδικός</h3>
<form name="custom_pass_form" action="{""|fn_url}" method="post">
    <div class="ty-control-group ty-password-forgot" style="width: 30%;">
            <label for="psw_{$id}" class="ty-login__filed-label ty-control-group__label ty-password-forgot__label cm-required">{__("password")}</label>
            <input type="password" id="psw_{$id}" name="password" size="30" value="{$config.demo_password}" class="ty-login__input" maxlength="32"/>
            <br><br>
            <a href="{"auth.recover_password"|fn_url}" class="ty-float-left"  tabindex="5">{__("forgot_password_question")}</a>
        </div>
    {hook name="index:login_buttons"}
        <div class="buttons-container clearfix">
            <div class="ty-float-left">
                {include file="buttons/login.tpl" but_name="dispatch[my_changes.prof_choice]" but_role="submit"}
            </div>
        </div>
    {/hook}
</form>
</div>