<?php 

$_trackingCodePosition = Config::get('SITE_TRACKING_CODE_POSITION');
if (empty($disableTrackingCode) && (empty($_trackingCodePosition) || $_trackingCodePosition === 'bottom')) {
	echo Config::get('SITE_TRACKING_CODE');
}

?>

<?php

$mh = Loader::helper("minify", "nuevebit");

if (defined('MINIFY_ENABLE') && MINIFY_ENABLE) {
    $items = array_merge($this->getHeaderItems(), $this->getFooterItems());
} else {
    $items = $this->getFooterItems();
}

$mh->outputItems($items, "js");
//print $this->controller->outputFooterItems();

?>