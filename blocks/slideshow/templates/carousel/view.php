<?php   
defined('C5_EXECUTE') or die("Access Denied."); 
$nav = Loader::helper('navigation');
$class = "active";

?>

<script type="text/javascript">
$(function(){
	$("#carousel<?=$bID?>").carousel({
        interval: <?=$duration * 1000?>
    });

});
</script>
<style>
</style>

<div class="carousel slide" id="carousel<?=$bID?>">
	
    <div class="carousel-inner">
    
            <?php  foreach($images as $imgInfo) { 
            $f = File::getByID($imgInfo['fID']);
			?>
            
			<div class="item <?=$class?>">	
              
                <img src="<?=$f->getRelativePath()?>" style="width: 100%;"/>
              
			</div>	
        
            <?php $class="";  } ?>

	</div><!-- .powerSliderContainer -->
    

</div><!-- #powerSliderShell  -->
