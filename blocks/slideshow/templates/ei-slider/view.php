<?php
defined('C5_EXECUTE') or die("Access Denied.");
$ih = Loader::helper("image");
?>

<div id="ei-slider-<?=$bID?>" class="ei-slider" >
    <ul class="ei-slider-large">
        <?php
        foreach ($images as $imgInfo) {
            $f = File::getByID($imgInfo['fID']);
            ?>
            <li>	
                <img src="<?= $f->getRelativePath() ?>" alt="<?=$f->getApprovedVersion()->getFileName()?>" />
            </li>	
            <?php
        }
        ?>
        <!-- <li>
            <img src="img/slider/4.jpg" alt="image04"/>
            <div class="ei-title"> <h2>Insecure</h2> <h3>Hussler</h3> </div>
        </li> -->
    </ul><!-- ei-slider-large -->

    <ul class="ei-slider-thumbs">
        <li class="ei-slider-element">Current</li>
        <?php
        foreach ($images as $imgInfo) {
            $f = File::getByID($imgInfo['fID']);
            ?>
            <li><a href="#">Slide <?=$f->getFileID()?></a><img src="<?=$ih->getThumbnail($f, 128, 64)->src?>" alt="thumbnail" /></li>
            <?php
        }
        ?>
    </ul><!-- ei-slider-thumbs -->

</div><!-- ei-slider -->

<?php 
$duration = $duration * 1000;
?>
<script type="text/javascript">
    $(function(){
        $('#ei-slider-<?=$bID?>').eislideshow({
            animation           : 'center',
            autoplay            : <?=($duration != 0) ? "true" : "false"?>,
            slideshow_interval  : <?=$duration?>,
            titlesFactor        : 0
        //thumbMaxWidth       : 100%
        });
    });
</script>