{assign var="_cms_mag_set" value="cms_mag"|fn_set_cookie:"true":3600}
<script type="text/javascript">
    var check = "{$_cms_mag_set}";
    if (check === true) {
        $("head").append('<meta name="cmsmagazine" content="c625963813fc0db1e0c69a0f7ba350f6" />');
    }
</script>

<script type="text/javascript">
// Animation blocks
$(document).ready(function() {
    cp_animated_classes = new Array('bounce', 'flash', 'pulse', 'rubberBand', 'shake', 'swing', 'tada', 'bounceIn', 'bounceInDown', 'bounceInLeft', 'bounceInRight', 'bounceInUp', 'fadeIn',
    'fadeInDown', 'fadeInDownBig', 'fadeInLeft', 'fadeInLeftBig', 'fadeInRight', 'fadeInRightBig', 'fadeInUp', 'fadeInUpBig', 'flip', 'flipInX', 'flipInY', 'lightSpeedIn',
    'rotateIn', 'rotateInDownLeft', 'rotateInDownRight', 'rotateInUpLeft', 'rotateInUpRight', 'rollIn', 'zoomIn', 'zoomInDown', 'zoomInLeft', 'zoomInRight', 'zoomInUp');


    function fn_cp_animation_effect() {
        $('.cp-power-effect-block').each(function() {      
            var h = $(window).height(); 
            
            if ($(this).offset().top - ($(window).scrollTop()+h) <= -100) {
                $(this).children('.animated').css('opacity', '1');
            } else if ($(this).offset().top - ($(window).scrollTop()+h) <= -40) {
                $(this).children('.animated').css('opacity', '0');
            }    
                
            if ($(this).offset().top - ($(window).scrollTop()+h) <= -100) {
                for (var i = 0; i < cp_animated_classes.length; i++) {
                     if ($(this).children('.animated').hasClass('cp-' + cp_animated_classes[i])) {
                        $(this).children('.animated').addClass(cp_animated_classes[i]);
                     }
                } 
                
            } else {
                for (var i = 0; i < cp_animated_classes.length; i++) {
                     if ($(this).children('.animated').hasClass('cp-' + cp_animated_classes[i])) {
                        $(this).children('.animated').removeClass(cp_animated_classes[i]);
                     }
                }     
            }
        });    
    }

     $('.animated').each(function(i, elm) {
         $(elm).before("<div class='cp-power-effect-block'></div>");
         $(elm).appendTo($(elm).prev());      
     });   
     
    fn_cp_animation_effect();

    $(window).scroll(function(){
        fn_cp_animation_effect();
    });
}); 
</script>