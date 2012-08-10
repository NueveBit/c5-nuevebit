<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$pages = $cArray;
?>

<div class="nb_sections">

	<?php foreach ($pages as $page):

        $title = substr($page->getCollectionPath(), 1);
		
     ?>

		<div id="<?=$title?>" class="nb_section">
            <?php 
			$GLOBALS["one_page_render"] = TRUE;

			$v = View::getInstance();
			$v->render($page);

			unset($GLOBALS["one_page_render"]);
            ?>
		</div>

	<?php endforeach; ?>
</div> 
