var PaceBuilder = (function ($) {

    var $window = $(window),
        $container = $('#pt-pb-content'),
        fullWidth = false;

    var $carousels = [];
    // tiny helper function to add breakpoints
    function getGridSize(cols) {
        cols = cols || 4;
        cols = getColsNum(cols);
        if (cols < 2)
            return cols;
        return (window.innerWidth < 600) ? 2 :
            (window.innerWidth < 900) ? 3 : cols;
    }

    function getColsNum(cols) {
        return isNaN(cols) ? ['one', 'two', 'three', 'four', 'five', 'six'].indexOf(cols) + 1 : cols;
    }

    return {

        fullWidth: function () {
            if ($('body').hasClass('pt-pb-fullwidth-page')) {
                if (!$container.parent().hasClass('pt-pb-full-width-wrap')) {
                    fullWidth = true;
                    $container.wrap('<div class="pt-pb-full-width-wrap"></div>');
                    this.widthManager();
                }
            }
        },

        widthManager: function () {
            if(fullWidth){
                $container.css({
                    'width': $window.width() + 'px',
                    left: 0 - Math.ceil($container.parent().offset().left) + 'px'
                });
                $container.parent().css('height', $container.outerHeight() + 'px');
            }
        },

        parallax: function(){
            $('.pt-pb-section.parallax-bg').each(function(){
                $(this).parallax( '50%', 0.2, true );
            });
        },

        slickNav: function () {
            $('.ptpb-mobile-menu').slicknav({
                prependTo: '.pt-pb-module-menu-mob',
                parentTag: 'liner',
                allowParentLinks: true,
                duplicate: true,
                label: '',
                closedSymbol: '<i class="fa fa-angle-right"></i>',
                openedSymbol: '<i class="fa fa-angle-down"></i>',
            });
        },

        colorbox: function() {
            $('.pt-pb-module-wrap a.gallery').colorbox({
                rel: function() {
                    return $(this).data('gallery');
                },
                maxWidth: '95%',
                maxHeight: '90%',
                retinaImage: true
            });
        },

        //init image hover effects
        imageEffects: function() {
            $('section.pt-pb-section .pt-pb-module-wrap .effect').mouseenter(function() {
                    $(this).addClass('hover');
                })
                .mouseleave(function() {
                    $(this).removeClass('hover');
                });
        },

        googleMaps: function () {
            if(typeof google === 'undefined'){
                setTimeout(PaceBuilder.googleMaps, 200);
                return;
            }
            $('.ptpb-google-map').each(function () {
                var $t = $(this),
                    points = $.parseJSON( $t.data('points') ? $.base64.decode( $t.data('points') ) : $t.siblings('script').html() ),
                    zoom = parseInt($t.data('zoom')),
                    center = $t.data('center') ? $t.data('center').split(',') : [],
                    centerLatLng = new google.maps.LatLng(0,0),
                    options = {
                        mapOptions: {
                            zoom: isNaN(zoom) ? 12 : zoom,
                            scrollwheel: false
                        }
                    };

                if (center.length === 2) {
                    centerLatLng = new google.maps.LatLng(center[0], center[1]);
                } else if (points.length > 0) {
                    centerLatLng = new google.maps.LatLng(parseFloat(points[0].lat), parseFloat(points[0].lng));
                }

                options.mapOptions.center = centerLatLng;

                if (!jQuery.isArray(points)) {
                    points = jQuery.map(points, function(val) {
                        val.autoShow = true;
                        return val;
                    });
                }

                options.showOnLoad = points;

                if ($t.data('theme') !== 'none' && GoogleMapThemes && GoogleMapThemes[$t.data('theme')]){
                    options.styles = GoogleMapThemes[$t.data('theme')];
                }

                $t.mapsed(options);

            });
        },

        flexSlider: function() {
            $('.pt-pb-module-wrap .flexslider:not(.post-carousel)').each(function() {
                var $slider = $(this),
                    options = $slider.data();

                options.animation = options.effect || 'slide';

                options.start = function() {
                    if (typeof options.navColor === 'undefined') {
                        options.navColor = '#5C5F6A';
                    }
                    $slider.find('.flex-control-nav a').css({ 'background-color': options.navColor, 'border-color': options.navColor });
                    $slider.find('.flex-direction-nav a').css('color', options.navColor);
                };

                $slider.flexslider(options);
            });
        },

        carousel: function() {
            $('.flexslider.post-carousel').each(function() {
                var $t = $(this),
                    cols = getColsNum($t.data('columns')),
                    margin = $t.data('spacing'),
                    gridSize = getGridSize(cols),
                    width = $t.width() / cols,
                    options = $t.data();

                $carousels.push($t.flexslider($.extend({
                    useCSS: false,
                    animation: 'slide',
                    itemWidth: width,
                    itemMargin: margin ? 15 : 0,
                    animationLoop: true,
                    minItems: gridSize,
                    maxItems: gridSize,
                    start: function() {
                        $t.data('flexslider').update();
                    }
                }, options)));
            });
        },

        resizeCarousel: function() {
            $.each($carousels, function(i, el) {

                var gridSize = getGridSize(el.data('columns'));

                el.data('flexslider').vars.minItems = gridSize;
                el.data('flexslider').vars.maxItems = gridSize;
                el.data('flexslider').vars.move = gridSize;
            });
        },

        icons: function() {
            $('[class^="ti-"], .fa, .dashicons').each(function(){
                $(this).empty();
            });
        },

        masonry: function() {
            var $container = $('.masonry-grid-container'); 
            if ($container.length > 0) { 
                $container.imagesLoaded(function() {
                    $container.masonry({
                        columnWidth: '.masonry-item',
                        itemSelector: '.masonry-item',
                        percentPosition: true
                    });
                });
            }
            $container = $('.ptpb-gallery');
            if ($container.length > 0) {
                $container.imagesLoaded(function() {
                    $container.masonry({
                        itemSelector: '.ptpb-gallery-thumb-wrap'
                    });
                });
            }
        },

        events: function () {
            $window.load(this.flexSlider);
            $window.load(this.carousel);
            $window.load(this.parallax);
            $window.resize(this.widthManager);
            $window.resize(this.resizeCarousel);
            new WOW({
                offset: 150
            }).init();
        }

    };

})(jQuery);

jQuery(document).ready(function() {
    PaceBuilder.slickNav();
    PaceBuilder.fullWidth();
    PaceBuilder.colorbox();
    PaceBuilder.imageEffects();
    PaceBuilder.googleMaps();
    PaceBuilder.icons();
    PaceBuilder.masonry();
    PaceBuilder.events();
});
