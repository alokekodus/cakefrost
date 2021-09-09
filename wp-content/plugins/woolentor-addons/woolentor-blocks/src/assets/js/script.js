( function( $, window ) {
    'use strict';

    function woolentor_tabs( $tabmenus, $tabpane ){
        $tabmenus.on('click', 'a', function(e){
            e.preventDefault();
            var $this = $(this),
                $target = $this.attr('href');
            $this.addClass('htactive').parent().siblings().children('a').removeClass('htactive');
            $( $tabpane + $target ).addClass('htactive').siblings().removeClass('htactive');

            // slick refresh
            if( $('.slick-slider').length > 0 ){
                var $id = $this.attr('href');
                $( $id ).find('.slick-slider').slick('refresh');
            }

        });
    }
    woolentor_tabs( $(".ht-tab-menus"),'.ht-tab-pane' );

    var initializeProductSlider = function( $block ) {

        var settings = $($block).data('settings');
        var arrows = settings['arrows'];
        var dots = settings['dots'];
        var autoplay = settings['autoplay'];
        var rtl = settings['rtl'];
        var autoplay_speed = parseInt(settings['autoplay_speed']) || 3000;
        var animation_speed = parseInt(settings['animation_speed']) || 300;
        var fade = settings['fade'];
        var pause_on_hover = settings['pause_on_hover'];
        var display_columns = parseInt(settings['product_items']) || 4;
        var scroll_columns = parseInt(settings['scroll_columns']) || 4;
        var tablet_width = parseInt(settings['tablet_width']) || 800;
        var tablet_display_columns = parseInt(settings['tablet_display_columns']) || 2;
        var tablet_scroll_columns = parseInt(settings['tablet_scroll_columns']) || 2;
        var mobile_width = parseInt(settings['mobile_width']) || 480;
        var mobile_display_columns = parseInt(settings['mobile_display_columns']) || 1;
        var mobile_scroll_columns = parseInt(settings['mobile_scroll_columns']) || 1;

        $($block).slick({
            arrows: arrows,
            prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-angle-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fa fa-angle-right"></i></button>',
            dots: dots,
            infinite: true,
            autoplay: autoplay,
            autoplaySpeed: autoplay_speed,
            speed: animation_speed,
            fade: false,
            pauseOnHover: pause_on_hover,
            slidesToShow: display_columns,
            slidesToScroll: scroll_columns,
            rtl: rtl,
            responsive: [
                {
                    breakpoint: tablet_width,
                    settings: {
                        slidesToShow: tablet_display_columns,
                        slidesToScroll: tablet_scroll_columns
                    }
                },
                {
                    breakpoint: mobile_width,
                    settings: {
                        slidesToShow: mobile_display_columns,
                        slidesToScroll: mobile_scroll_columns
                    }
                }
            ]
        });

    }

    $(document).ready(function(){
        $('.product-slider').each(function(){
            initializeProductSlider( $(this) );
        });
    });


} )( jQuery, window );
