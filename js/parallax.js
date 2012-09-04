
if ("undefined" == typeof nuevebit) {
    var nuevebit = {};
}

if ("undefined" == typeof nuevebit.Parallax) {
    nuevebit.Parallax = function() {
        this._init();
    }

    nuevebit.Parallax.Viewport = function(id, height, initialPosition, topOffset, speedFactor) {
        this._init(id, height, initialPosition, topOffset, speedFactor);
    }

    nuevebit.Parallax.Viewport.prototype = {
        
        _init: function(id, height, initialPosition, topOffset, speedFactor) {
            this.height = height;
            this.initialPosition = initialPosition;
            this.topOffset = topOffset;
            this.speedFactor = speedFactor;
            
            this._elements = [];
            this._id = id;
            this._count = 0;
        },

        add: function(initialPosition, speedFactor) {
            var id = "element" + this._count++;
            
            var element = {
                initialPosition: initialPosition,
                speedFactor: speedFactor,

                toString: function() {
                    return id;
                }
            };

            this._elements.push(element);
            return element;
        },

        toString: function() {
            return this._id;
        }
    }

    /*
    nuevebit.Parallax.Listener = function() {}

    nuevebit.Parallax.Listener.prototype = {
        
        viewportMoved: function(viewport, y) {
            // override
        },

        elementMoved: function(viewport, element, y) {
            // override
        }
    }
    */

    nuevebit.Parallax.prototype = {

        _init: function() {
            this._viewports = [];
            this._listeners = [];
            
            this._count = 0;
        },

        addListener: function(listener) {
            this._listeners.push(listener);
        },

        addViewport: function(height, initialPosition, topOffset, speedFactor) {
            var id = "viewport" + this._count++;
            var viewport = new nuevebit.Parallax.Viewport(id, height, initialPosition, topOffset, speedFactor);
            
            this._viewports.push(viewport);
            return viewport;
        },

        animate: function(windowHeight, currentPosition) {
            var viewport = null;
            var element = null;
            var listener = null;
            var y = 0;
            var i = 0, j = 0, k = 0;

            for (i = 0; i < this._viewports.length; i++) {
                viewport = this._viewports[i];

                // check if current viewport is inside of window's viewport
                if ((currentPosition + windowHeight) > viewport.topOffset &&
                    (viewport.topOffset + viewport.height) > currentPosition) {

                    // move the viewport's background if speedFactor > 0
                    y = this._getY(currentPosition, viewport.initialPosition, viewport.topOffset, viewport.speedFactor);

                    // notify registered listeners
                    for (j = 0; j < this._listeners.length; j++) {
                        listener = this._listeners[j];
                        listener.viewportMoved(viewport, y);
                    }

                    // move the inner elements contained within this parallax container
                    for (j = 0; j < viewport._elements.length; j++) {
                        element = viewport._elements[j];
                        y = this._getY(currentPosition, element.initialPosition, viewport.topOffset, element.speedFactor);

                        // notify registered listeners
                        for (k = 0; k < this._listeners.length; k++) {
                            listener = this._listeners[k];
                            listener.elementMoved(viewport, element, -1 * y);
                        }
                    }
                }
            }
        },

        _getY: function(currentPosition, initialPosition, topOffset, speedFactor) {
            return Math.round((topOffset - currentPosition) * speedFactor) - initialPosition;
        }

        
    }

}


(function($) {

    var parallax = null;
    var viewports = {};
    var $window = $(window);

    var listener = {
        viewportMoved: function(viewport, y) {
            if (viewport in viewports && viewport.speedFactor > 0) {
                var jqElement = viewports[viewport].element;
                var xpos = viewports[viewport].xpos;

                var coords = xpos + ' ' + y + 'px';
                jqElement.css('backgroundPosition', coords);
            }
        }, 

        elementMoved: function(viewport, element, y) {
            if (viewport in viewports && element in viewports[viewport].elements) {
                var jqElement = viewports[viewport].elements[element];

                jqElement.css('top', y);
            }
        }
    };

    var init = function() {
        parallax = new nuevebit.Parallax();
        parallax.addListener(listener);

        $window.scroll(function() {
            parallax.animate($window.height(), $window.scrollTop());
        }).resize(function() {
            parallax.animate($window.height(), $window.scrollTop());
        });
    };
    
    $.fn.parallax = function(speedFactor, xpos) {
        if (!parallax) {
            init();
        }
        
        if ("undefined" == typeof speedFactor) {
            speedFactor = 0.1;
        }

        if ("undefined" == typeof xpos) {
            xpos = "50%";
        }
        
        var ypos = this.css('backgroundPosition');

        if (ypos) {
            ypos = this.css('backgroundPosition').split(" ")[1];
            ypos = ypos.replace(/[^0-9-]/g, '')
        } else {
            ypos = 0;
        }

        var viewport = parallax.addViewport(this.height(), parseInt(ypos), this.offset().top, speedFactor);
        viewports[viewport] = {};
        
        viewports[viewport].element = this;
        viewports[viewport].xpos = xpos;
        viewports[viewport].elements = {};
        
        var jqueryViewport = {
            add: function(element, speedFactor) {
                var initialTop = element.css('top');
                initialTop = initialTop.replace(/[^0-9-]/g, '')
                var viewportElement = viewport.add(parseInt(initialTop), speedFactor);  
                
                viewports[viewport].elements[viewportElement] = element;
                parallax.animate($window.height(), $window.scrollTop());
                return this;
            }
        };

        parallax.animate($window.height(), $window.scrollTop());
        return jqueryViewport;
    }

    function getValue(total, percentage) {
        return (total * percentage) / 100;
    }

})(jQuery);
