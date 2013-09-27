var nuevebit = nuevebit || {};

nuevebit.GalleryBlock = {
    _template: null,
    _fileSets: null,
    _container: null,
	
	init: function(){
        var source = $("#file-set-row-template").html();
        
        this._template = Handlebars.compile(source);
        this._fileSets = $("#file-sets-select");
        this._container = $("#file-sets-container");

        this._bind();
    },	

    _bind: function() {
        var addImageButton = $("#file-sets-add-button");
        var that = this;
        
        addImageButton.click(function() {
            var set = that._fileSets.find("option:selected");
            var showThumbnail = $("#file-sets-show-thumbnail").is(":checked");
            var date = new Date($("#file-sets-date").val()).getTime() / 1000;
            that._addFileSet(set.val(), set.html(), showThumbnail, date);
        });
    },

	chooseImg:function(){ 
		ccm_launchFileManager('&fType=' + ccmi18n_filemanager.FTYPE_IMAGE);
	}, 

    _addFileSet: function(id, name, showThumbnail, date) {
        var row = this._template({id: id, name: name, showThumbnail: showThumbnail, date: date});
        this._container.append(row);
    },
	
	selectObj:function(obj){
		if (obj.fsID != undefined) {
			$("#ccm-slideshowBlock-fsRow input[name=fsID]").attr("value", obj.fsID);
			$("#ccm-slideshowBlock-fsRow input[name=fsName]").attr("value", obj.fsName);
			$("#ccm-slideshowBlock-fsRow .ccm-slideshowBlock-fsName").text(obj.fsName);
		} else {
			this.addNewImage(obj.fID, obj.thumbnailLevel1, obj.height, obj.title);
		}
	},

	moveUp:function(fID){
		var thisImg=$('#ccm-slideshowBlock-imgRow'+fID);
		var qIDs=this.serialize();
		var previousQID=0;
		for(var i=0;i<qIDs.length;i++){
			if(qIDs[i]==fID){
				if(previousQID==0) break; 
				thisImg.after($('#ccm-slideshowBlock-imgRow'+previousQID));
				break;
			}
			previousQID=qIDs[i];
		}	 
	},
	moveDown:function(fID){
		var thisImg=$('#ccm-slideshowBlock-imgRow'+fID);
		var qIDs=this.serialize();
		var thisQIDfound=0;
		for(var i=0;i<qIDs.length;i++){
			if(qIDs[i]==fID){
				thisQIDfound=1;
				continue;
			}
			if(thisQIDfound){
				$('#ccm-slideshowBlock-imgRow'+qIDs[i]).after(thisImg);
				break;
			}
		} 
	},
    
	validate:function(){
        var selectedSet = this._fileSets.find("option:selected");
        
		if (selectedSet.val() > 0) {
            return true;
		} else {
            alert(ccm_t('choose-fileset'));
			ccm_isBlockError=1;

            return false;
		}
	} 
}

ccmValidateBlockForm = function() { return nuevebit.GalleryBlock.validate(); }
//ccm_chooseAsset = function(obj) { SlideshowBlock.selectObj(obj); }

$(function() {
//    nuevebit.GalleryBlock.init();
});

