<script>
var _scroll = null;
var _position = new Array();
_position[1] = ';left:0px; top:0px';
_position[2] = ';right:0px; top:0px';
_position[3] = ';right:0px; bottom:0px';
_position[4] = ';left:0px; bottom:0px';

$(function(){$ldelim}
	$('body').append('<img src="images/hw_top.png" id="scroll_to_top" alt="" style="margin:{$addons.hw_top.margin};cursor:pointer; position:fixed; '+_position[{$addons.hw_top.position}]+'">');
	_scroll = $('#scroll_to_top');
	_scroll.fadeOut().click(function(event){$ldelim}
		event.preventDefault();
		$("html, body").animate({ scrollTop: 0 }, "slow", function(){$ldelim} _scroll.fadeOut(); {$rdelim});
	{$rdelim});

	$(window).scroll(function() {$ldelim}
        		if ($(window).scrollTop() > 45){$ldelim}
        			_scroll.fadeIn();
        		{$rdelim}else {$ldelim}
           			_scroll.fadeOut();
        		{$rdelim};
    	{$rdelim});
{$rdelim});
</script>