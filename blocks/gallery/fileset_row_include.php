<?php  defined('C5_EXECUTE') or die("Access Denied."); ?> 
<div id="file-sets-row-<?=$id?>" class="file-set-row" >
    <div class="row">
        <strong><?php echo t('File Set:')?></strong> 
        <span><?=$name?></span>
    </div>

    <div class="row">
        <?php
        $show = ($showThumbnail) ? "Sí" : "No";
        $d = date("m/Y", strtotime($date));
        ?>
        Mostrar miniatura: <?=$show?> Fecha: <?=$d?>
    </div>

    <input type="hidden" name="fileSets[]" value="<?=$id?>" />
    <input type="hidden" name="fileSetShowThumbnail_<?=$id?>" value="<?=$showThumbnail?>" />
    <input type="hidden" name="fileSetDate_<?=$id?>" value="<?=$date?>" />
</div>
