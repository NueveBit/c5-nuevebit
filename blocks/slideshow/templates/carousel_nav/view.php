<?php
defined('C5_EXECUTE') or die("Access Denied.");
$nav = Loader::helper('navigation');
$class = "active";
?>

<div class="carousel slide" id="carousel<?= $bID ?>">

    <div class="carousel-inner">

        <?php
        foreach ($images as $imgInfo) {
            $f = File::getByID($imgInfo['fID']);
            ?>

            <div class="item <?= $class ?>">	

                <img src="<?= $f->getRelativePath() ?>"/>

            </div>	

            <?php $class = "";
        } ?>

    </div><!-- .powerSliderContainer -->

    <!--  Next and Previous controls below
          href values must reference the id for this carousel -->
    <a class="carousel-control left" href="#carousel<?=$bID?>" data-slide="prev">&lsaquo; PREV</a>
    <a class="carousel-control right" href="#carousel<?=$bID?>" data-slide="next">NEXT &rsaquo;</a>
</div><!-- #powerSliderShell  -->
