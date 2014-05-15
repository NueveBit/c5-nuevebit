<?php

$_trackingCodePosition = Config::get('SITE_TRACKING_CODE_POSITION');
if (empty($disableTrackingCode) && (empty($_trackingCodePosition) || $_trackingCodePosition === 'bottom')) {
    echo Config::get('SITE_TRACKING_CODE');
}
?>

<?php

$mh = Loader::helper("minify", "c5-nuevebit");

if (defined('MINIFY_ENABLE') && MINIFY_ENABLE) {
    $items = array_merge($this->getHeaderItems(), $this->getFooterItems());
    $printInline = TRUE;
} else {
    $items = $this->getFooterItems();
    $printInline = FALSE;
}

$mh->outputItems($items, "js");

if ($printInline) {
    // print inline items last
    foreach ($mh->inlineItems as $item) {
        print $item;
    }
}
//print $this->controller->outputFooterItems();
?>