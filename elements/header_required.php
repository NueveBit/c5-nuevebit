<?php  
defined('C5_EXECUTE') or die("Access Denied.");
global $c;
global $cp;
global $cvID;

if (is_object($c)) {
	$pageTitle = (!$pageTitle) ? $c->getCollectionName() : $pageTitle;
	$pageDescription = (!$pageDescription) ? $c->getCollectionDescription() : $pageDescription;
	$cID = $c->getCollectionID(); 
	$isEditMode = ($c->isEditMode()) ? "true" : "false";
	$isArrangeMode = ($c->isArrangeMode()) ? "true" : "false";
	
} else {
	$cID = 1;
}
?>

<meta http-equiv="content-type" content="text/html; charset=<?php  echo APP_CHARSET?>" />
<?php 
$akt = $c->getCollectionAttributeValue('meta_title'); 
$akd = $c->getCollectionAttributeValue('meta_description');
$akk = $c->getCollectionAttributeValue('meta_keywords');

if ($akt) { 
	$pageTitle = $akt; 
	?><title><?php  echo htmlspecialchars($akt, ENT_COMPAT, APP_CHARSET)?></title>
<?php  } else { 
	$pageTitle = htmlspecialchars($pageTitle, ENT_COMPAT, APP_CHARSET);
	?><title><?php  echo sprintf(PAGE_TITLE_FORMAT, SITE, $pageTitle)?></title>
<?php  } 

if ($akd) { ?>
<meta name="description" content="<?php echo htmlspecialchars($akd, ENT_COMPAT, APP_CHARSET)?>" />
<?php  } else { ?>
<meta name="description" content="<?php echo htmlspecialchars($pageDescription, ENT_COMPAT, APP_CHARSET)?>" />
<?php  }
if ($akk) { ?>
<meta name="keywords" content="<?php echo htmlspecialchars($akk, ENT_COMPAT, APP_CHARSET)?>" />
<?php  } 
if($c->getCollectionAttributeValue('exclude_search_index')) { ?>
    <meta name="robots" content="noindex" />
<?php  } ?>
<meta name="generator" content="concrete5 - <?php  echo APP_VERSION ?>" />

<?php  $u = new User(); ?>
<script type="text/javascript">
<?php 
	echo("var CCM_DISPATCHER_FILENAME = '" . DIR_REL . '/' . DISPATCHER_FILENAME . "';\r");
	echo("var CCM_CID = ".($cID?$cID:0).";\r");
	if (isset($isEditMode)) {
		echo("var CCM_EDIT_MODE = {$isEditMode};\r");
	}
	if (isset($isEditMode)) {
		echo("var CCM_ARRANGE_MODE = {$isArrangeMode};\r");
	}
?>
var CCM_IMAGE_PATH = "<?php  echo ASSETS_URL_IMAGES?>";
var CCM_TOOLS_PATH = "<?php  echo REL_DIR_FILES_TOOLS_REQUIRED?>";
var CCM_BASE_URL = "<?php  echo BASE_URL?>";
var CCM_REL = "<?php  echo DIR_REL?>";

</script>

<?php 
$assets = Loader::helper('assets', "nuevebit");

if ($u->isRegistered()) {
    $this->addHeaderItem($assets->css('ccm.base.css'), 'CORE');
    $this->addFooterItem($assets->javascript('ccm.base.js'), 'CORE');
}

$favIconFID=intval(Config::get('FAVICON_FID'));
$appleIconFID =intval(Config::get('IPHONE_HOME_SCREEN_THUMBNAIL_FID'));


if($favIconFID) {
	$f = File::getByID($favIconFID); ?>
	<link rel="shortcut icon" href="<?php  echo $f->getRelativePath()?>" type="image/x-icon" />
	<link rel="icon" href="<?php  echo $f->getRelativePath()?>" type="image/x-icon" />
<?php  } 

if($appleIconFID) {
	$f = File::getByID($appleIconFID); ?>
	<link rel="apple-touch-icon" href="<?php  echo $f->getRelativePath()?>"  />
<?php  } ?>

<?php  
if (is_object($cp)) { 

	if ($this->editingEnabled()) {
		Loader::element('page_controls_header', array('cp' => $cp, 'c' => $c));
	}

	if ($this->areLinksDisabled()) { 
		$this->addHeaderItem('<script type="text/javascript">window.onload = function() {ccm_disableLinks()}</script>', 'CORE');
	}
	$cih = Loader::helper('concrete/interface');
	if ($cih->showNewsflowOverlay()) {
		$this->addFooterItem('<script type="text/javascript">$(function() { ccm_showDashboardNewsflowWelcome(); });</script>');
	}	

}

$headerItems = $this->getHeaderItems();
$headerJsItems = $assets->getHeaderJsItems();
$mh = Loader::helper("minify", "nuevebit");
$minifyEnable = defined('MINIFY_ENABLE') && MINIFY_ENABLE;
$useCdn = defined('MINIFY_USE_CDN') && MINIFY_USE_CDN;

if ($minifyEnable):
    if ($useCdn):
?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

<?php
    else:
        $jqueryAsset = $assets->javascript("jquery.js", null, true);
    
        // include jquery before every other script
        array_unshift($headerJsItems, $jqueryAsset);
    endif;
else:
    echo $assets->javascript("jquery.js");
endif;

$mh->outputItems($headerItems, "css");

if (count($headerJsItems) > 1) {
    $mh->outputItems($headerJsItems, "js");
} else {
    // only jquery is to be included, we skip the minifier to speed things up
    echo $headerJsItems[0];
}

//$this->controller->outputHeaderItems();
$_trackingCodePosition = Config::get('SITE_TRACKING_CODE_POSITION');
if (empty($disableTrackingCode) && $_trackingCodePosition === 'top') {
	echo Config::get('SITE_TRACKING_CODE');
}
echo $c->getCollectionAttributeValue('header_extra_content');

?>