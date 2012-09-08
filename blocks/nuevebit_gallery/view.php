<?php   
defined('C5_EXECUTE') or die("Access Denied."); 

$ih = Loader::helper("image");
?>

<script type="text/javascript">
$(function(){
    // lazy load images in galleria
    var data = [];
    <?php 
    foreach ($images as $image):
        $file = File::getByID($image['fID']);
    ?>

    data.push({
        image: "<?=$file->getRelativePath()?>",
        thumb: "<?=$ih->getThumbnail($file, 180, 120)->src?>"
    });
    <?php
    endforeach;
    ?>

    <?php if ($lazyLoad != 1): ?>
    Galleria.run("#galleria<?=$bID?>", {
        dataSource: data
    });
    <?php else: ?>
        nuevebit.GalleryManager.addGallery($("#galleria<?=$bID?>"), data);
    <?php endif; ?>
});
</script>

<div class="galleria" id="galleria<?=$bID?>">
    <!--
    Content will be loaded through javascript
    -->
</div>
