<?php   
defined('C5_EXECUTE') or die("Access Denied."); 

$ih = Loader::helper("image");
?>

<script type="text/javascript">
$(function(){
    // lazy load images in galleria
    var data = [];
    var source = null;
    <?php 
    foreach ($images as $image):
        $file = File::getByID($image['fID']);
    
        if ($dataLayer == 1) {
            $fileVersion = $file->getApprovedVersion();
            
            $title = $fileVersion->getTitle();
            $description = $fileVersion->getDescription();
            $description = str_replace("\n", "<br/>", $description);
            $description = str_replace("\"", "'", $description);
            
            $layer = "<div class='galleria-inner-layer'><h1>$title</h1><p>$description</p></div>";
        }
    ?>

    source = {
        image: "<?=$file->getRelativePath()?>",
        thumb: "<?=$ih->getThumbnail($file, 180, 120)->src?>"
    }
    <?php if ($dataLayer == 1): ?>
    source.layer = "<?=$layer?>"
    <?php endif;?>
            
    data.push(source);
    
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
    TODO: Usar <noscript/> para usuarios que no tengan javascript habilitado
    -->
</div>
