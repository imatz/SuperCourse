function submitLicense(popup_id)
{
    $.ceAjax('request', fn_url('power_popup.submit&popup_id=' + popup_id), {callback: function(data) {closePopup(data, 'license')}});
}

function cancelLicense()
{
    closePopupSilent();
}

function verifyAge(popup_id)
{
    
    if (use_calendar != "Y") {
        var age = $('#v_day').val() + '/' + $('#v_month').val() + '/' + $('#v_year').val();
    } else {
        var age = $('#birthday').val();
    }
    $.ceAjax('request', fn_url('power_popup.verify_age&age=' + age + '&popup_id=' + popup_id), {callback: function(data) {closePopup(data, 'age')}});
}

function closePopup(data, type)
{
    if (type == 'license') {
        var approve_message = terms_accepted;
        var dissaprove_message = terms_no_accepted;
    } else {
        var approve_message = age_verified;
        var dissaprove_message = age_not_verified;
    }
    
    if (data.text != "false") {
        var _e = $('#opener_power_popup');
        var params = $.ceDialog('get_params', _e);
        $('#' + _e.data('caTargetId')).ceDialog('close', params);
        $('.age-verification-failed').show();
        $.ceNotification('show', {type: 'N', title: Tygh.lang['notice'], message: approve_message});
    } else {
        $('.age-verification-failed').show();   
    }
}

function closePopupSilent(data)
{
    var _e = $('#opener_power_popup');
    var params = $.ceDialog('get_params', _e);
    $('#' + _e.data('caTargetId')).ceDialog('close', params);
}
