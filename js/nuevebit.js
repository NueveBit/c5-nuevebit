/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// nuevebit namespace
var nuevebit = nuevebit || {};

nuevebit.GalleryManager = {
    _galleries: [],
    
    addGallery: function(gallery, data) {
        this._galleries.push({
            gallery: gallery,
            data: data
        });
    },

    start: function(gallery) {
        var i = 0;
        var current = null;

        for (i = 0; i < this._galleries.length; i++) {
            current = this._galleries[0];

            if (current.gallery.is(gallery)) {
                Galleria.run(current.gallery, {
                    dataSource: current.data
                });
                
                break;
            }
        }
    }
}