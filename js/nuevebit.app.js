/* 
 * Main Application 'class'
 * 
 * Main entry point for interactive applications created by NueveBit.
 * 
 */

// nuevebit namespace
var nuevebit = nuevebit || {};

// application interface 
nuevebit.Application = function() {
}

nuevebit.Application.prototype = {
    width: 0,
    height: 0,
    showingMobile: false,
    showingPortrait: false,
    currentPosition: 0,
    touch: false,
    _loaded: false,
    _started: false,
    _disableScroll: false, // si no se utiliza, no es necesario usar $(window).scrollTop() cada vez

    _preloader: null,

    /**
     * Function called on document ready
     */
    load: function() {
        if (this._loaded) {
            return;
        }
        
        this._loaded = true;

        this._preloader = $("<div />");
        this._preloader.attr("id", "preloader");
        this._preloader.css({
            visibility: "hidden",
            position: "fixed",
            top: 0
        });

        if (typeof Modernizr != "undefined") {
            this.touch = Modernizr.touch;
        }

        this._init();

        this.resize();
        
        this.__bind();
    },

    resize: function() {
        this.width = $(window).width();
        this.height = $(window).height();
        
        this.showingMobile = this.width < 768;
        this.showingPortrait = this.width < this.height;

        this.currentPosition = $(window).scrollTop();
        
        this._resize();
    },

    scroll: function() {
        this.currentPosition = $(window).scrollTop();
        
        this._scroll();
    },

    _scrollSpy: function(sections, offset, callback) {
        var i = 0;
        var section = null;
        var topOffset = 0;

        for (i = 0; i < sections.length; i++) {
            section = $(sections[i]);
            topOffset = section.offset().top;

            if (!section.is(":visible")) {
                continue;
            }

            if (this.currentPosition + offset >= topOffset && 
                    this.currentPosition < topOffset + section.height() - offset) {
                
                callback(section);
                break;
            }
        }
    },

    __bind: function() {
        var that = this;

        $(window).load(function() {
            that.__start();
        });

        $(window).resize(function() {
            that.resize();
        });

        if (!this._disableScroll) {
            $(window).scroll(function() {
                that.scroll();
            });
        }

        this._bind();
    },

    __unbind: function() {
        $(window).unbind("resize");
        $(window).unbind("scroll");

        this._unbind();
    },

    // utility function to change window location appropiately (avoid jumping)
    _setWindowLocation: function(node) {
        var fx = null;
        var hash = null;
        
        if ( node.length ) {
            hash = node.attr('id');
            
            node.attr( 'id', '' );
            
            window.location.hash = hash;
            
            node.attr( 'id', hash );
        }
        
    },
    
    _getUrlParameters: function() {
        var vars = {}, hash;
        var encodedParams = window.location.href.slice(window.location.href.indexOf('?') + 1);
        encodedParams = encodedParams.slice(0, encodedParams.indexOf("#"));
        
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
            vars[key] = value;
        });
        
        return vars;
    },

    restart: function() {
        this.__unbind();
        this._loaded = false;
        this._started = false;
        
        this.load();
        this.__start();
    },

    __start: function() {
        if (this._started) {
            return;
        }

        this._started = true;
        this._start();
    },

    _start: function() {
        
    },

    _init: function() {
//        throw new Exception("Not implemented");
    },

    _resize: function() {
//        throw new Exception("Not implemented");
    },

    _scroll: function() {
        
    },

    _bind: function() {
//        throw new Exception("Not implemented");
    },

    _unbind: function() {
        
    }

}

