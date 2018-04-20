<script>
  var recaptcha;
  var myCallBack = function() {

    $('.g_recaptcha').each(function(i, elm) {
      recaptcha = grecaptcha.render($(elm).attr('id'), {
	'sitekey' : '{$addons.google_recaptcha.key}',
	'theme' : '{$addons.google_recaptcha.theme}',
	'type': '{$addons.google_recaptcha.type}'
      });  
    });    
  };
</script>

<script src="https://www.google.com/recaptcha/api.js?onload=myCallBack&render=explicit&hl={$smarty.const.CART_LANGUAGE}" async defer></script>