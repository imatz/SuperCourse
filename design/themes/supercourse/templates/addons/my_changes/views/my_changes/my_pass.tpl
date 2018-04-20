{*Liampas Stathis-Prothiki autofocus sto pedio password kai visible-invisible to password*}
<div style="margin-left: 20px;">

<h3>Σύνδεση</h3>

<form name="custom_pass_form" action="{""|fn_url}" method="post">
    <div class="ty-control-group ty-password-forgot" style="width: 30%;">
		<table>
			<tr>
				<label for="psw_{$id}" class="ty-login__filed-label ty-control-group__label ty-password-forgot__label cm-required">{__("password")}</label>
			</tr>
			<tr>
				<br><br>
				<td style=" vertical-align:top;"><input type="password" id="psw_{$id}" name="password" size="30" value="{$config.demo_password}" class="ty-login__input" maxlength="32" autofocus/></td>
				<td style=" vertical-align:top;">
					<button type="button" id="eye" style="height: 32px;"><img src="images/pass_visible.png" alt="eye" /></button>
					<button type="button" id="eye2" style="height: 32px;"><img src="images/pass_invisible.png" alt="eye" /></button>
				</td>
			</tr>
		</table>
		<br>
        <a href="{"auth.recover_password"|fn_url}" class="ty-float-left"  tabindex="5">{__("forgot_password_question")}</a>
    </div>


{*To parakatw script dimiourgithike gia thn prosthiki koympiwn gia emfanisi toy kwdikou*}
	<script>
	document.getElementById("eye2").style.display='none';
	function show() {
		var p = document.getElementById('psw_{$id}');
		p.setAttribute('type', 'text');
	}

	function hide() {
		var p = document.getElementById('psw_{$id}');
		p.setAttribute('type', 'password');
	}

	var pwShown = 0;
	document.getElementById("eye").addEventListener("click", function () {
			pwShown = 1;
			show();
			document.getElementById("eye").style.display='none';
			document.getElementById("eye2").style.display='block';
			document.getElementById("psw_{$id}").focus();
	}, false);

	document.getElementById("eye2").addEventListener("click", function () {
			pwShown = 0;
			hide();
			document.getElementById("eye2").style.display='none';
			document.getElementById("eye").style.display='block';
			document.getElementById("psw_{$id}").focus();
	}, false);
	</script>
	
    {hook name="index:login_buttons"}
        <div class="buttons-container clearfix">
            <div class="ty-float-left">
                {include file="buttons/login.tpl" but_name="dispatch[my_changes.login3]" but_role="submit"}
                {*{if $membership_id} == 'x'}<input type="hidden" name="redirect_url" value="index.php?target=topics&topic_id=12" />*}
            </div>
        </div>
    {/hook}

</form>
</div>