<?php   
defined('C5_EXECUTE') or die("Access Denied."); 

$galleriaThemeUri = "galleria/themes/nuevebit/galleria.simple.js";

$html = Loader::helper("html");
$galleriaTheme = $html->javascript($galleriaThemeUri, "nuevebit");

$ih = Loader::helper("image");
?>

<script type="text/javascript">
var galleriaLoaded = false;

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

    if (!galleriaLoaded) {
        Galleria.loadTheme("<?=$galleriaTheme->href?>");
        galleriaLoaded = true;
    }
    
    Galleria.run("#galleria<?=$bID?>", {
        dataSource: data
    });
});
</script>
<style>
</style>

<div class="galleria" id="galleria<?=$bID?>">
    <!--
    Content will be loaded through javascript
    -->
</div>
