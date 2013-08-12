/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// goofyBackground
// JavaScript Document
(function( $ ){
  var settings = {
    speedX:2,
    speedY:1,
    interval:31
  };
  
  var methods = {
    init:function(options){
      var pp = this;
      
      return this.each(function(){        
        var $this = $(this), 
          data = $this.data("goofyBackground");
        
        // If the plugin hasn't been initialized yet
        if ( ! data ) {
          $this.data("goofyBackground", {
            target : $this,
            bgPosX:0,
            bgPosY:0,
            animation:0
          });
          data = $this.data("goofyBackground");
        }
        
        $.extend(data, settings, options);
        
        $this.mouseenter(function(){
          $this.data("goofyBackground").animation = setInterval(function(){
            var posX = data.bgPosX;
            var posY = data.bgPosY;
            
            posX += data.speedX;
            posY += data.speedY;
            $this.css("background-position",posX+"px "+posY+"px");
            data.bgPosX = posX;
            data.bgPosY = posY;
          },data.interval);
        }).mouseleave(function(){
          
          clearInterval($this.data("goofyBackground").animation);
        });
      });
    }
  };
  
  $.fn.goofyBackground = function(method) {
    // Method calling logic
    if ( methods[method] ) {
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jquery.goofyBackground' );
    }
  };
})( jQuery );