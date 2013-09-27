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

                <img src="<?= $f->getRelativePath() ?>"/>

            </div>	

            <?php $class = "";
        } ?>

    </div>


</div>

<?php 
$duration = $duration * 1000;
?>

<script type="text/javascript">
    $(window).load(function() {
        $("#carousel<?= $bID ?>").carousel({
            interval: <?=$duration?>
        });
    });
</script>
