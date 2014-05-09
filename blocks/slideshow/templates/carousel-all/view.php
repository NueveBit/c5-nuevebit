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
            $duration = $imgInfo["duration"];
            ?>

            <div class="item <?= $class ?>">	

                <?php if ($imgInfo["url"]) { ?>
                    <a href="<?= $imgInfo["url"]; ?>">
                    <?php } ?>
                    <img src="<?= $f->getRelativePath() ?>"/>
                    <?php if ($imgInfo["url"]) { ?>
                    </a>
                <?php } ?>

            </div>	

            <?php $class = "";
        }
        ?>

    </div>


</div>

<?php
$duration = $duration * 1000;
?>

<script type="text/javascript">
    $(window).load(function() {
        $("#carousel<?= $bID ?>").carousel({
            interval: <?= $duration ?>
        });
    });
</script>
