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
    
$(function() {
    
Galleria.addTheme({
    name: 'simple',
    author: 'Nuevebit',
    defaults: {
        transition: 'slide',
        thumbCrop:  'height',
        fullscreenDoubleTap: false
    },
    init: function(options) {

        Galleria.requires(1.28, 'This version requires Galleria 1.2.8 or later');

//        this.$("container").css("height", "+=60px");
//        $(".galleria-stage").css("bottom", "10px"); 
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

        // bind some stuff
        this.bind('thumbnail', function(e) {

            if (! touch ) {
                // fade thumbnails
                $(e.thumbTarget).css('opacity', 0.6).parent().hover(function() {
                    $(this).not('.active').children().stop().fadeTo(100, 1);
                }, function() {
                    $(this).not('.active').children().stop().fadeTo(400, 0.6);
                });

                if ( e.index === this.getIndex() ) {
                    $(e.thumbTarget).css('opacity',1);
                }
            } else {
                $(e.thumbTarget).css('opacity', this.getIndex() ? 1 : 0.6);
            }
        });

        this.bind('loadstart', function(e) {
            if (!e.cached) {
                this.$('loader').show().fadeTo(200, 0.4);
            }

            $(e.thumbTarget).css('opacity',1).parent().siblings().children().css('opacity', 0.6);
        });

        this.bind('loadfinish', function(e) {
            this.$('loader').fadeOut(200);
        });
    }
});
})

}(jQuery));
