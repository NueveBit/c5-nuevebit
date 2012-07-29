<?php 

$_trackingCodePosition = Config::get('SITE_TRACKING_CODE_POSITION');
if (empty($disableTrackingCode) && (empty($_trackingCodePosition) || $_trackingCodePosition === 'bottom')) {
	echo Config::get('SITE_TRACKING_CODE');
}

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

<?php

$mh = Loader::helper("minify", "nuevebit");
$mh->outputItems($this->getFooterItems());
//print $this->controller->outputFooterItems();

?>