<?php 
defined('C5_EXECUTE') or die("Access Denied.");
$al = Loader::helper('concrete/asset_library');
$ah = Loader::helper('concrete/interface');

if (!isset($fsIdField)) {
    $fsIdField = "fsID";
}

if (!isset($typeField)) {
    $typeField = "type";
}

if (!isset($imgFIDs)) {
    $imgFIDs = "imgFIDs";
}

?>
<style type="text/css">
#ccm-slideshowBlock-imgRows a{cursor:pointer}
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow,
#ccm-slideshowBlock-fsRow {margin-bottom:16px;clear:both;padding:7px;background-color:#eee}
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveUpLink{ display:block; background:url(<?php  echo ASSETS_URL_IMAGES?>/icons/arrow_up.png) no-repeat center; height:10px; width:16px; }
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveDownLink{ display:block; background:url(<?php  echo ASSETS_URL_IMAGES?>/icons/arrow_down.png) no-repeat center; height:10px; width:16px; }
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveUpLink:hover{background:url(<?php  echo ASSETS_URL_IMAGES?>/icons/arrow_up_black.png) no-repeat center;}
#ccm-slideshowBlock-imgRows .ccm-slideshowBlock-imgRow a.moveDownLink:hover{background:url(<?php  echo ASSETS_URL_IMAGES?>/icons/arrow_down_black.png) no-repeat center;}
#ccm-slideshowBlock-imgRows .cm-slideshowBlock-imgRowIcons{ float:right; width:35px; text-align:left; }
</style>

<div id="newImg">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
	<td>
	<strong><?php echo t('Type')?></strong>
	<select class="select" name="<?=$typeField?>" style="vertical-align: middle">
		<option value="CUSTOM"<?php  if ($type == 'CUSTOM') { ?> selected<?php  } ?>><?php echo t('Custom Slideshow')?></option>
		<option value="FILESET"<?php  if ($type == 'FILESET') { ?> selected<?php  } ?>><?php echo t('Pictures from File Set')?></option>
	</select>
	</td>
	</tr>
	<tr style="padding-top: 8px">
	<td colspan="2">
	<br />
	<span id="ccm-slideshowBlock-chooseImg"><?php echo $ah->button_js(t('Add Image'), 'SlideshowBlock.chooseImg()', 'left');?></span>
	</td>
	</tr>
	</table>
</div>
<br/>

<div id="ccm-slideshowBlock-imgRows">
<?php  if ($fsID <= 0 && count($images) > 0) {
	foreach($images as $imgInfo){ 
		$f = File::getByID($imgInfo['fID']);
		$fp = new Permissions($f);
		$imgInfo['thumbPath'] = $f->getThumbnailSRC(1);
		$imgInfo['fileName'] = $f->getTitle();
		if ($fp->canRead()) { 
			$this->inc('image_row_include.php', array('imgInfo' => $imgInfo, "imgFIDs" => $imgFIDs));
		}
	}
} ?>
</div>

<?php 
Loader::model('file_set');
$s1 = FileSet::getMySets();
$sets = array();
foreach ($s1 as $s){
    $sets[$s->fsID] = $s->fsName;
}
$fsInfo['fileSets'] = $sets;

if ($fsID > 0) {
	$fsInfo['fsID'] = $fsID;
	$fsInfo['duration']=$duration;
	$fsInfo['fadeDuration']=$fadeDuration;
} else {
	$fsInfo['fsID']='0';
	$fsInfo['duration']=$defaultDuration;
	$fsInfo['fadeDuration']=$defaultFadeDuration;
}
$this->inc('fileset_row_include.php', array('fsInfo' => $fsInfo, "fsIdField" => $fsIdField)); ?> 

<div id="imgRowTemplateWrap" style="display:none">
<?php 
$imgInfo['slideshowImgId']='tempSlideshowImgId';
$imgInfo['fID']='tempFID';
$imgInfo['fileName']='tempFilename';
$imgInfo['origfileName']='tempOrigFilename';
$imgInfo['thumbPath']='tempThumbPath';
$imgInfo['duration']=$defaultDuration;
$imgInfo['fadeDuration']=$defaultFadeDuration;
$imgInfo['groupSet']=0;
$imgInfo['imgHeight']=tempHeight;
$imgInfo['url']='';
$imgInfo['class']='ccm-slideshowBlock-imgRow';
?>
<?php  $this->inc('image_row_include.php', array('imgInfo' => $imgInfo, "imgFIDs" => $imgFIDs)); ?> 
</div>
