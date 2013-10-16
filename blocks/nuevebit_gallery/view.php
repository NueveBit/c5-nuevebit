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

    $fileVersion = $file->getApprovedVersion();

    $title = $image["title"];
    $description = $image["description"];
    if ($description) {
        $description = str_replace("\n", "<br/>", $description);
        $description = str_replace("\"", "'", $description);
    }

    $thumb = $ih->getThumbnail($file, 180, 120)->src;

    if ($title) {
        $layer = "<div class='galleria-inner-layer'><header><h1>$title</h1></header><p>$description</p></div>";
    }
    ?>

                source = {
                    image: "<?= $file->getRelativePath() ?>",
                    thumb: "<?= $thumb ?>"
                }
    <?php if ($title): ?>
                    source.title = "<?=$title?>",
    <?php endif; ?>
    <?php if ($description): ?>
                    source.description = "<?=$description?>"
    <?php endif; ?>
                                
                data.push(source);
                        
    <?php
endforeach;
?>
        nuevebit.GalleryManager.addGallery($("#galleria<?= $bID ?>"), data);
    });
</script>

<div class="galleria" id="galleria<?= $bID ?>" >
    <!--
    Content will be loaded through javascript
    TODO: Usar <noscript/> para usuarios que no tengan javascript habilitado
    -->
</div>
