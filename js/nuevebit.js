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

    show: function(gallery, force) {
        var i = 0;
        var current = null;
        var galleryId = gallery.attr('id');

        if (!force && $.inArray(galleryId, this._loadedGalleries) != -1) {
            return;
        }

        for (i = 0; i < this._galleries.length; i++) {
            current = this._galleries[i];

            if (current.gallery.is(gallery)) {
                Galleria.run(current.gallery, {
                    dataSource: current.data
                });

                this._loadedGalleries.push(galleryId);
//                console.log("gallery loaded: " + galleryId);
                
                break;
            }
        }
    }
}