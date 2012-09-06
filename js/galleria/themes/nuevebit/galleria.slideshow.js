/**
 * Galleria Nuevebit Simple Theme 
 * http://www.nuevebit.com
 * 
 * This is a modified version of the Galleria Classic theme.
 *
 * Licensed under the Artistic License
 *
 */

(function($) {

Galleria.addTheme({
    name: 'slideshow',
    author: 'Nuevebit',
    defaults: {
        transition: 'slide',
        thumbCrop:  'height',
        carousel: false,
        autoplay: true
    },
    init: function(options) {

        Galleria.requires(1.28, 'This version requires Galleria 1.2.8 or later');

//        this.$("container").css("height", "+=60px");
//        $(".galleria-stage").css("bottom", "10px"); 
        this.$("thumbnails-container").remove();
        this.$("info").remove();
        this.$("tooltip").remove();

        // cache some stuff
        var touch = Galleria.TOUCH;

        // show loader & counter with opacity
        this.$('loader,counter').show().css('opacity', 0.4);

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
        });

        this.bind('loadfinish', function(e) {
            this.$('loader').fadeOut(200);
        });
    }
});

}(jQuery));
