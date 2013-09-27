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

                <img src="<?= $f->getRelativePath() ?>" />

            </div>	

            <?php
            $class = "";
        }
        ?>

    </div><!-- .powerSliderContainer -->

    <div class="carousel-indicators">
        <?php
        $class = "active";
        for ($i = 0; $i < count($images); $i++) {
            ?>

            <button type="button" data-target="#carousel<?= $bID ?>" data-slide-to="<?= $i ?>" class="<?= $class ?>"></button>
            <?php
            $class = "";
        }
        ?>
    </div>
</div><!-- #powerSliderShell  -->
