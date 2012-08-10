<?php   
defined('C5_EXECUTE') or die("Access Denied."); 

$html = Loader::helper("html");
$galleriaTheme = $html->javascript("themes/slideshow/galleria.slideshow.js", "nuevebit");
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
        image: "<?=$file->getRelativePath()?>"
    });

    <?php
    endforeach;
    ?>

    Galleria.loadTheme("<?=$galleriaTheme->href?>");
    Galleria.run("#galleria<?=$bID?>", {
        thumbnails: 'no',
        responsive:true, 
        height:1.0,
        dataSource: data
    });
});
</script>
<style>
</style>

<div class="" id="galleria<?=$bID?>">
    <!--
    Content will be loading through javascript
    -->
</div>
