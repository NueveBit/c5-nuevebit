/**
 * Galleria Classic Theme 2012-08-08
 * http://galleria.io
 *
 * Licensed under the MIT license
 * https://raw.github.com/aino/galleria/master/LICENSE
 *
 */

(function($) {

/*global jQuery, Galleria */

Galleria.addTheme({
    name: 'slideshow',
    author: 'NueveBit',
    css: 'galleria.slideshow.css',
    defaults: {
        transition: 'slide',
        thumbCrop:  'height',

        // set this to false if you want to show the caption all the time:
        _toggleInfo: true
    },
    init: function(options) {

        $(".galleria-thumbnails-container").remove();
        $(".galleria-container").css("height", "-=60px");
//        $(".galleria-stage").css("bottom", "10px"); 

        Galleria.requires(1.28, 'This version of Classic theme requires Galleria 1.2.8 or later');

        // cache some stuff
        var touch = Galleria.TOUCH;

        // show loader & counter with opacity
        this.$('loader').show().css('opacity', 0.4);

        // some stuff for non-touch browsers
        if (! touch ) {
            this.addIdleState( this.get('image-nav-left'), { left:-50 });
            this.addIdleState( this.get('image-nav-right'), { right:-50 });
            this.addIdleState( this.get('counter'), { opacity:0 });
        }

        this.bind('loadstart', function(e) {
            if (!e.cached) {
                this.$('loader').show().fadeTo(200, 0.4);
            }

//            this.$('info').toggle( this.hasInfo() );

//            $(e.thumbTarget).css('opacity',1).parent().siblings().children().css('opacity', 0.6);
        });

        this.bind('loadfinish', function(e) {
            this.$('loader').fadeOut(200);
        });
    }
});

}(jQuery));
