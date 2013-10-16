var SlideshowBlock = {
    adding: false,
    init: function() {
   },
    chooseImg: function() {
        this.adding = true;
        ccm_launchFileManager('&fType=' + ccmi18n_filemanager.FTYPE_IMAGE);
    },
    showImages: function() {
        $("#ccm-slideshowBlock-imgRows").show();
        $("#ccm-slideshowBlock-chooseImg").show();
        $("#ccm-slideshowBlock-fsRow").hide();
    },
    showFileSet: function() {
        $("#ccm-slideshowBlock-imgRows").hide();
        $("#ccm-slideshowBlock-chooseImg").hide();
        $("#ccm-slideshowBlock-fsRow").show();
    },
    selectObj: function(obj) {
        if (obj.fsID != undefined) {
            $("#ccm-slideshowBlock-fsRow input[name=fsID]").attr("value", obj.fsID);
            $("#ccm-slideshowBlock-fsRow input[name=fsName]").attr("value", obj.fsName);
            $("#ccm-slideshowBlock-fsRow .ccm-slideshowBlock-fsName").text(obj.fsName);
        } else {
            this.addNewImage(obj.fID, obj.thumbnailLevel1, obj.height, obj.title);
        }
    },
    addImages: 0,
    addNewImage: function(fID, thumbPath, imgHeight, title) {
        this.addImages--; //negative counter - so it doesn't compete with real slideshowImgIds
        var slideshowImgId = this.addImages;
        var templateHTML = $('#imgRowTemplateWrap .ccm-slideshowBlock-imgRow').html().replace(/tempFID/g, fID);
        templateHTML = templateHTML.replace(/tempThumbPath/g, thumbPath);
        templateHTML = templateHTML.replace(/tempFilename/g, title);
        templateHTML = templateHTML.replace(/tempSlideshowImgId/g, slideshowImgId).replace(/tempHeight/g, imgHeight);
        var imgRow = document.createElement("div");
        imgRow.innerHTML = templateHTML;
        imgRow.id = 'ccm-slideshowBlock-imgRow' + parseInt(slideshowImgId);
        imgRow.className = 'ccm-slideshowBlock-imgRow';
        document.getElementById('ccm-slideshowBlock-imgRows').appendChild(imgRow);
        var bgRow = $('#ccm-slideshowBlock-imgRow' + parseInt(fID) + ' .backgroundRow');
        bgRow.css('background', 'url(' + escape(thumbPath) + ') no-repeat left top');
    },
    removeImage: function(fID) {
        $('#ccm-slideshowBlock-imgRow' + fID).remove();
    },
    moveUp: function(fID) {
        var thisImg = $('#ccm-slideshowBlock-imgRow' + fID);
        var qIDs = this.serialize();
        var previousQID = 0;
        for (var i = 0; i < qIDs.length; i++) {
            if (qIDs[i] == fID) {
                if (previousQID == 0)
                    break;
                thisImg.after($('#ccm-slideshowBlock-imgRow' + previousQID));
                break;
            }
            previousQID = qIDs[i];
        }
    },
    moveDown: function(fID) {
        var thisImg = $('#ccm-slideshowBlock-imgRow' + fID);
        var qIDs = this.serialize();
        var thisQIDfound = 0;
        for (var i = 0; i < qIDs.length; i++) {
            if (qIDs[i] == fID) {
                thisQIDfound = 1;
                continue;
            }
            if (thisQIDfound) {
                $('#ccm-slideshowBlock-imgRow' + qIDs[i]).after(thisImg);
                break;
            }
        }
    },
    serialize: function() {
        var t = document.getElementById("ccm-slideshowBlock-imgRows");
        var qIDs = [];
        for (var i = 0; i < t.childNodes.length; i++) {
            if (t.childNodes[i].className && t.childNodes[i].className.indexOf('ccm-slideshowBlock-imgRow') >= 0) {
                var qID = t.childNodes[i].id.replace('ccm-slideshowBlock-imgRow', '');
                qIDs.push(qID);
            }
        }
        return qIDs;
    },
    validate: function() {
        var failed = 0;

        if ($("#newImg select[name=type]").val() == 'FILESET')
        {
            if ($("#ccm-slideshowBlock-fsRow input[name=fsID]").val() <= 0) {
                alert(ccm_t('choose-fileset'));
                $('#ccm-slideshowBlock-AddImg').focus();
                failed = 1;
            }
        } else {
            qIDs = this.serialize();
            /*
             if( qIDs.length<1 ){
             alert(ccm_t('choose-min-2'));
             $('#ccm-slideshowBlock-AddImg').focus();
             failed=1;
             }	
             */
        }

        if (failed) {
            ccm_isBlockError = 1;
            return false;
        }
        return true;
    }
}

ccmValidateBlockForm = function() {
    return SlideshowBlock.validate();
}

ccm_chooseAsset = function(obj) { SlideshowBlock.selectObj(obj); }

$(function() {
    if ($("#newImg .select").val() == 'FILESET') {
        $("#newImg .select").val('FILESET');
        SlideshowBlock.showFileSet();
    } else {
        $("#newImg .select").val('CUSTOM');
        SlideshowBlock.showImages();
    }

    $("#newImg .select").change(function() {
        if (this.value == 'FILESET') {
            SlideshowBlock.showFileSet();
        } else {
            SlideshowBlock.showImages();
        }
    });

});



$(window).load(function() {
    // we need to override this atrocity
    ccm_alSelectFile = function(fID) {

        if (SlideshowBlock.adding || typeof(ccm_chooseAsset) == 'function') {
            var qstring = '';
            if (typeof(fID) == 'object') {
                for (i = 0; i < fID.length; i++) {
                    qstring += 'fID[]=' + fID[i] + '&';
                }
            } else {
                qstring += 'fID=' + fID;
            }

            $.getJSON(CCM_TOOLS_PATH + '/files/get_data.php?' + qstring, function(resp) {
                ccm_parseJSON(resp, function() {
                    for (i = 0; i < resp.length; i++) {
                        if (SlideshowBlock.adding) {
                            SlideshowBlock.selectObj(resp[i]);
                        } else {
                            ccm_chooseAsset(resp[i]);
                        }
                    }
                    jQuery.fn.dialog.closeTop();
                    SlideshowBlock.adding = false;
                });
            });

        } else {
            if (typeof(fID) == 'object') {
                for (i = 0; i < fID.length; i++) {
                    ccm_triggerSelectFile(fID[i]);
                }
            } else {
                ccm_triggerSelectFile(fID);
            }
            jQuery.fn.dialog.closeTop();
        }

    }
});
