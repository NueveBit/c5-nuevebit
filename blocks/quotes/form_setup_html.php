<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div>
    <h2><?php echo t('Contenido de la cita') ?></h2>
    
    <textarea id="nb-quote-content" name="content" style="width:98%; height:200px;"><?php echo $content ?></textarea>
    
    <h2><?php echo t('Autor / Fuente de la cita') ?></h2>
    
    <textarea id="nb-quote-source" name="source" style="width:98%; height:100px;"><?php echo $source ?></textarea>
</div>
