{if !$auth.user_id && $addons.ecl_login_redirect.disable_registration == 'Y'}
<style type="text/css">
.modal {
  position: fixed;
  top: 25%;
  left: 50%;
  z-index: 1050;
  width: 300px !important;
  height: auto !important;
  margin-left: -150px;
  background-color: #fff;
  border: 1px solid #999;
  border: 1px solid rgba(0,0,0,0.3);
  *border: 1px solid #999;
  -webkit-border-radius: 6px;
  -moz-border-radius: 6px;
  border-radius: 6px;
  -webkit-box-shadow: 0 3px 7px rgba(0,0,0,0.3);
  -moz-box-shadow: 0 3px 7px rgba(0,0,0,0.3);
  box-shadow: 0 3px 7px rgba(0,0,0,0.3);
  -webkit-background-clip: padding-box;
  -moz-background-clip: padding-box;
  background-clip: padding-box;
  outline: none;
}
.modal .ty-control-group {
    padding: 0px 15px;
}
.modal .buttons-container {
    border-radius: 0 0 6px 6px;
    -webkit-box-shadow: inset 0 1px 0 #fff;
    -moz-box-shadow: inset 0 1px 0 #fff;
    box-shadow: inset 0 1px 0 #fff;
}
.modal .login-redirect-text {
    padding: 15px;
}
.modal .ty-login, .modal .ty-recover-password {
    padding: 0px;
    margin: 0px;
}
body {
  background: url('design/backend/media/images/main_bg.png') #ccc;
}
</style>
<div class="modal">
{if $runtime.mode != 'recover_password'}
    <p class="login-redirect-text">{__('login_redirect_text')}</p>
    {include file="views/auth/login_form.tpl"}
{else}
    <p class="login-redirect-text">{__('text_recover_password_notice')}</p>
    {include file="views/auth/recover_password.tpl"}
{/if}
</div>
{/if}