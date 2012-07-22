<?php 
defined('C5_EXECUTE') or die("Access Denied.");

$pages = $cArray;
$th = Loader::helper('text');
?>

<div class="onepage-container">

	<?php foreach ($pages as $page):

		// Prepare data for each page being listed...
		$title = $th->sanitizeFileSystem($page->getCollectionName());
		$url = $nh->getLinkToCollection($page);
		$target = ($page->getCollectionPointerExternalLink() != '' && $page->openCollectionPointerExternalLinkInNewWindow()) ? '_blank' : $page->getAttribute('nav_target');
		$target = empty($target) ? '_self' : $target;

		$description = $page->getCollectionDescription();
		if ($controller->truncateSummaries) {
			$description = $th->shorten($description, $controller->truncateChars); //Concrete5.4.2.1 and lower
			//$description = $th->shortenTextWord($description, $controller->truncateChars); //Concrete5.4.2.2 and higher
		}
		$description = $th->entities($description);

		//Other useful page data...
		//$date = date('F j, Y', strtotime($page->getCollectionDatePublic()));
		//$author = Page::getByID($page->getCollectionID(), 1)->getVersionObject()->getVersionAuthorUserName();
		
		 ?>

		<div id="<?=$title?>" class="section">
            <?php 

			$GLOBALS["one_page_render"] = TRUE;

			$v = View::getInstance();
			$v->render($page);

			unset($GLOBALS["one_page_render"]);
            ?>
		</div>

	<?php endforeach; ?>
</div> 
