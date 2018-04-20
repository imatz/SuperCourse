function fn_set_package_option_value(id, option_id, value, index)
{
    var $ = Tygh.$;

    var elm = $('#option_' + id + '_' + option_id + '_' + index);
    if (elm.prop('disabled')) {
        return false;
    }
    if (elm.prop('type') == 'select-one') {
        elm.val(value).change();
    } else {
        elms = $('#option_' + id + '_' + option_id + '_group' + '_' + index);
        if ($.browser.msie) {
            $('input[type=radio][value=' + value + ']', elms).prop('checked', true);
        }
        $('input[type=radio][value=' + value + ']', elms).click();
    }

    return true;
}