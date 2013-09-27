<?php defined('C5_EXECUTE') or die("Access Denied."); ?> 

<style type="text/css">
    .properties {
        position: relative;
        width: 100%;
        margin-top: 15px;
    }
    
    .properties .field {
        margin-top: 4px;
        position: relative;
        width: 100%;
    }

    .properties .field > label {
        float: left;
        width: 25%;
        text-align: right;
        padding-right: 20px;
    }

    .properties .field > input, .properties .field textarea {
        float: left;
        width: 50%;
    }
</style>

<div id="ccm-slideshowBlock-imgRow<?php echo $imgInfo['slideshowImgId'] ?>" class="ccm-slideshowBlock-imgRow" >
    <div class="backgroundRow" style="background: url(<?php echo $imgInfo['thumbPath'] ?>) no-repeat left top; padding-left: 100px">
        <div class="cm-slideshowBlock-imgRowIcons" >
            <div style="float:right">
                <a onclick="SlideshowBlock.moveUp('<?php echo $imgInfo['slideshowImgId'] ?>')" class="moveUpLink"></a>
                <a onclick="SlideshowBlock.moveDown('<?php echo $imgInfo['slideshowImgId'] ?>')" class="moveDownLink"></a>									  
            </div>
            <div style="margin-top:4px"><a onclick="SlideshowBlock.removeImage('<?php echo $imgInfo['slideshowImgId'] ?>')"><img src="<?php echo ASSETS_URL_IMAGES ?>/icons/delete_small.png" /></a></div>
        </div>

        <strong><?php echo $imgInfo['fileName'] ?></strong><br/>

        <div class="properties">
            <div class="field clearfix">
                <label><?php echo t('Title') ?>:</label> 
                <input type="text" name="title[]" value="<?php echo $imgInfo['title'] ?>" style="vertical-align: middle; " />
            </div>
            
            <div class="field clearfix">
                <label><?php echo t('Description') ?>:</label> 
                <textarea name="description[]" style="vertical-align: middle; "><?php echo $imgInfo['description'] ?></textarea>
            </div>
            
            <div class="field clearfix">
                <label><?php echo t('Link URL') ?>:</label> 
                <input type="text" name="url[]" value="<?php echo $imgInfo['url'] ?>" style="vertical-align: middle; " />
            </div>
        </div>

        <input type="hidden" name="<?=$imgFIDs?>[]" value="<?php echo $imgInfo['fID'] ?>">
        <input type="hidden" name="imgHeight[]" value="<?php echo $imgInfo['imgHeight'] ?>">
    </div>
</div>
