<?php
defined('C5_EXECUTE') or die("Access Denied.");
//$nav = Loader::helper('navigation');
?>

<script type="text/javascript">
    $(function(){
        $("#bar-slides-<?=$bID?>").responsiveSlides({
              "auto": <?=($duration != 0) ? "true" : "false"?>,  
              //pager: true,           // Boolean: Show pager, true or false
              nav: true,             // Boolean: Show navigation, true or false
              //random: false,          // Boolean: Randomize the order of the slides, true or false
              prevText: "Prev",   // String: Text for the "previous" button
              nextText: "Sig",       // String: Text for the "next" button
              //maxwidth: "",           // Integer: Max-width of the slideshow, in pixels
              controls: ".bar-slides",           // Selector: Where controls should be appended to, default is after the 'ul'
              namespace: "bar-slides"   // String: change the default namespace used
        });
    });
</script>

<div id="bar-slides-<?=$bID?>" class="bar-slides">
    <?php
    foreach ($images as $imgInfo) {
        $f = File::getByID($imgInfo['fID']);
        ?>
        <figure>
            <img src="<?= $f->getRelativePath() ?>" alt="Location photography" />
        </figure>
    <?php } ?>
</div>