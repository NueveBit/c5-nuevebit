/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// nuevebit namespace
var nuevebit = nuevebit || {};

nuevebit.GalleryManager = {
    _galleries: [],
    _loadedGalleries: [],
    
    addGallery: function(gallery, data) {
        this._galleries.push({
            gallery: gallery,
            data: data
        });
    },

    show: function(gallery, options) {
        var i = 0;
        var current = null;
        var galleryId = gallery.attr('id');

        /*
        if (!force && $.inArray(galleryId, this._loadedGalleries) != -1) {
            return;
        }
        */
       options = options || {};

        for (i = 0; i < this._galleries.length; i++) {
            current = this._galleries[i];

            if (current.gallery.is(gallery)) {
                Galleria.run(current.gallery, $.extend(options, {
                    dataSource: current.data
                }));

                this._loadedGalleries.push(galleryId);
//                console.log("gallery loaded: " + galleryId);
                
                break;
            }
        }
    }
}

nuevebit.FeedbackPanel = function(selector, timeout) {
    this._init(selector, timeout);
}

nuevebit.FeedbackPanel.prototype = {
    node: null,
    timeout: 5000,
    _showing: false,
    _timer: null,
    
    _init: function(selector, timeout) {
        this.node = $(selector);

        if (timeout) {
            this.timeout = timeout;
        }

        this._bind();
    },

    _bind: function() {
        var that = this;
        
        this.node.click(function() {
            that.hide();
        });
    },

    isVisible: function() {
        return this.node.is(":visible");
    },

    show: function() {
        if (this._timer) {
            clearTimeout(this._timer);
            this._timer = null;
        }
        
        var that = this;
        this._timer = setTimeout(function() {
            that.node.fadeOut();
            that._showing = false;
        }, this.timeout);

        if (!this._showing) {
            this.node.fadeIn();
            this._showing = true;
        } 
        
    }, 

    hide: function(timeout) {
        if (this._timer) {
            clearTimeout(this._timer);
            this._timer = null;
        }

        var that = this;

        if (timeout) {
            this._timer = setTimeout(function() {
                that.node.fadeOut();
                that._showing = false;
            }, timeout);
        } else {
            this.node.fadeOut();
            this._showing = false;
        }
        
    }
}